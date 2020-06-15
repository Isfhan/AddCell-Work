<?php

namespace CbaxAdcell\Subscriber;

use Doctrine\Common\Collections\ArrayCollection;
use Enlight\Event\SubscriberInterface;
use Shopware\Components\Theme\LessDefinition;

class Theme implements SubscriberInterface
{
    /**
     * @var
     */
    private $pluginPath;

    /**
     * Theme constructor
     */
    public function __construct($pluginPath)
    {
		$this->pluginPath = $pluginPath;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Theme_Inheritance_Template_Directories_Collected' => 'onTemplateDirectoriesCollect'
        ];
    }

    /**
     * adds the plugin views directory to the shopware template directories.
     * needs to be done here, in order to overwrite the documents template.
     *
     * @param EventArgs $args
     */
    public function onTemplateDirectoriesCollect(\Enlight_Event_EventArgs $args)
    {
        $dirs = $args->getReturn();

        $dirs[] = $this->pluginPath . '/Resources/views/';

        $args->setReturn($dirs);
    }
}
