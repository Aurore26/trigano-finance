<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\TranslatorInterface;

use App\Entity\Document;
use App\Entity\Page;
use App\Entity\DocumentType;

class DocumentController extends Controller
{
	/**
     * @Route("/{_locale}/document/{slug}", name="document")
     */
    public function index(Request $request, $slug, TranslatorInterface $translator)
    {		
		$locale = $request->getLocale();
		$document = $this->getDoctrine() 
			->getRepository(Document::class)
			->findOneBySlug($slug);
        
        // on ne trouve pas de doc, on declenche une erreur
        if (!$document) {
            throw new NotFoundHttpException($translator->trans('page.not_found'));
        }
		
		$PreviousDoc = $this->getDoctrine() 
			->getRepository(Document::class)
			->getPreviousDoc($document);
		
		$NextDoc = $this->getDoctrine() 
			->getRepository(Document::class)
			->getNextDoc($document);

		return $this->render('document/index.html.twig', [
			'document' => $document,
			'PreviousDoc' => $PreviousDoc,
			'NextDoc' => $NextDoc
		]);
    }
	
	public function lastCP(Request $request)
    {
       	$locale = $request->getLocale();
		$array_codes=array('cp ca', 'cp res', 'cp autre');
		$documents = $this->getDoctrine() 
            ->getRepository(Document::class)
            ->findLastDoc($array_codes, $locale, 3);
		
		return $this->render('default/container_last_cp.html.twig', [
            'documents' => $documents,
        ]);
    }
	
	public function LastCalendar(Request $request)
    {
       	$locale = $request->getLocale();
		$documents = $this->getDoctrine() 
            ->getRepository(Document::class)
            ->findBy(
			  array('langue' => $locale, 'inCalendar' => true, 'isPublished' => false), 
			  array ('date' => 'asc'),
			  $limit = 3//,
			  //$offset = null
			);
		
		return $this->render('default/container_calendar.html.twig', [
            'documents' => $documents,
        ]);
    }
	
	public function blockContact(Request $request)
    {
       	$locale = $request->getLocale();
		$page_contact = $this->getDoctrine() 
            ->getRepository(Page::class)
            ->findOneBy(array('slug' => 'Contact', 'langue' => $locale));
		
		return $this->render('default/container_contact.html.twig', [
            'page_contact' => $page_contact,
        ]);
    }
		
	/**
     * @Route("/{_locale}/documents/{exercice}", name="document_by_exercice", requirements={"exercice"="\d+"}))
	 * IMPORTANT !!! LAISSER allByExercice AVANT allByType
     */
	public function allByExercice(Request $request, $exercice)
    {
       	$locale = $request->getLocale();
		$documents = $this->getDoctrine() 
            ->getRepository(Document::class)
            ->findAllDocByExercice($exercice, $locale);		
        
		$exercice_verif = $this->getDoctrine() 
            ->getRepository(Document::class)
            ->findOneBy(array('exercice' => $exercice, 'langue' => $locale));	

        // on ne trouve pas l exercice, on declenche une erreur
        if (!$exercice_verif) {
            throw new NotFoundHttpException($translator->trans('page.not_found'));
        }
        
		return $this->render('document/tableau.html.twig', [
            'documents' => $documents,
			'sous_titre' => $exercice
        ]);
    }
	
	/**
     * @Route("/{_locale}/documents/{type_slug}", name="document_by_type")
     */
	public function allByType(Request $request, $type_slug)
    {
       	$locale = $request->getLocale();
		$documents = $this->getDoctrine() 
            ->getRepository(Document::class)
            ->findAllDocByType($type_slug, $locale);	
		$type = $this->getDoctrine() 
            ->getRepository(DocumentType::class)
            ->findOneBy(array('slug' => $type_slug, 'langue' => $locale));	
                
        // on ne trouve pas le type de doc, on declenche une erreur
        if (!$type) {
            throw new NotFoundHttpException($translator->trans('page.not_found'));
        }
        
		return $this->render('document/tableau.html.twig', [
            'documents' => $documents,
			'sous_titre' => $type
        ]);
    }
	
	public function slider(Request $request)
    {
       	$locale = $request->getLocale();
		$documents = $this->getDoctrine() 
            ->getRepository(Document::class)
            ->findBy(
				  array('inSlide' => true, 'langue' => $locale), // Critere
				  array ('date' => 'desc')//,
				  //$limit  = null,
				  //$offset = null
				);
		
		return $this->render('document/slider.html.twig', [
            'documents' => $documents,
        ]);
    }
}
