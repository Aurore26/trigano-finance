<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use App\Entity\Langue;
/**
 * @ORM\Entity(repositoryClass="App\Repository\OffreEmploiRepository")
 */
class OffreEmploi
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
     * @ORM\Column(name="contrat", type="string", length=255, nullable=true)
     */
    private $contrat;
	
	 /**
     * @var string
     *
     * @ORM\Column(name="localisation", type="string", length=255)
     */
    private $localisation;
	
	/**
     * @var string
     *
     * @ORM\Column(name="ref", type="string", length=255, nullable=true)
     */
    private $ref;
	
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
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=4000, nullable=true)
     */
    private $description;
	
	/**
     * @var date
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;
		
	 /**
     * @var string
     *
     * @ORM\Column(name="rawContent", type="string", length=4000, nullable=true)
     */
    private $rawContent;
	
	/**
     * @var string
     *
     * @ORM\Column(name="contentFormatter", type="string", length=4000, nullable=true)
     */
    private $contentFormatter;
	
	/**
     * @var string
     *
     * @ORM\Column(name="destinataire", type="string", length=255, nullable=true)
     */
    private $destinataire;
	
	/**
     * @var string
     *
     * @ORM\Column(name="reponse_auto", type="string", length=2000, nullable=true)
     */
    private $reponse_auto;
	
	public function __construct()
	{
		$this->date				= new \DateTime();
		$this->destinataire 	= 'amorel@trigano.fr';
		$this->reponse_auto 	= "Bonjour,<br><br>Nous accusons réception de votre candidature pour un poste au sein de notre groupe.<br>Sans contact de notre part dans le délai d'un mois, votre candidature ne pourra être prise en compte pour ce poste, mais sera intégrée dans notre banque de données. Nous vous remercions de la confiance que vous nous témoignez et de l'intérêt porté à notre entreprise.<br><br>Nous vous prions d'agréer l'expression de nos sentiments distingués.<br><br>Cordialement,<br>Les Ressources Humaines de TRIGANO";
	}
	
	 public function getId()
    {
        return $this->id;
    }
	
	public function __toString()
    {
        return (string) $this->titre;
    }

	public function getTitre(){
		return $this->titre;
	}

	public function setTitre($titre){
		$this->titre = $titre;
	}
	
	public function getContrat(){
		return $this->contrat;
	}

	public function setContrat($contrat){
		$this->contrat = $contrat;
	}
	
	public function getLocalisation(){
		return $this->localisation;
	}

	public function setLocalisation($localisation){
		$this->localisation = $localisation;
	}

	public function getRef(){
		return $this->ref;
	}

	public function setRef($ref){
		$this->ref = $ref;
	}

	public function getCreatedAt(){
		return $this->createdAt;
	}

	public function setCreatedAt($createdAt){
		$this->createdAt = $createdAt;
	}

	public function getUpdatedAt(){
		return $this->updatedAt;
	}

	public function setUpdatedAt($updatedAt){
		$this->updatedAt = $updatedAt;
	}

	public function getSlug(){
		return $this->slug;
	}

	public function setSlug($slug){
		$this->slug = $slug;
	}

	public function getLangue(){
		return $this->langue;
	}

	public function setLangue($langue){
		$this->langue = $langue;
	}

	public function getDescription(){
		return $this->description;
	}

	public function setDescription($description){
		$this->description = $description;
	}

	public function getDate(){
		return $this->date;
	}

	public function setDate($date){
		$this->date = $date;
	}

	public function getRawContent(){
		return $this->rawContent;
	}

	public function setRawContent($rawContent){
		$this->rawContent = $rawContent;
	}

	public function getContentFormatter(){
		return $this->contentFormatter;
	}

	public function setContentFormatter($contentFormatter){
		$this->contentFormatter = $contentFormatter;
	}

	public function getDestinataire(){
		return $this->destinataire;
	}

	public function setDestinataire($destinataire){
		$this->destinataire = $destinataire;
	}

	public function getReponseAuto(){
		return $this->reponse_auto;
	}

	public function setReponseAuto($reponse_auto){
		$this->reponse_auto = $reponse_auto;
	}
}
