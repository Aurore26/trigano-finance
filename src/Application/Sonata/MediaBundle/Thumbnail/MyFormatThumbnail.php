<?php

namespace App\Application\Sonata\MediaBundle\Thumbnail;

use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Provider\MediaProviderInterface;

use Sonata\MediaBundle\Thumbnail\ThumbnailInterface;

use Cocur\Slugify\Slugify;

class MyFormatThumbnail implements ThumbnailInterface
{
    /**
     * @var string
     */
    private $defaultFormat;

    /**
     * @param string $defaultFormat
     */
    public function __construct($defaultFormat)
    {
        $this->defaultFormat = $defaultFormat;
    }

    /**
     * {@inheritdoc}
     */
    public function generatePublicUrl(MediaProviderInterface $provider, MediaInterface $media, $format)
    {
        if (MediaProviderInterface::FORMAT_REFERENCE === $format) {
            $path = $provider->getReferenceImage($media);
        } else {
           	//$path = sprintf('%s/thumbbbb_%s_%s.%s', $provider->generatePath($media), $media->getId(), $format, $this->getExtension($media));
			
			//thumb_37_image_small.png
			//avatar_small_37.png
			
			$temp = explode('.', $media->getName()); // avatar.png
			$slugify = new Slugify();
			$name = $slugify->slugify($temp[0]); // pour convertir les carac speciaux
			
			$path = sprintf('%s/%s_%s.%s', 
				$provider->generatePath($media),  
				$name, 
				str_replace($media->getContext() . '_', '', $format),
				$this->getExtension($media));
        }

        return $path;
    }

    /**
     * {@inheritdoc}
     */
    public function generatePrivateUrl(MediaProviderInterface $provider, MediaInterface $media, $format)
    {
        if (MediaProviderInterface::FORMAT_REFERENCE === $format) {
            return $provider->getReferenceImage($media);
        }

        /*return sprintf(
            '%s/thumb_%s_%s.%s',
            $provider->generatePath($media),
            $media->getId(),
            $format,
            $this->getExtension($media)
        );*/
		
			$temp = explode('.', $media->getName()); // avatar.png
			$slugify = new Slugify();
			$name = $slugify->slugify($temp[0]); // pour convertir les carac speciaux
			
			$path = sprintf('%s/%s_%s.%s', 
				$provider->generatePath($media),  
				$name, 
				str_replace($media->getContext() . '_', '', $format),
				$this->getExtension($media));
		
			return $path;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(MediaProviderInterface $provider, MediaInterface $media)
    {
        if (!$provider->requireThumbnails()) {
            return;
        }

        $referenceFile = $provider->getReferenceFile($media);

        if (!$referenceFile->exists()) {
            return;
        }

        foreach ($provider->getFormats() as $format => $settings) {
            if (substr($format, 0, strlen($media->getContext())) == $media->getContext() ||
                MediaProviderInterface::FORMAT_ADMIN === $format) {
                $provider->getResizer()->resize(
                    $media,
                    $referenceFile,
                    $provider->getFilesystem()->get($provider->generatePrivateUrl($media, $format), true),
                    $this->getExtension($media),
                    $settings
                );
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete(MediaProviderInterface $provider, MediaInterface $media, $formats = null)
    {
        if (is_null($formats)) {
            $formats = array_keys($provider->getFormats());
        } elseif (is_string($formats)) {
            $formats = array($formats);
        }

        if (!is_array($formats)) {
            throw new \InvalidArgumentException('"Formats" argument should be string or array');
        }

        foreach ($formats as $format) {
            $path = $provider->generatePrivateUrl($media, $format);
            if ($path && $provider->getFilesystem()->has($path)) {
                $provider->getFilesystem()->delete($path);
            }
        }
    }

    /**
     * @param MediaInterface $media
     *
     * @return string the file extension for the $media, or the $defaultExtension if not available
     */
    protected function getExtension(MediaInterface $media)
    {
        $ext = $media->getExtension();
        if (!is_string($ext) || strlen($ext) < 3) {
            $ext = $this->defaultFormat;
        }

        return $ext;
    }
}
