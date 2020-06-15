<?php

namespace CbaxAdcell\Subscriber;

use Enlight\Event\SubscriberInterface;
use Shopware\Components\Plugin\ConfigReader;

class Frontend implements SubscriberInterface
{
	/**
     * @var
     */
    private $config;
	
	/**
     * Frontend constructor
     */
    public function __construct($pluginName, ConfigReader $configReader)
    {
		$this->config = $configReader->getByPluginName($pluginName, Shopware()->Shop());
    }

    public static function getSubscribedEvents()
    {
        return array(
            'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'onPostDispatch'
        );
    }

	public function onPostDispatch(\Enlight_Event_EventArgs $args)
	{
		if (!$this->config['active'])
			return;
		
		/** @var \Enlight_Controller_Action $controller */
		$controller = $args->getSubject();
		$view = $controller->View();
		$request = $controller->Request();
		
		$controllerName = strtolower($request->getControllerName());
		
		$view->AdcellTracking = $this->config['active'];
		
		// Adcell Remarketing Code
		if (!empty($this->config['pid']))
		{
			if ($controllerName == 'index')
			{
				$adcell['typ'] 			= 'start';
				$adcell['retargeting'] 	= $this->config['retargeting'];
				$adcell['pid'] 			= $this->config['pid'];
				
				$view->Adcell = $adcell;
			}
			elseif ($controllerName == 'listing')
			{
				$adcell['typ'] 			= 'category';
				$adcell['retargeting'] 	= $this->config['retargeting'];
				$adcell['pid'] 			= $this->config['pid'];
				$adcell['categoryName'] = $view->sCategoryContent['name'];
				$adcell['categoryId'] 	= $view->sCategoryContent['id'];
				$adcell['productIds'] 	= self::getArticle($view->sArticles);
				
				$view->Adcell = $adcell;
			}
			elseif ($controllerName == 'search')
			{
				$adcell['typ'] 			= 'search';
				$adcell['retargeting'] 	= $this->config['retargeting'];
				$adcell['pid'] 			= $this->config['pid'];
				$adcell['search'] 		= $view->sRequests['sSearch'];
				$adcell['productIds'] 	= self::getArticle($view->sSearchResults['sArticles']);
				
				$view->Adcell = $adcell;
			}
			elseif ($controllerName == 'detail')
			{
				$adcell['typ'] 			= 'product';
				$adcell['retargeting'] 	= $this->config['retargeting'];
				$adcell['pid'] 			= $this->config['pid'];
				$adcell['productId'] 	= $view->sArticle['ordernumber'];
				$adcell['productName'] 	= $view->sArticle['articleName'];
				$adcell['categoryId'] 	= $view->sCategoryInfo['id'];
				$adcell['productIds'] 	= self::getArticle($view->sArticle['sSimilarArticles']);
				
				$view->Adcell = $adcell;
			}
			elseif ($controllerName == 'checkout' && $request->getActionName() == "cart")
			{
				$adcell['typ'] 					= 'basket';
				$adcell['retargeting'] 			= $this->config['retargeting'];
				$adcell['pid'] 					= $this->config['pid'];
				$adcell['productIds'] 			= self::getArticle($view->sBasket['content']);
				$adcell['quantities'] 			= self::getQuantity($view->sBasket['content']);
				$adcell['basketProductCount'] 	= self::getProductCount($view->sBasket['content']);
				$adcell['basketTotal'] 			= ($view->sBasket['AmountNetNumeric'] - $view->sBasket['sShippingcostsNet']);
				
				$view->Adcell = $adcell;
			}
			elseif ($controllerName == 'checkout' && $request->getActionName() == "confirm")
			{
				$adcell['typ'] 					= 'confirm';
				$adcell['retargeting'] 			= $this->config['retargeting'];
				$adcell['pid'] 					= $this->config['pid'];
				$adcell['productIds'] 			= self::getArticle($view->sBasket['content']);
				$adcell['quantities'] 			= self::getQuantity($view->sBasket['content']);
				$adcell['basketProductCount'] 	= self::getProductCount($view->sBasket['content']);
				$adcell['basketTotal'] 			= ($view->sBasket['AmountNetNumeric'] - $view->sBasket['sShippingcostsNet']);
				
				$view->Adcell = $adcell;
			}
			elseif ($controllerName == 'checkout' && $request->getActionName() == "finish")
			{
				$adcell['typ'] 					= 'finish';
				$adcell['retargeting'] 			= $this->config['retargeting'];
				$adcell['pid'] 					= $this->config['pid'];
				$adcell['eventid'] 				= $this->config['eventid'];
				$adcell['basketId'] 			= $view->sOrderNumber;
				$adcell['basketTotal'] 			= ($view->sBasket['AmountNetNumeric'] - $view->sBasket['sShippingcostsNet']);
				$adcell['basketTotalNetto'] 	= ($view->sBasket['AmountNetNumeric'] - $view->sBasket['sShippingcostsNet']);
				$adcell['basketProductCount'] 	= self::getProductCount($view->sBasket['content']);
				$adcell['productIds'] 			= self::getArticle($view->sBasket['content']);
				$adcell['quantities'] 			= self::getQuantity($view->sBasket['content']);
				
				$view->Adcell = $adcell;
			}
		}
	}
	
	public function getArticle($articles)
	{
		foreach ($articles as $article)
		{
			if ($article['modus'] == 0)
			{
				$ordernumber[] = $article['ordernumber'];
			}
		}
		
		$result = implode(";", $ordernumber);
		
		return $result;
	}
	
	public function getQuantity($articles)
	{
		foreach ($articles as $article)
		{
			if ($article['modus'] == 0)
			{
				$quantities[] = $article['quantity'];
			}
		}
		
		$result = implode(";", $quantities);
		
		return $result;
	}
	
	public function getProductCount($articles)
	{
		foreach ($articles as $article)
		{
			if ($article['modus'] == 0)
			{
				$result = $result + $article['quantity'];
			}
		}
		
		return $result;
	}
	
	public function getBasketSum($articles)
	{
		foreach ($articles as $article)
		{
			if ($article['modus'] == 0)
			{
				if ($article['netprice'])
				{
					$sum = $article['quantity'] * $article['netprice'];
				}
				else
				{
					$sum = $article['quantity'] * $article['priceNumeric'];
				}
				$result = $result + $sum;
			}
		}
		
		return $result;
	}
	
	public function setCategoriesInBasket($basketContent, $sCategoryStart)
	{

		if ($basketContent['modus'] == 0)
		{
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
	
			return Shopware()->Db()->fetchOne($sql, array($basketContent['articleID'], $sCategoryStart));
		}
	}
}
