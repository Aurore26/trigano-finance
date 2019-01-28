<?php
// src/AppBundle/Admin/CategorieAdmin.php
namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Sonata\MediaBundle\Form\Type\MediaType;

use App\Entity\Categorie;

//http://developers-club.com/posts/143413/

class CategorieAdmin extends AbstractAdmin
{	
	protected $datagridValues = array(
        '_sort_order' => 'ASC',
        '_sort_by'    => 'p.langue, p.root, p.lft'
    );
	
	public function createQuery($context = 'list')
    {
		$query = parent::createQuery();
		$alias = $query->getRootAliases()[0]; //'o'
		//$query->where($alias . '.parent IS NOT NULL');
		$query->orderBy($alias . '.langue, ' . $alias . '.root, ' . $alias . '.lft', 'ASC');
		return $query;
    }
	
	protected function configureFormFields(FormMapper $formMapper)
    {
		$subject = $this->getSubject();
        $id = $subject->getId();
		
        $formMapper
			->tab('Document')
        		->with('Texte', [ 'class'=> 'col-md-6'])	
					->add('titre', 		TextType::class)			
					->add('langue', 	EntityType::class, array('class' => 'App\Entity\Langue'))
					->add('parent', 	null, array(
					  // 'required'=>true,
					   'query_builder' => function($er) use ($id) {
							$qb = $er->createQueryBuilder('p');
							if ($id){
								$qb
									->where('p.id <> :id')
									->setParameter('id', $id);
							}
							$qb->orderBy('p.langue, p.root, p.lft', 'ASC');
							return $qb;
						},
						'required' => false
					))
				->end()
        		->with('Image Activité', [ 'class'=> 'col-md-6'])
					->add('media', 		MediaType::class, array(
						'provider' => 'sonata.media.provider.image',
						'context'  => 'menu'
					))			
					->add('media_sous_titre1', 		TextType::class, array('required' => false))
					->add('media_sous_titre2', 		TextType::class, array('required' => false))
			->end()
		;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
			->add('langue', 	null, ['show_filter' => true])
			->add('titre');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
			->add('up', 'text', array('template' => 'bundles/SonataAdminBundle/field_tree_up.html.twig', 'label'=>' '))
			->add('down', 'text', array('template' => 'bundles/SonataAdminBundle/field_tree_down.html.twig', 'label'=>' '))
			->addIdentifier('MonTitre')
			->add('getNbPages')
			->add('langue')
        ;
    }
	
	
	/*public function preRemove($object)
    {
        $em = $this->modelManager->getEntityManager($object);
        $repo = $em->getRepository("ShtumiPravBundle:Page");
        $subtree = $repo->childrenHierarchy($object);
        foreach ($subtree AS $el){
            $menus = $em->getRepository('ShtumiPravBundle:AdditionalMenu')
                        ->findBy(array('page'=> $el['id']));
            foreach ($menus AS $m){
                $em->remove($m);
            }
            $services = $em->getRepository('ShtumiPravBundle:Service')
                           ->findBy(array('page'=> $el['id']));
            foreach ($services AS $s){
                $em->remove($s);
            }
            $em->flush();
        }

        $repo->verify();
        $repo->recover();
        $em->flush();
    }*/

   /* public function postPersist($object)
    {
        $em = $this->modelManager->getEntityManager($object);
        $repo = $em->getRepository("ShtumiPravBundle:Page");
        $repo->verify();
        $repo->recover();
        $em->flush();
    }

    public function postUpdate($object)
    {
        $em = $this->modelManager->getEntityManager($object);
        $repo = $em->getRepository("ShtumiPravBundle:Page");
        $repo->verify();
        $repo->recover();
        $em->flush();
    }*/
}