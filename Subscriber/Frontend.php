<?php

namespace CbaxAdcell\Subscriber;

use CbaxAdcell\Models\AdcellEvent;
use Enlight\Event\SubscriberInterface;
use Enlight_Template_Manager;
use Shopware\Components\Plugin\ConfigReader;

class Frontend implements SubscriberInterface
{
    /**
     * @var
     */
    private $config;

    /**
     * @var Enlight_Template_Manager
     */
    private $templateManager;

    private $pluginDirectory;

    public function __construct(
        Enlight_Template_Manager $templateManager,
        $pluginName,
        $pluginDirectory,
        ConfigReader $configReader
    ) {
        $this->templateManager = $templateManager;
        $this->pluginDirectory = $pluginDirectory;
        $this->config = $configReader->getByPluginName($pluginName);
    }

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PreDispatch' => 'onPreDispatch',
            'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'onPostDispatch'
        ];
    }

    public function onPreDispatch()
    {
        $this->templateManager->addTemplateDir($this->pluginDirectory . '/Resources/views');
    }

    public function onPostDispatch(\Enlight_Event_EventArgs $args)
    {
        if (!$this->config['active']) {
            return;
        }

        /** @var \Enlight_Controller_Action $controller */
        $controller = $args->getSubject();
        $view = $controller->View();
        $request = $controller->Request();

        $controllerName = strtolower($request->getControllerName());

        $view->AdcellTracking = $this->config['active'];

        // Adcell Remarketing Code
        if (!empty($this->config['pid'])) {
            if ($controllerName == 'index') {
                $adcell['typ'] = 'start';
                $adcell['retargeting'] = $this->config['retargeting'];
                $adcell['pid'] = $this->config['pid'];

                $view->Adcell = $adcell;
            } elseif ($controllerName == 'listing') {
                $adcell['typ'] = 'category';
                $adcell['retargeting'] = $this->config['retargeting'];
                $adcell['pid'] = $this->config['pid'];
                $adcell['categoryName'] = $view->sCategoryContent['name'];
                $adcell['categoryId'] = $view->sCategoryContent['id'];
                $adcell['productIds'] = self::getArticle($view->sArticles);

                $view->Adcell = $adcell;
            } elseif ($controllerName == 'search') {
                $adcell['typ'] = 'search';
                $adcell['retargeting'] = $this->config['retargeting'];
                $adcell['pid'] = $this->config['pid'];
                $adcell['search'] = $view->sRequests['sSearch'];
                $adcell['productIds'] = self::getArticle($view->sSearchResults['sArticles']);

                $view->Adcell = $adcell;
            } elseif ($controllerName == 'detail') {
                $adcell['typ'] = 'product';
                $adcell['retargeting'] = $this->config['retargeting'];
                $adcell['pid'] = $this->config['pid'];
                $adcell['productId'] = $view->sArticle['ordernumber'];
                $adcell['productName'] = $view->sArticle['articleName'];
                $adcell['categoryId'] = $view->sCategoryInfo['id'];
                $adcell['productIds'] = self::getArticle($view->sArticle['sSimilarArticles']);

                $view->Adcell = $adcell;
            } elseif ($controllerName == 'checkout' &&
                (
                    $request->getActionName() == "cart" ||
                    $request->getActionName() == "confirm" ||
                    $request->getActionName() == "finish"
                )) {
                $adcell['typ'] = $request->getActionName();
                $adcell['retargeting'] = $this->config['retargeting'];
                $view->Adcell = $adcell;

                $articlesByEvent = $this->splitBasketByEvent($view->sBasket['content']);
                $events = [];

                foreach ($articlesByEvent as $event => $articles) {
                    list($eventName, $pid) = explode('<->', $event);
                    $adcell = [];
                    $adcell['eventid'] = $eventName;
                    $adcell['pid'] = $pid;
                    $adcell['productIds'] = self::getArticle($articles);
                    $adcell['quantities'] = self::getQuantity($articles);
                    $adcell['basketProductCount'] = self::getProductCount($articles);
                    $adcell['basketTotal'] = $this->getBasketSum($articles);
                    $adcell['basketTotalNetto'] = $this->getBasketSum($articles);
                    $events[] = $adcell;
                }
                $view->AdcellEvents = $events;
            }
        }
    }

    public function getArticle($articles)
    {
        $ordernumber = [];
        foreach ($articles as $article) {
            if ($article['modus'] == 0) {
                $ordernumber[] = $article['ordernumber'];
            }
        }

        return implode(";", $ordernumber);
    }

    public function getQuantity($articles)
    {
        $quantities = [];
        foreach ($articles as $article) {
            if ($article['modus'] == 0) {
                $quantities[] = $article['quantity'];
            }
        }

        return implode(";", $quantities);
    }

    public function getProductCount($articles)
    {
        $result = 0;
        foreach ($articles as $article) {
            if ($article['modus'] == 0) {
                $result += $article['quantity'];
            }
        }

        return $result;
    }

    public function getBasketSum($articles)
    {
        $result = 0;
        foreach ($articles as $article) {
            if ($article['modus'] == 0) {
                if ($article['netprice']) {
                    $sum = $article['quantity'] * $article['netprice'];
                } else {
                    $sum = $article['quantity'] * $article['priceNumeric'];
                }
                $result += $sum;
            }
        }

        return $result;
    }

    public function setCategoriesInBasket($basketContent, $sCategoryStart)
    {
        if ($basketContent['modus'] == 0) {
            $sql = '
				SELECT
					   c.description 
				FROM
					s_articles_categories_ro ac, s_categories c

				WHERE
					ac.articleID = ?
				AND
					ac.categoryID = ?
				AND
					ac.parentCategoryID = c.id
				ORDER BY ac.id
			';

            return Shopware()->Db()->fetchOne($sql, [$basketContent['articleID'], $sCategoryStart]);
        }
    }

    /**
     * @param $articles
     *
     * @return bool
     */
    private function basketHasVoucher($articles)
    {
        foreach ($articles as $article) {
            if ($article['modus'] == 2) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $articles
     *
     * @return array
     */
    private function splitBasketByEvent($articles)
    {
        $hasVoucher = $this->basketHasVoucher($articles);
        $eventRepo = Shopware()->Models()->getRepository(AdcellEvent::class);
        /** @var AdcellEvent $standardEvent */
        $standardEvent = $eventRepo->find(1);
        $pid = $eventName = '';

        $articlesByEvent = [];
        foreach ($articles as $article) {
            if (($article['modus'] == 0) && isset($article['additional_details'])) {
                $eventId = $article['additional_details']['adcell_event'];
                // Use standard event ID for all products if there is a voucher in cart
                if ($hasVoucher) {
                    $eventId = 1;
                }
                if ($eventId == 1) {
                    if ($standardEvent) {
                        $pid = $standardEvent->getPid();
                        $eventName = $standardEvent->getName();
                    }
                } else {
                    /** @var AdcellEvent $event */
                    $event = $eventRepo->find($eventId);
                    if ($event) {
                        $pid = $event->getPid();
                        $eventName = $event->getName();
                    }
                }

                if (!empty($pid)) {
                    $articlesByEvent[$eventName . '<->' . $pid][] = $article;
                }
            }
        }

        return $articlesByEvent;
    }
}
