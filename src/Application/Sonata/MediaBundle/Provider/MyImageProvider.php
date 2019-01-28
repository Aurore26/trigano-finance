<?php

namespace App\Application\Sonata\MediaBundle\Provider;


use Sonata\MediaBundle\Provider\MediaProviderInterface;
use Gaufrette\Filesystem;
use Imagine\Image\ImagineInterface;
use Sonata\CoreBundle\Model\Metadata;
use Sonata\MediaBundle\CDN\CDNInterface;
use Sonata\MediaBundle\Generator\GeneratorInterface;
use Sonata\MediaBundle\Metadata\MetadataBuilderInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Thumbnail\ThumbnailInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use App\Application\Sonata\MediaBundle\Provider\MyFileProvider;

class MyImageProvider extends MyFileProvider
{
    /**
     * @var ImagineInterface
     */
    protected $imagineAdapter;

    /**
     * @param string                   $name
     * @param Filesystem               $filesystem
     * @param CDNInterface             $cdn
     * @param GeneratorInterface       $pathGenerator
     * @param ThumbnailInterface       $thumbnail
     * @param array                    $allowedExtensions
     * @param array                    $allowedMimeTypes
     * @param ImagineInterface         $adapter
     * @param MetadataBuilderInterface $metadata
     */
    public function __construct($name, Filesystem $filesystem, CDNInterface $cdn, GeneratorInterface $pathGenerator, ThumbnailInterface $thumbnail, array $allowedExtensions, array $allowedMimeTypes, ImagineInterface $adapter, MetadataBuilderInterface $metadata = null)
    {
        parent::__construct($name, $filesystem, $cdn, $pathGenerator, $thumbnail, $allowedExtensions, $allowedMimeTypes, $metadata);

        $this->imagineAdapter = $adapter;
    }

    /**
     * {@inheritdoc}
     */
    public function getProviderMetadata()
    {
        return new Metadata($this->getName(), $this->getName().'.description', false, 'SonataMediaBundle', array('class' => 'fa fa-picture-o'));
    }

    /**
     * {@inheritdoc}
     */
    public function getHelperProperties(MediaInterface $media, $format, $options = [])
    {
        if (MediaProviderInterface::FORMAT_REFERENCE === $format) {
            $box = $media->getBox();
        } else {
            $resizerFormat = $this->getFormat($format);
            if (false === $resizerFormat) {
                throw new \RuntimeException(sprintf('The image format "%s" is not defined.
                        Is the format registered in your ``sonata_media`` configuration?', $format));
            }

            $box = $this->resizer->getBox($media, $resizerFormat);
        }

        $mediaWidth = $box->getWidth();

        $params = [
            'alt' => $media->getName(),
            'title' => $media->getName(),
            'src' => $this->generatePublicUrl($media, $format),
            'width' => $mediaWidth,
            'height' => $box->getHeight(),
        ];

        if (MediaProviderInterface::FORMAT_ADMIN !== $format) {
            $srcSetFormats = $this->getFormats();

            if (isset($options['srcset']) && is_array($options['srcset'])) {
                $srcSetFormats = [];
                foreach ($options['srcset'] as $srcSetFormat) {
                    $formatName = $this->getFormatName($media, $srcSetFormat);
                    $srcSetFormats[$formatName] = $this->getFormat($formatName);
                }
                unset($options['srcset']);

                // Make sure the requested format is also in the srcSetFormats
                if (!isset($srcSetFormats[$format])) {
                    $srcSetFormats[$format] = $this->getFormat($format);
                }
            }

            if (!isset($options['srcset'])) {
                $srcSet = [];

                foreach ($srcSetFormats as $providerFormat => $settings) {
                    // Check if format belongs to the current media's context
                    if (0 === strpos($providerFormat, $media->getContext())) {
                        $width = $this->resizer->getBox($media, $settings)->getWidth();

                        $srcSet[] = sprintf('%s %dw', $this->generatePublicUrl($media, $providerFormat), $width);
                    }
                }

                // The reference format is not in the formats list
                $srcSet[] = sprintf(
                    '%s %dw',
                    $this->generatePublicUrl($media, MediaProviderInterface::FORMAT_REFERENCE),
                    $media->getBox()->getWidth()
                );

                $params['srcset'] = implode(', ', $srcSet);
            }

            $params['sizes'] = sprintf('(max-width: %1$dpx) 100vw, %1$dpx', $mediaWidth);
        }

        return array_merge($params, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getReferenceImage(MediaInterface $media)
    {
		return sprintf('%s/%s',
            $this->generatePath($media),
            $media->getProviderReference()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function updateMetadata(MediaInterface $media, $force = true)
    {
        try {
            if (!$media->getBinaryContent() instanceof \SplFileInfo) {
                // this is now optimized at all!!!
                $path = tempnam(sys_get_temp_dir(), 'sonata_update_metadata');
                $fileObject = new \SplFileObject($path, 'w');
                $fileObject->fwrite($this->getReferenceFile($media)->getContent());
            } else {
                $fileObject = $media->getBinaryContent();
            }

            $image = $this->imagineAdapter->open($fileObject->getPathname());
            $size = $image->getSize();

            $media->setSize($fileObject->getSize());
            $media->setWidth($size->getWidth());
            $media->setHeight($size->getHeight());
        } catch (\LogicException $e) {
            $media->setProviderStatus(MediaInterface::STATUS_ERROR);

            $media->setSize(0);
            $media->setWidth(0);
            $media->setHeight(0);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function generatePublicUrl(MediaInterface $media, $format)
    {
        if (MediaProviderInterface::FORMAT_REFERENCE === $format) {
            $path = $this->getReferenceImage($media);
        } else {
            $path = $this->thumbnail->generatePublicUrl($this, $media, $format);
        }

        return $this->getCdn()->getPath($path, $media->getCdnIsFlushable());
    }

    /**
     * {@inheritdoc}
     */
    public function generatePrivateUrl(MediaInterface $media, $format)
    {
        return $this->thumbnail->generatePrivateUrl($this, $media, $format);
    }

    /**
     * {@inheritdoc}
     */
    protected function doTransform(MediaInterface $media)
    {
        parent::doTransform($media);

        if ($media->getBinaryContent() instanceof UploadedFile) {
            $fileName = $media->getBinaryContent()->getClientOriginalName();
        } elseif ($media->getBinaryContent() instanceof File) {
            $fileName = $media->getBinaryContent()->getFilename();
        } else {
            // Should not happen, FileProvider should throw an exception in that case
            return;
        }

        if (!in_array(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)), $this->allowedExtensions)
            || !in_array($media->getBinaryContent()->getMimeType(), $this->allowedMimeTypes)) {
            return;
        }

        try {
            $image = $this->imagineAdapter->open($media->getBinaryContent()->getPathname());
        } catch (\RuntimeException $e) {
            $media->setProviderStatus(MediaInterface::STATUS_ERROR);

            return;
        }

        $size = $image->getSize();

        $media->setWidth($size->getWidth());
        $media->setHeight($size->getHeight());

        $media->setProviderStatus(MediaInterface::STATUS_OK);
    }
}
