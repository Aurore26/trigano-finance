<?php
// src/AppBundle/Admin/CategorieAdmin.php
namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Sonata\FormatterBundle\Form\Type\FormatterType; # pour sonata_formatter_type
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;

use App\Entity\OffreEmploi;

//http://developers-club.com/posts/143413/

class OffreEmploiAdmin extends AbstractAdmin
{

	protected function configureFormFields(FormMapper $formMapper)
    {
		
        $formMapper
			->tab('Document')
        		->with('Ref', [ 'class'=> 'col-md-6'])	
					->add('titre', TextType::class)	
					->add('ref', TextType::class, array('required' => false))	
					->add('contrat', TextType::class, array('required' => false))	
					->add('date', 		DateType::class)	
					->add('langue', 	EntityType::class, array('class' => 'App\Entity\Langue'))
					->add('localisation', TextType::class)	
					->add('destinataire', TextType::class)	
					->add('ReponseAuto',TextareaType::class)	
				->end()
				->with('Détail', [ 'class'=> 'col-md-6'])	
					/*->add('description',FormatterType::class, array(
							'event_dispatcher' => $formMapper->getFormBuilder()->getEventDispatcher(),
							'format_field'   => 'contentFormatter',
							'source_field'   => 'rawContent',
							'source_field_options'      => array(
								'attr' => array('class' => 'span10', 'rows' => 20)
							),
							'listener'       => true,
							'target_field'   => 'description',
							//'ckeditor_context'     => 'default',
							'required'=>false
						))	*/
			->add('description', SimpleFormatterType::class, array(
				'format' => 'richhtml',
				//'ckeditor_context' => 'default', // optional
			))
				->end()
			->end()
		;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('titre');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
			->addIdentifier('titre')
			->add('localisation')
			->add('date')
			->add('contrat')
        ;
    }
}