<?php

namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UtilisateurRepository")
 * @ORM\Table(name="fos_user")
 */
class Utilisateur extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

     public function __construct()
    {
        parent::__construct();
        // your own logic
    }
}
