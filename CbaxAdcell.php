<?php

namespace CbaxAdcell;

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
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall(UninstallContext $context)
    {
		if (!$context->keepUserData()) {
			return;
		}
		
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
}
