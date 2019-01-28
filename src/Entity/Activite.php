<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use App\Entity\Categorie;


/**
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="activite")
 * use repository for handy tree functions
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\NestedTreeRepository")
 *
 * >>>>> https://github.com/webdevilopers/DoctrineExtensions-1/blob/master/doc/tree.md
 */
class Activite extends Categorie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
	
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

}
