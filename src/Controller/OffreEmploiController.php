<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\OffreEmploi;
use App\Entity\Page;

class OffreEmploiController extends Controller
{
    /**
     * @Route("/{_locale}/offre_emploi/{slug}", name="offre_emploi")
     */
    public function index(Request $request, $slug)
    {
        $locale = $request->getLocale();
		
		$offre = $this->getDoctrine() 
			->getRepository(OffreEmploi::class)
			->findOneBySlug($slug);
		
		$page = $this->getDoctrine() 
			->getRepository(Page::class)
			->findOneBySlug('offres-demploi');
		
		$page_contact = $this->getDoctrine() 
            ->getRepository(Page::class)
            ->findOneBy(array('slug' => 'Contact', 'langue' => $locale));

		return $this->render('offre_emploi/index.html.twig', [
			'offre' => $offre,
			'page' => $page,
			'page_contact' => $page_contact
		]);
    }
	
	public function container(Request $request)
    {
        $locale = 'fr';//$request->getLocale();		
		$offres = $this->getDoctrine() 
			->getRepository(OffreEmploi::class)
			->findBy(
				  array('langue' => $locale), 
				  array ('date' => 'desc', 'localisation' => 'asc')
			);
		
		$criteres = $this->getDoctrine() 
			->getRepository(OffreEmploi::class)
			->findTopLocalisation($locale);
		
		return $this->render('offre_emploi/container.html.twig', [
			'offres' => $offres,
			'criteres' => $criteres
		]);
    }
	
	public function sub_container(Request $request, $localisation = null )
    {
        $locale = 'fr';//$request->getLocale();	
		if($localisation){
			$param = array('langue' => $locale, 'localisation' => $localisation);
		}else{
			$param = array('langue' => $locale);			
		}
		$offres = $this->getDoctrine() 
			->getRepository(OffreEmploi::class)
			->findBy(
				  $param, 
				  array ('date' => 'desc', 'localisation' => 'asc')
			);
		
		return $this->render('offre_emploi/sub_container.html.twig', [
			'offres' => $offres
		]);
    }
}
