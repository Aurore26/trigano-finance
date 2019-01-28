<?php

// src/AppBundle/Admin/CategorieAdmin.php
namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
#use Symfony\Bridge\Doctrine\Form\Type\EntityType;
#use Symfony\Component\Form\Extension\Core\Type\TextareaType;
#use Sonata\FormatterBundle\Form\Type\FormatterType; # pour sonata_formatter_type
use Sonata\MediaBundle\Form\Type\MediaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
#use Symfony\Component\Form\Extension\Core\Type\DateType;
use Sonata\AdminBundle\Form\Type\ModelListType; #sonata_type_model_list

#use Sonata\CoreBundle\Form\Type\CollectionType;

class DocumentFileAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
		$formMapper
			->add('titre', 		TextType::class, array( 
				'required' => false 
			))
			->add('file', 		ModelListType::class, array( 
				'required' => false
			))
			->add('position', 	IntegerType::class, array( 
				'required' => true 
			));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('file');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('file')
			->add('position', 'text')
			//->add('media', null, array('template' => 'AppBundle:Admin:list_image.html.twig'))
			;
    }
}