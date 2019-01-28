<?php

// src/AppBundle/Admin/CategorieAdmin.php
namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
//use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Sonata\FormatterBundle\Form\Type\FormatterType; # pour sonata_formatter_type
use Sonata\AdminBundle\Form\Type\ModelListType;

class PageAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $id='';
		$formMapper
			->add('titre', TextType::class)
			//->add('slug', TextType::class)
			//->add('description',TextareaType::class)	
			->add('description',FormatterType::class, array(
				'event_dispatcher' => $formMapper->getFormBuilder()->getEventDispatcher(), //$formBuilder->getEventDispatcher(),
				'format_field'   => 'contentFormatter',
				/*'format_field_options' => array(
					'choices' => [
						'text' => 'Text',
						'markdown' => 'Markdown',
					],
					'data' => 'richhtml',
				), */
				'source_field'   => 'rawContent',
				'source_field_options'      => array(
					'attr' => array('class' => 'span10', 'rows' => 5)
				),
				'listener'       => true,
				'target_field'   => 'description',
				//'ckeditor_context'     => 'default',
				 'required'=>false
			))
			->add('categorie', null, array(
			  // 'required'=>true,
			   'query_builder' => function($er) use ($id) {
					$qb = $er->createQueryBuilder('p');
					$qb->orderBy('p.langue, p.root, p.lft', 'ASC');
					return $qb;
				},
				'required' => false
			))
			->add('langue', 	EntityType::class, array('class' => 'App\Entity\Langue'))
			->add('media', 		ModelListType::class, array(), array(
					'link_parameters' => array(
						'context' => 'page'
					)))
			;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
			->add('titre')
			->add('langue')
			->add('categorie');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('titre')
			->add('langue')
			->add('slug')
			->add('categorie')
			->add('media');
    }
}