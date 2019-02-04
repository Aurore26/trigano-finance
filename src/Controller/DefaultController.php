<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Page;

class DefaultController extends Controller
{
	
	 /**
     * @Route("/{_locale}/recherche", name="recherche")
     */
    public function search(Request $request, $_locale)
    {
		$pagination = '';		
		if ('GET' === $request->getMethod()){
			$search_field = $request->query->get('field-search');//$_GET parameters
			//$search_field = $request->request->get('field-search');	//$_POST parameters

			//$finder = $this->container->get('fos_elastica.finder.trigano_index.document');			
			$results2 = '';/*$finder->find($search_field, null, [
				'search_type' => 'dfs_query_then_fetch'
			]); */
			
			$query = new \Elastica\Query();

			$finder = $this->container->get('fos_elastica.finder.trigano_index.document');
			$boolQuery = new \Elastica\Query\BoolQuery();

			// on cherche les mots clés dans le titre
			$titreQuery = new \Elastica\Query\Match();
			$titreQuery->setFieldQuery('titre', $search_field);
			$titreQuery->setFieldParam('titre', 'analyzer', 'custom_french_analyzer');
			$boolQuery->addMust($titreQuery);

			// les doc doivent être publiés
			$isPublishedQuery = new \Elastica\Query\Match('isPublished', true);
			$boolQuery->addMust($isPublishedQuery);
            
            // les doc doivent être dans la langue
			$langueQuery = new \Elastica\Query\Match('langue', $locale);
			$boolQuery->addMust($langueQuery);   

			// priorité sur les doc récents
            // doc de moins de 6 moins
			$dateQuery = new \Elastica\Query\Range('date', [
				'boost' => 6,
				'gte'   => (new \DateTime('-6 months'))->format('c')
			]);            
			$boolQuery->addShould($dateQuery);
            // doc entre 6 mois et 1 an
            $dateQuery = new \Elastica\Query\Range('date', [
				'boost' => 4,
				'gte'   => (new \DateTime('-12 months'))->format('c'),
				'lte'   => (new \DateTime('-6 months'))->format('c')
			]);
            $boolQuery->addShould($dateQuery);
            // doc entre 1 an et 1 an et demi
            $dateQuery = new \Elastica\Query\Range('date', [
				'boost' => 3,
				'gte'   => (new \DateTime('-18 months'))->format('c'),
				'lte'   => (new \DateTime('-12 months'))->format('c')
			]);
			$boolQuery->addShould($dateQuery);

			$query->setQuery($boolQuery);


			// pagination			
			$paginator = $this->get('knp_paginator');
			$results = $finder->createPaginatorAdapter($query);
			$pagination = $paginator->paginate(
				$results,// target to paginate
				$request->query->getInt('page', 1),// page parameter, now section
				10, // limit per page
				array('pageParameterName' => 'page', 'sortDirectionParameterName' => 'dir')
			);

			$pagination->setUsedRoute('recherche');
			$pagination->setParam('field-search', $search_field);		

			
		}

		return $this->render('page/recherche.html.twig', [
			'search_field' => $search_field,
			'pagination' => $pagination,
			'results2' => $results2
		]);
    }
	
	
    /**
     * @Route("/{_locale}", requirements={"_locale" = "en|fr", "_scheme":  "https"}, defaults={"_locale" = "fr"}, name="homepage")
     */
    public function index($_locale)
    {
		return $this->render('default/index.html.twig');
    }
}
