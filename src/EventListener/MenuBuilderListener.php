<?php
// src/EventListener/MenuBuilderListener.php

namespace App\EventListener;

use Sonata\AdminBundle\Event\ConfigureMenuEvent;

class MenuBuilderListener
{
    public function addMenuItems(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();

        /*$child = $menu->addChild('reports', [
            'label' => 'Daily and monthly reports',
            'route' => 'homepage',
        ])->setExtras([
            'icon' => '<i class="fa fa-bar-chart"></i>',
        ]);*/
		
		
		$menu["Documents"]->removeChild("Document");
		//dump($menu["Documents"]);
		$menu["Documents"]
			->addChild('FR', array(
				'route' => 'admin_app_document_list', 
				'routeParameters' => array(
					'filter[langue][value]' => 'FR',
					'filter[exercice][type]' => 1,
					'filter[exercice][value]' => 2019
				)
			)
		)/*->setExtras([
            'keep_open' => 'true',
			'icon' => '<i class="fa fa-flag"></i>',
        ])*/;
		$menu["Documents"]
			->addChild('EN', array(
				'route' => 'admin_app_document_list', 
				'routeParameters' => array(
					'filter[langue][value]' => 'EN',
					'filter[exercice][type]' => 1,
					'filter[exercice][value]' => 2019
				)
			)
		)/*->setExtras([
            'keep_open' => 'true',
			'icon' => '<i class="fa fa-flag"></i>',
        ])*/;
    }
}