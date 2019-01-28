<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LangueRepository")
 */
class Langue
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     */
    private $id;
	
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

    /**
     * Set id
     *
     * @param string $id
     *
     * @return Country
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
