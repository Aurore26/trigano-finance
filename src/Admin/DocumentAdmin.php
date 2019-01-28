<?php

// src/AppBundle/Admin/CategorieAdmin.php
namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
#use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Sonata\FormatterBundle\Form\Type\FormatterType; # pour sonata_formatter_type
use Sonata\MediaBundle\Form\Type\MediaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Sonata\CoreBundle\Form\Type\CollectionType;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use App\Entity\DocumentType;

class DocumentAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by'    => 'p.date'
    );
	
	protected function configureFormFields(FormMapper $formMapper)
    {
        $id='';
		$formMapper
			->tab('Document')
        		->with('Texte', [ 'class'=> 'col-md-6'])		
					->add('titre', TextType::class)
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
				->end()
        		->with('Paramètre', [ 'class'=> 'col-md-6'])
					->add('langue', 	EntityType::class, array('class' => 'App\Entity\Langue'))			
					->add('document_type', 	EntityType::class, array('class' => 'App\Entity\DocumentType'))
					->add('exercice', 	IntegerType::class)
					->add('date', 		DateType::class)
					->add('inSlide', 	CheckboxType::class, array('required'=>false))
					->add('inCalendar', CheckboxType::class, array('required'=>false))
					->add('isPublished', CheckboxType::class, array('required'=>false))
					//->add('slug', TextType::class)
				->end()
			->end()
			->tab('Médias')
        		->with('Image', [ 'class'=> 'col-md-12'])				
					/*->add('media', 		MediaType::class, array(
						 'provider' => 'sonata.media.provider.image',
						 'context'  => 'image'
					))*/
					->add('media', 		ModelListType::class, array(), array(
					'link_parameters' => array(
						'context' => 'document'
					)))
			
				->end()
        		->with('Fichiers', [ 'class'=> 'col-md-12'])
					->add('document_files', CollectionType::class, array(
							//'required' => false,
							//'by_reference' => false
						),
						array(
							'edit' => 'inline',
							'inline' => 'table',
							//'sortable'  => 'position',
							//'targetEntity'=>'AppBundle\Entity\DocumentFile',	
							'sortable' => 'position',
							'limit' => 5,
							'link_parameters' => array('context' => 'pdf'),
					))
				->end()
			->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
			->add('langue', 	null, ['show_filter' => true])
			->add('exercice', 	null, ['show_filter' => true])
			->add('document_type', 
				  null, 
				  array('show_filter' => true, 'label' => 'type'), 
				  EntityType::class, 
				  array(
						'class'    => 'App\Entity\DocumentType',
						'expanded' => false, 
						'multiple' => false
					))
			->add('titre', 		null, ['show_filter' => true])					
			->add('inSlide')
			->add('inCalendar')
			->add('isPublished');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
			->add('langue')
			->add('date', null, ['format' => 'd/m/Y'])
			->add('exercice')
			->add('documentType')
			->addIdentifier('titre')
			->add('media.name')
			->add('document_files')
			->add('inSlide', 'boolean', ["editable" => true])
			->add('inCalendar', 'boolean', ["editable" => true])
			->add('isPublished', 'boolean', ["editable" => true])
			;
    }
	
	public function toString($object)
    {
        return $object instanceof Document
            ? $object->getTitre()
            : 'Document'; // shown in the breadcrumb on the create view
    }
	
	public function prePersist($object)
	{
		foreach ($object->getDocumentFiles() as $media) {
			$media->setDocument($object);
		}
	}

	public function preUpdate($object)
	{
		foreach ($object->getDocumentFiles() as $media) {
			$media->setDocument($object);
		}
	}
}