<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use App\Application\Sonata\MediaBundle\Entity\Media as Media;

use App\Entity\DocumentFile as DocumentFile;

/**
 * Document
 *
 * @ORM\Table(name="document")
 * @ORM\Entity(repositoryClass="App\Repository\DocumentRepository")
 */
class Document
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

     /**
     * @ORM\Column(type="string", length=200)
     */
    private $titre;
	
	 /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=2000, nullable=true)
     */
    private $description;
	
	/**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="App\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"})
     */
    private $media;
	
	/**
     * @var date
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;
	
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
     * @Gedmo\Slug(fields={"titre"}, separator="-", updatable=true, unique=true)
     * @ORM\Column(length=128)
     */
    private $slug;
	
	/**
     * @ORM\ManyToOne(targetEntity="Langue")
     */
    private $langue;
	
	 /**
     * @var string
     *
     * @ORM\Column(name="rawContent", type="string", length=2000, nullable=true)
     */
    private $rawContent;
	
	/**
     * @var string
     *
     * @ORM\Column(name="contentFormatter", type="string", length=2000, nullable=true)
     */
    private $contentFormatter;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="exercice", type="integer")
     */
    private $exercice;
	
	/**
     * @ORM\ManyToOne(targetEntity="DocumentType")
     */
    private $document_type;
	
	/**
	* @var ArrayCollection DocumentFile
	*
	* @ORM\OneToMany(targetEntity="DocumentFile", mappedBy="document", cascade={"persist", "remove"}, orphanRemoval=true, fetch="LAZY" )
	* @ORM\JoinColumn(nullable=true)
	*/
	private $document_files;
	
	/**
	* @var boolean $inSlide
	*
	* @ORM\Column(name="inSlide", type="boolean")
	*/
	private $inSlide;
	
	/**
	* @var boolean $inCalendar
	*
	* @ORM\Column(name="inCalendar", type="boolean")
	*/
	private $inCalendar;
	
	/**
	* @var boolean $isPublished
	*
	* @ORM\Column(name="isPublished", type="boolean")
	*/
	private $isPublished;
	
	
	public function __toString()
    {
        return (string) $this->titre;
    }
	
	public function __construct()
	{
		$this->date				= new \DateTime();
		$this->exercice       	= date('m')<9 ? date('Y') : (date('Y')+1);
		$this->document_files 	= new \Doctrine\Common\Collections\ArrayCollection();
		$this->inSlide = false;
		$this->inCalendar = false;
		$this->isPublished = false;
	}
	
	public function removeAllDocumentFile()
    {
		$all = $this->document_files;
		foreach($all as $documentFile){
			/* rajout" aurore */
			$documentFile->setDocument(null);
			/**/
			$this->document_files->removeElement($documentFile);
		}
    }
	
	public function removeAllMedia()
    {
		$this->media = null;
    }
	
	
	
    public function getId()
    {
        return $this->id;
    }
	 
	public function getTitre()
    {
        return $this->titre;
    }

    public function setTitre($titre)
    {
        $this->titre = $titre;
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
	
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }

    /* public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }*/
	
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /* public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    } */

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

    public function setExercice($exercice)
    {
        $this->exercice = $exercice;

        return $this;
    }

    public function getExercice()
    {
        return $this->exercice;
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
	
	public function setDocumentType(DocumentType $document_type = null)
    {
        $this->document_type = $document_type;
        return $this;
    }

    public function getDocumentType()
    {
        return $this->document_type;
    }

    public function addDocumentFile(DocumentFile $documentFile)
    {
        $this->document_files[] = $documentFile;
        return $this;
    }
    public function removeDocumentFile(DocumentFile $documentFile)
    {
        $this->document_files->removeElement($documentFile);
    }
    public function getDocumentFiles()
    {
        return $this->document_files;
    }
	

	public function setInSlide($inSlide)
	{
		$this->inSlide = $inSlide;
	}
	public function getInSlide()
	{
		return $this->inSlide;
	}
	
	public function setInCalendar($inCalendar)
	{
		$this->inCalendar = $inCalendar;
	}
	public function getInCalendar()
	{
		return $this->inCalendar;
	}
	
	public function setIsPublished($isPublished)
	{
		$this->isPublished = $isPublished;
	}
	public function getIsPublished()
	{
		return $this->isPublished;
	}

}
