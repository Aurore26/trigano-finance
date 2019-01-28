<?php

// src/AppBundle/Admin/CategorieAdmin.php
namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Sonata\FormatterBundle\Form\Type\FormatterType; # pour sonata_formatter_type

class DocumentTypeAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $id='';
		$formMapper
			->add('titre', TextType::class)
			->add('langue', 	EntityType::class, array('class' => 'App\Entity\Langue'))			
			->add('code', TextType::class)
			;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
			->add('titre')
			->add('langue');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('titre')
			->add('langue')
			->add('code');
    }
	
	
	public function toString($object)
    {
        return $object instanceof DocumentType
            ? $object->getTitre()
            : 'Document Type'; // shown in the breadcrumb on the create view
    }
}