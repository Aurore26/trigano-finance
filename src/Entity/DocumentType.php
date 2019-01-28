<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DocumentTypeRepository")
 */
class DocumentType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    
    /**
     * @ORM\Column(type="string", length=100)
     */
    private $titre;
	
	/**
     * @ORM\ManyToOne(targetEntity="Langue")
     */
    private $langue;
	
	/**
     * @ORM\Column(type="string", length=20)
     */
    private $code;
	
	/**
     * @Gedmo\Slug(fields={"titre"}, separator="-", updatable=true, unique=true)
     * @ORM\Column(length=128)
     */
    private $slug;
	
	public function __toString()
    {
        return (string) $this->titre;
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
	
	public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;
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
	
	    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    public function getSlug()
    {
        return $this->slug;
    }
}
