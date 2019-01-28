<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure;

use App\Entity\Categorie;

class CategorieController extends Controller
{	
	public function menu(Request $request)
    {
		$locale = $request->getLocale();
		$categories = $this->getDoctrine() 
			->getRepository(Categorie::class)
			->findBy(
				array('lvl' => '1', 'langue' => $locale), // Critere
				array('lft' => 'asc') // Tri
			);

		return $this->render('categorie/menu.html.twig', [
			'categories' => $categories,
		]);
    }
	
	public function menuSticky(Request $request)
    {
		$locale = $request->getLocale();
		$categories = $this->getDoctrine() 
			->getRepository(Categorie::class)
			->findBy(
				array('lvl' => '1', 'langue' => $locale), // Critere
				array('lft' => 'asc') // Tri
			);

		return $this->render('categorie/menu_sticky.html.twig', [
			'categories' => $categories,
		]);
    }
	
	public function menuHidden(Request $request)
    {
		$locale = $request->getLocale();
		$categories = $this->getDoctrine() 
			->getRepository(Categorie::class)
			->findBy(
				array('lvl' => '1', 'langue' => $locale), // Critere
				array('lft' => 'asc') // Tri
			);

		return $this->render('categorie/menu_hidden.html.twig', [
			'categories' => $categories,
		]);
    }
	
	
	/******************************************************/
	/* BO */
	
	/**
     * @Route("/admin/categorie_tree_up/{categorie_id}", name="categorie_tree_up")
     */
    public function upAction($categorie_id, Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository(Categorie::class);
        $categorie = $repo->findOneById($categorie_id);
        if ($categorie->getParent()){
            $repo->moveUp($categorie);
        }
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/admin/categorie_tree_down/{categorie_id}", name="categorie_tree_down")
     */
    public function downAction($categorie_id, Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository(Categorie::class);
        $categorie = $repo->findOneById($categorie_id);
        if ($categorie->getParent()){
            $repo->moveDown($categorie);
        }
        return $this->redirect($request->headers->get('referer'));
    }
}
