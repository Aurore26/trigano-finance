<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TestAuroreRepository")
 */
class TestAurore
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
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
     * @ORM\Column(name="desc", type="string", length=255)
     */
    private $desc;
	
	public function __toString()
    {
        return (string) $this->id;
    }


    /**
     * Get id
     *
     * @return int
     */
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
	
		
	    public function setDesc($desc)
    {
        $this->desc = $desc;
        return $this;
    }

    public function getDesc()
    {
        return $this->desc;
    }
}
