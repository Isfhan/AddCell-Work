<?php

namespace CbaxAdcell;

use CbaxAdcell\Models\AdcellEvent;
use Doctrine\ORM\Tools\SchemaTool;
use Exception;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;

/**
 * Shopware-Plugin CbaxAdcell.
 */
class CbaxAdcell extends Plugin
{
    /**
     * {@inheritdoc}
     */
    public function install(InstallContext $context)
    {
        parent::install($context);
        $this->createTable();
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall(UninstallContext $context)
    {
        if (!$context->keepUserData()) {
            return;
        }

        $this->removeTable();
        $context->scheduleClearCache(UninstallContext::CACHE_LIST_ALL);
    }

    /**
     * {@inheritdoc}
     */
    public function activate(ActivateContext $context)
    {
        $context->scheduleClearCache(ActivateContext::CACHE_LIST_ALL);
    }

    /**
     * {@inheritdoc}
     */
    public function deactivate(DeactivateContext $context)
    {
        $context->scheduleClearCache(DeactivateContext::CACHE_LIST_ALL);
    }

    private function createTable()
    {
        $modelManager = $this->container->get('models');
        $tool = new SchemaTool($modelManager);

        $classes = $this->getClasses($modelManager);

        $tool->updateSchema($classes, true); // make sure to use the save mode

        // create a new attribute using the attribute crud service
        $service = $this->container->get('shopware_attribute.crud_service');
        try {
            $service->update(
                's_articles_attributes',
                'adcell_event',
                'single_selection',
                [
                    'displayInBackend' => true,
                    'label' => 'Adcell Event',
                    'helpText' => 'Associate an event for commission calculation',
                    'entity' => AdcellEvent::class,
                ],
                null,
                true
            );
        } catch (Exception $e) {
        }
        Shopware()->Models()->generateAttributeModels(['s_articles_attributes']);
        $metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
        $metaDataCache->deleteAll();
    }

    private function removeTable()
    {
        $modelManager = $this->container->get('models');
        $tool = new SchemaTool($modelManager);

        $classes = $this->getClasses($modelManager);

        $tool->dropSchema($classes);
        $service = $this->container->get('shopware_attribute.crud_service');
        try {
            $service->delete(
                's_articles_attributes',
                'adcell_event'
            );
        } catch (Exception $e) {
        }
        Shopware()->Models()->generateAttributeModels(['s_articles_attributes']);
        $metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
        $metaDataCache->deleteAll();
    }

    /**
     * @param ModelManager $modelManager
     *
     * @return array
     */
    private function getClasses(ModelManager $modelManager)
    {
        return [
            $modelManager->getClassMetadata(AdcellEvent::class)
        ];
    }
}
