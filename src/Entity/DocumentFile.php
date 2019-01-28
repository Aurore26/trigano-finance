<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use App\Application\Sonata\MediaBundle\Entity\Media as Media;
/**
 * DocumentFile
 *
 * @ORM\Table(name="document_file")
 * @ORM\Entity(repositoryClass="App\Repository\DocumentFileRepository")
 */
class DocumentFile
{
/**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    private $position;
	
	/**
     * @var File
     *
     * @ORM\ManyToOne(targetEntity="App\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"})
     */
    private $file;
	
	/**
     * @var File
     *
     * @ORM\ManyToOne(targetEntity="Document", inversedBy="document_files")
     */
    private $document;
	
	/**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $titre;
	
	public function __toString()
    {
        return (string) $this->file;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return DocumentFile
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set file
     *
     * @param App\Application\Sonata\MediaBundle\Entity\Media $file
     *
     * @return DocumentFile
     */
    public function setFile(Media $file = null)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return App\Application\Sonata\MediaBundle\Entity\Media
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set document
     *
     * @param App\Entity\Document $document
     *
     * @return DocumentFile
     */
    public function setDocument($document = null)
    {
        $this->document = $document;
        return $this;
    }

    /**
     * Get document
     *
     * @return App\Entity\Document
     */
    public function getDocument()
    {
        return $this->document;
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
}
