<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Translation\TranslatorInterface;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Entity\Page;
use App\Entity\Document;
use App\Entity\OffreEmploi;

class PageController extends Controller
{
	
	/**
     * @Route("/{_locale}/page/{slug}", name="page")
     */
    public function index(Request $request, $slug, \Swift_Mailer $mailer, TranslatorInterface $translator)
    {
		/***********/
		/* emploi */
		/***********/
		if($slug=='offres-demploi' or $slug=='job-opportunities'){
			$offres = $this->getDoctrine() 
				->getRepository(OffreEmploi::class)
				->findAll();
			
		   $page = $this->getDoctrine() 
				->getRepository(Page::class)
				->findOneBySlug($slug);

			return $this->render('offre_emploi/tableau.html.twig', [
				'offres' => $offres,
				'page' => $page
			]);
		   
		/***********/
		/* contact */
		/***********/
		}elseif($slug=='contact' or $slug=='contact-1'){
			$defaultData = array();
			$form = $this->createFormBuilder($defaultData)
				->add('name', TextType::class, array(
					'required' => true,
				 	//'label'  => $translator->trans('contact.name'),
                    'label'  => 'contact.name',
					/*'attr' => [
						'placeholder' => $translator->trans('contact.name') . '...'
					]*/
				))
				->add('email', EmailType::class, array(
					'required' => true,
				 	'label'  => 'contact.email'
				))
				->add('subject', ChoiceType::class, array(
					'required' => true,
				 	'label'  => 'contact.subject',
					'choices'  => array(
						'contact.subject.produit' => 'produit',
						'contact.subject.emploi' => 'emploi',
						'contact.subject.finance' => 'finance',
						'contact.subject.fournisseur' => 'fournisseur',
						'contact.subject.autre' => 'autre',
					),
				))
				->add('message', TextareaType::class, array(
					'required' => true,
				 	'label'  => 'contact.message'
				))				
				->add('file', FileType::class, array(
					'required' => false,
				 	'label'  => 'contact.file',
				))				
				->add('file2', FileType::class, array(
					'required' => false,
				 	'label'  => 'contact.file',
				))
				->add('send', SubmitType::class, array(
				 	'label'  => 'contact.send',
					'attr' => [
						'class' => 'submit-btn'
					]
				))
				->getForm();

			$form->handleRequest($request);
			$retour = '';

			if ($form->isSubmitted() && $form->isValid()) {
				$data = $form->getData();
				$file = $form['file']->getData();
				$file2 = $form['file2']->getData();
				if($file) $file->move('file', $file->getClientOriginalName());
				if($file2) $file2->move('file', $file2->getClientOriginalName());
				/**************************/
				$message2 = (new \Swift_Message('Hello Email Depuis PROD'))
					->setFrom('amorel@trigano.fr')
					->setTo('amorel@trigano.fr')
					//setCc
					->setBcc('amorel@trigano.fr')
					->setBody(
						$this->renderView(
							'emails/contact_trigano.html.twig',
								array('name' => $data['name'], 'email' => $data['email'], 'subject' => $data['subject'], 'message' => $data['message'])
							),
							'text/html'
					)
					->setReturnPath('contact@trigano.fr')
					;
				if($file) $message2->attach(\Swift_Attachment::fromPath('file/'.$file->getClientOriginalName()));
				if($file2) $message2->attach(\Swift_Attachment::fromPath('file/'.$file2->getClientOriginalName()));
				
				$retour = 'message envoyé !';

				try{
					$mailer->send($message2);
					if($file) {		
						$spool = $this->container->get('mailer')->getTransport()->getSpool();
						$transport = $this->container->get('swiftmailer.transport.real');
						if ($spool and $transport){
							$spool->flushQueue($transport);  
							unlink('file/'.$file->getClientOriginalName());
						}
					}
					if($file2) {		
						$spool = $this->container->get('mailer')->getTransport()->getSpool();
						$transport = $this->container->get('swiftmailer.transport.real');
						if ($spool and $transport){
							$spool->flushQueue($transport);  
							unlink('file/'.$file2->getClientOriginalName());
						}
					}
				}catch (Exception $e){
					printf("Error: %s\n", $e->getMessage());
					$retour =  $e->getMessage();
					$mailer->getTransport()->stop();
					sleep(10);
				}

				/**************************/
				
			}
			
			
		   $page = $this->getDoctrine() 
				->getRepository(Page::class)
				->findOneBySlug($slug);
			
			return $this->render('page/contact.html.twig', [
				'page' => $page,
				'form' => $form->createView(),
				'retour' => $retour
			]);
				
		/***********/
		/* agenda */
		/***********/
		}elseif($slug=='agenda' or $slug=='calendar'){
			$locale = $request->getLocale();
			$documents = $this->getDoctrine() 
				->getRepository(Document::class)
				->findBy(
				  array('langue' => $locale, 'inCalendar' => true, 'exercice' => $this->container->getParameter('exercice')), 
				  array ('date' => 'asc')
				);//exercice définit en constante dans services.yaml
		   
		   	$page = $this->getDoctrine() 
				->getRepository(Page::class)
				->findOneBySlug($slug);
		   
			return $this->render('document/agenda.html.twig', [
				'documents' => $documents,
				'page' => $page,
			]);
			
		/***********/
		/* operations-sur-le-titre */
		/***********/
		}elseif($slug=='operations-sur-le-titre'
			   or $slug=='autre-information-reglementee'){
			
			if($slug=='operations-sur-le-titre') 		$array_codes=array('ope vote', 'ope bilan','ope rachat');
			if($slug=='autre-information-reglementee')	$array_codes=array('ope autre');
			
			$locale = $request->getLocale();
			$exercice = $this->container->getParameter('exercice');//exercice définit en constante dans services.yaml
			$documents = $this->getDoctrine() 
				->getRepository(Document::class)
				->findLastDoc($array_codes,$locale,null,$exercice);

		   
		   	$page = $this->getDoctrine() 
				->getRepository(Page::class)
				->findOneBySlug($slug);
		   
			return $this->render('document/tableau.html.twig', [
				'documents' => $documents,
				'page' => $page,
				'sous_titre' => $page->getTitre() . ' ' . $exercice
			]);
		
		/***********/
		/* communiques-de-presse */
		/***********/
		}elseif($slug=='communiques-de-presse' or $slug=='rapports-annuels-semestriels' or 
			   $slug=='press-release' or $slug=='annual-report' or
			   $slug =='rapport-semestriel' or
			   $slug =='rapport-annuel' or $slug=='annual-report-1' or 
			   $slug =='communiques-financiers' or $slug=='financial-press-releases'){
			
		   	if($slug=='communiques-de-presse' or $slug=='press-release' or $slug =='communiques-financiers' or $slug=='financial-press-releases') 		$array_codes=array('cp ca', 'cp res', 'cp autre');
			if($slug=='rapports-annuels-semestriels' or $slug=='annual-report')	$array_codes=array('ra', 'rs');
			if($slug=='rapport-semestriel')			$array_codes=array('rs');
			if($slug=='rapport-annuel' or $slug=='annual-report-1')				$array_codes=array('ra');
			
			$locale = $request->getLocale();
			$document = $this->getDoctrine() 
				->getRepository(Document::class)
			    ->findOneLastDoc($array_codes, $locale);
		   
		   	$page = $this->getDoctrine() 
				->getRepository(Page::class)
				->findOneBySlug($slug);
		   
			$PreviousDoc = $this->getDoctrine() 
			->getRepository(Document::class)
			->getPreviousDoc($document);
		
			$NextDoc = $this->getDoctrine() 
				->getRepository(Document::class)
				->getNextDoc($document);

			return $this->render('document/index.html.twig', [
				'document' => $document,
				'page' => $page,
				'PreviousDoc' => $PreviousDoc,
				'NextDoc' => $NextDoc
			]);
		
		}else{
		   $page = $this->getDoctrine() 
				->getRepository(Page::class)
				->findOneBySlug($slug);
            
            // on ne trouve pas de page, on declenche une erreur
            if (!$page) {
                throw new NotFoundHttpException($translator->trans('page.not_found'));
            }
			return $this->render('page/index.html.twig', [
				'page' => $page,
			]);
		   
	   }
    }
}
