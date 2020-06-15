<?php

use CbaxAdcell\Models\AdcellEvent;

class Shopware_Controllers_Backend_CbaxAdcell extends Shopware_Controllers_Backend_Application
{
    protected $model = AdcellEvent::class;

    protected $alias = 'adcell_event';
}
