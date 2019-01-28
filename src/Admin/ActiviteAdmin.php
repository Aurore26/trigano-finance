<?php
// src/AppBundle/Admin/CategorieAdmin.php
namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use App\Entity\Activite;

//http://developers-club.com/posts/143413/

class ActiviteAdmin extends AbstractAdmin
{
   	/*protected $datagridValues = array(
        '_sort_order' => 'ASC',
        '_sort_by'    => 'p.langue, p.root, p.lft'
    );*/
	
	/*public function createQuery($context = 'list')
    {
		$query = parent::createQuery();
		$alias = $query->getRootAliases()[0]; //'o'
		//$query->where($alias . '.parent IS NOT NULL');
		$query->orderBy($alias . '.langue, ' . $alias . '.root, ' . $alias . '.lft', 'ASC');
		return $query;
    }*/
	
	protected function configureFormFields(FormMapper $formMapper)
    {
		$subject = $this->getSubject();
        $id = $subject->getId();
		
        $formMapper
			->add('titre', TextType::class)			
			->add('langue', 	EntityType::class, array('class' => 'App\Entity\Langue'))
			->add('parent', null, array(
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
		;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('titre');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
			/*->add('exist', null, [
				'template' => 'AdminBundle::field_tree_up.html.twig'
			])*/
			->addIdentifier('MonTitre')
			//->add('langue')
        ;
    }
}