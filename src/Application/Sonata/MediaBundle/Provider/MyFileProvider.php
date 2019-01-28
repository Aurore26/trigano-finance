<?php

namespace App\Application\Sonata\MediaBundle\Provider;

use Gaufrette\Filesystem;
use Sonata\MediaBundle\CDN\CDNInterface;
use Sonata\MediaBundle\Generator\GeneratorInterface;
use Sonata\MediaBundle\Thumbnail\ThumbnailInterface;
use Sonata\MediaBundle\Metadata\MetadataBuilderInterface;
use Sonata\MediaBundle\Provider\FileProvider;
use Sonata\MediaBundle\Model\MediaInterface;

class MyFileProvider extends FileProvider{

    /**
     * @param string                   $name
     * @param Filesystem               $filesystem
     * @param CDNInterface             $cdn
     * @param GeneratorInterface       $pathGenerator
     * @param ThumbnailInterface       $thumbnail
     * @param array                    $allowedExtensions
     * @param array                    $allowedMimeTypes
     * @param MetadataBuilderInterface $metadata
     */
	public function __construct($name, Filesystem $filesystem, CDNInterface $cdn, GeneratorInterface $pathGenerator, ThumbnailInterface $thumbnail, array $allowedExtensions = [], array $allowedMimeTypes = [], MetadataBuilderInterface $metadata = null)
	{
		parent::__construct($name, $filesystem, $cdn, $pathGenerator, $thumbnail);

		$this->allowedExtensions = $allowedExtensions;
		$this->allowedMimeTypes = $allowedMimeTypes;
		$this->metadata = $metadata;
	}
	
	protected function generateReferenceName(MediaInterface $media)
    {
        //return '**'.$this->generateMediaUniqId($media).'tttt.'.$media->getBinaryContent()->guessExtension();
		/* aurore */
		$metadata = $media->getProviderMetadata();
		$fileName = $metadata['filename'];
		$temp = explode('.', $fileName);
		$name = $temp[0];
		
		/*if($media->getContext()=='tissue' || $media->getContext()=='sheetmetal' || $media->getContext()=='furniture'){			
			return $name . '.' . $media->getBinaryContent()->guessExtension();
		}else{
			return $name . '_' . time() . '.' . $media->getBinaryContent()->guessExtension();
		}*/
			return $name . '.' . $media->getBinaryContent()->guessExtension();
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
	/* aurore */
	// permet de supprimer le sous repertoire 0001
	public function generatePath(MediaInterface $media)
    {
		/*$rep_first_level = (int) ($media->getId() / $this->firstLevel);
        $rep_second_level = (int) (($media->getId() - ($rep_first_level * $this->firstLevel)) / $this->secondLevel);

        return sprintf('%s/%04s/%02s', $media->getContext(), $rep_first_level + 1, $rep_second_level + 1);*/
		
		$created_at = $media->getCreatedAt();
		$year 			= date_format($created_at,'Y');
		$month 			= date_format($created_at,'m');
		$day 			= date_format($created_at,'d');
						   
		/*$created_at 	= $media->getDescription();
		$tab = explode("-", $created_at);
		$year 			= $tab[2];
		$month 			= $tab[1];
		$day 			= $tab[0];*/
		
		return sprintf('%s/%s/%s/%s', $media->getContext(), $year, $month, $day);
    }
}
