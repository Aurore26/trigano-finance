<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use App\Application\Sonata\MediaBundle\Entity\Media as Media;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PageRepository")
 */
class Page
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=255)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=6000, nullable=true)
     */
    private $description;
	
	 /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updatedAt;
	
    /**
     * @Gedmo\Slug(fields={"titre"},separator="-", updatable=true, unique=true)
     * @ORM\Column(length=255)
     */
    private $slug;
	
	/**
     * @ORM\ManyToOne(targetEntity="Langue")
     */
    private $langue;
	
	/**
     * @ORM\ManyToOne(targetEntity="Categorie", inversedBy="pages")
     */
    private $categorie;
	
	 /**
     * @var string
     *
     * @ORM\Column(name="rawContent", type="string", length=6000, nullable=true)
     */
    private $rawContent;
	
	/**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="App\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"})
     */
    private $media;
	
	/**
     * @var string
     *
     * @ORM\Column(name="contentFormatter", type="string", length=6000, nullable=true)
     */
    private $contentFormatter;
	
	public function __toString()
    {
        return (string) $this->titre;
    }




    public function getId()
    {
        return $this->id;
    }

    public function setTitre($titre)
    {
        $this->titre = $titre;
        return $this;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setLangue(Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
	
    public function getLangue()
    {
        return $this->langue;
    }

    public function setCategorie(Categorie $categorie = null)
    {
        $this->categorie = $categorie;

        return $this;
    }
	
    public function getCategorie()
    {
        return $this->categorie;
    }
	
    public function setRawContent($rawContent)
    {
        $this->rawContent = $rawContent;

        return $this;
    }

    public function getRawContent()
    {
        return $this->rawContent;
    }

    public function setContentFormatter($contentFormatter)
    {
        $this->contentFormatter = $contentFormatter;

        return $this;
    }

    public function getContentFormatter()
    {
        return $this->contentFormatter;
    }
	
	public function setMedia(Media $media = null)
    {
        $this->media = $media;
        return $this;
    }

    public function getMedia()
    {
        return $this->media;
    }

}
