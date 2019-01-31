<?php


namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;


class MaintenanceListener
{
    private $maintenanceMode;
    private $maintenanceAuthorizedIp;
    private $templating;

    /**
     * MaintenanceListener constructor.
     * @param $maintenanceMode
     * @param $maintenanceAuthorizedIp
     * @param EngineInterface $templating
     */
    public function __construct($maintenanceMode, $maintenanceAuthorizedIp, EngineInterface $templating)
    {
        $this->maintenanceMode = $maintenanceMode;
        $this->maintenanceAuthorizedIp = $maintenanceAuthorizedIp;
        $this->templating = $templating;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($this->maintenanceMode == false)
        {
            return;
        }
        if (in_array($event->getRequest()->getClientIp(), $this->maintenanceAuthorizedIp))
        {
            
            dump("maintenance activée pour les autres IP");
            return;
        }

        $maintenanceView = $this->templating->render(
            'maintenance/index.html.twig'
        );
        /*return $this->render(
            'article/recent_list.html.twig',
            ['articles' => $articles]
        );
        $template = $engine->render('::maintenance.html.twig');
        
        */
        $response = new Response(
            $maintenanceView,
            Response::HTTP_OK,
            array('content-type' => 'text/html')
        );
        $event->setResponse(new Response($response->getContent()));
        $event->stopPropagation();
    }

}