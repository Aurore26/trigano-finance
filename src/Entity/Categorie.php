<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Application\Sonata\MediaBundle\Entity\Media as Media;

/**
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="categorie")
 * use repository for handy tree functions
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\NestedTreeRepository")
 *
 * >>>>> https://github.com/webdevilopers/DoctrineExtensions-1/blob/master/doc/tree.md
 */
class Categorie
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(name="titre", type="string", length=64)
     */
    private $titre;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeRoot
     * @ORM\ManyToOne(targetEntity="Categorie")
     * @ORM\JoinColumn(name="tree_root", referencedColumnName="id", onDelete="CASCADE")
     */
    private $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Categorie", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Categorie", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;
	
	/**
     * @ORM\ManyToOne(targetEntity="Langue")
     */
    private $langue;
	
	/**
     * @ORM\OneToMany(targetEntity="Page", mappedBy="categorie")
     */
    private $pages;
	
	/**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="App\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"})
     */
    private $media;
	
	/**
     * @ORM\Column(name="media_sous_titre1", type="string", length=200, nullable=true)
     */
    private $MediaSousTitre1;
	
	/**
     * @ORM\Column(name="media_sous_titre2", type="string", length=200, nullable=true)
     */
    private $MediaSousTitre2;
	
	public function __toString()
    {
        $prefix = "";
        for ($i=1; $i<= $this->lvl; $i++){
            $prefix .= "__";
        }
        return $prefix . $this->titre;
		
		//return (string) $this->title;
    }
	
	public function getMonTitre()
    {
        $prefix = "";
        for ($i=1; $i<= $this->lvl; $i++){
            $prefix .= "__";
        }
        return $prefix . $this->titre;
		
		//return (string) $this->title;
    }
	
	public function getNbPages()
    {
        return sizeof($this->pages);
		
		//return (string) $this->title;
    }
	
	

    public function getId()
    {
        return $this->id;
    }

    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function getRoot()
    {
        return $this->root;
    }

    public function setParent(Categorie $parent = null)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }
	
    public function addChild(Categorie $child)
    {
        $this->children[] = $child;
        return $this;
    }

    public function removeChild(Categorie $child)
    {
        $this->children->removeElement($child);
    }
	
    public function getChildren()
    {
        return $this->children;
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

    public function addPage(Page $page)
    {
        $this->pages[] = $page;
        return $this;
    }

    public function removePage(Page $page)
    {
        $this->pages->removeElement($page);
    }

    public function getPages()
    {
        return $this->pages;
    }

    public function getLvl()
    {
        return $this->lvl;
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
	
	  public function setMediaSousTitre1($MediaSousTitre1)
    {
        $this->MediaSousTitre1 = $MediaSousTitre1;
    }

    public function getMediaSousTitre1()
    {
        return $this->MediaSousTitre1;
    }
	
	public function setMediaSousTitre2($MediaSousTitre2)
    {
        $this->MediaSousTitre2 = $MediaSousTitre2;
    }

    public function getMediaSousTitre2()
    {
        return $this->MediaSousTitre2;
    }
}