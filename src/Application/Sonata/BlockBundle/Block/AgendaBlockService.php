<?php
// src/AdminBundle/Block 
namespace App\Application\Sonata\BlockBundle\Block;
 
use Sonata\BlockBundle\Block\BlockContextInterface; 
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface; 
use Sonata\AdminBundle\Form\FormMapper; 
use Sonata\CoreBundle\Validator\ErrorElement; 
use Sonata\AdminBundle\Admin\Pool; 
use Sonata\BlockBundle\Model\BlockInterface; 
use Sonata\BlockBundle\Block\BaseBlockService; 
use Symfony\Component\Security\Core\SecurityContext; 
use Symfony\Component\OptionsResolver\OptionsResolverInterface; 
use Doctrine\ORM\EntityManager; 
use App\Entity\Document;
 
class AgendaBlockService extends BaseBlockService 
{ 
    /** * @var SecurityContextInterface */
    protected $securityContext; 
 
    /** * @var EntityManager */
    protected $em; 
 
    public function __construct(
      $name, 
      EngineInterface $templating, 
      EntityManager $em) { 
        parent::__construct($name, $templating); 
        //$this->pool = $pool;
        $this->em = $em;
        //$this->securityContext = $securityContext;
    }
 
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'My Agenda';
    }
 
 
    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
     $agenda_fr = $this->em->getRepository(Document::class)->findBy(
			  array('langue' => 'FR', 'inCalendar' => true, 'isPublished' => false), 
			  array ('date' => 'asc'),
			  $limit = 3//,
			  //$offset = null
			);  
	 $agenda_en = $this->em->getRepository(Document::class)->findBy(
			  array('langue' => 'EN', 'inCalendar' => true, 'isPublished' => false), 
			  array ('date' => 'asc'),
			  $limit = 3//,
			  //$offset = null
			);  
		
	$slide_fr = $this->em->getRepository(Document::class)->findBy(
		array('inSlide' => true, 'langue' => 'FR'), 
		array ('date' => 'desc')
	);
	$slide_en = $this->em->getRepository(Document::class)->findBy(
		array('inSlide' => true, 'langue' => 'EN'), 
		array ('date' => 'desc')
	);
 
        // merge settings
        //$settings = array_merge($this->getDefaultSettings(), $blockContext->getSettings());
 
       return $this->renderResponse('bundles/SonataAdminBundle/AgendaInfoDashboard.html.twig', array(
            'block'     => $blockContext->getBlock(),
            //'settings'  => $settings
		   'agenda_fr' => $agenda_fr,
		   'agenda_en' => $agenda_en,
		   'slide_fr' => $slide_fr,
		   'slide_en' => $slide_en
            ), $response); 
    }
    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'title'    => 'Mes informations',
            'template' => 'bundles/SonataAdminBundle/AgendaInfoDashboard.html.twig' // Le template à render dans execute()
        ));
    }
 
    public function getDefaultSettings()
    {
        return array();
    }
 
    /**
     * {@inheritdoc}
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block) {}
 
    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block) {}
}