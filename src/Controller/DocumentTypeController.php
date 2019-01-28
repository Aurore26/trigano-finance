<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\DocumentType;
use App\Entity\Document;

class DocumentTypeController extends Controller
{
	
	public function blockDocType(Request $request)
    {
		$locale = $request->getLocale();
		$types = $this->getDoctrine() 
			->getRepository(DocumentType::class)
			->findBy(array('langue' => $locale));
		$documents = $this->getDoctrine() 
            ->getRepository(Document::class)
            ->findDistinctYear($locale);

		return $this->render('document_type/block.html.twig', [
			'types' => $types,
			'documents' => $documents
		]);
    }
}
