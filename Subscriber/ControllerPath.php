<?php

namespace CbaxAdcell\Subscriber;

use Enlight\Event\SubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ControllerPath implements SubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_CbaxAdcell' => 'onGetControllerPathBackend',
        ];
    }

    /**
     * Register the backend controller
     *
     * @param \Enlight_Event_EventArgs $args
     *
     * @return  string
     * @Enlight\Event Enlight_Controller_Dispatcher_ControllerPath_Backend_CbaxAdcell
     */
    public function onGetControllerPathBackend(\Enlight_Event_EventArgs $args)
    {
        $this->container->get('template')->addTemplateDir(__DIR__ . '/../Resources/views/');

        return __DIR__ . '/../Controllers/Backend/CbaxAdcell.php';
    }
}
