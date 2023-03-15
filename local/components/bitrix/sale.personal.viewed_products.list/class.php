<?php

    use Bitrix\Main;
    use Bitrix\Main\Localization\Loc as Loc;
    use Bitrix\Main\Application;
    use Bitrix\Main\Context;
    use Bitrix\Main\Engine\CurrentUser;
    use Bitrix\Iblock\Elements\ElementCatalogTable;
    use Bitrix\Catalog\Model\Price;
    use Bitrix\Main\Data\Cache;
    
    Loc::loadMessages(__FILE__);
    
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

    class ViewedProductsComponent extends CBitrixComponent
    {
        
        public $viewedProducts;
        var $necessaryCountViewed = 3;
        var $cache;
        var $cacheId;
        
        public function __construct($component = null)
        {
            parent::__construct($component);
            $this->viewedProducts = new ViewedProducts;
            $this->cache = Application::getInstance()->getManagedCache();
            $this->cacheId = implode('|', [
                'viewed_products',
                CurrentUser::get()->getId(),
                SITE_ID
            ]);
        }
        
        public function onPrepareComponentParams($arParams)
        {
            if($arParams['NECESSARY_COUNT_VIEWED'])
            {
                $this->necessaryCountViewed = $arParams['NECESSARY_COUNT_VIEWED'];
            }
            return $arParams;
        }

        public function executeComponent()
        {
            $this->setTitle();
            $this->checkDeleteViewedProduct();
            $this->getResult();
            $this->includeComponentTemplate();
        }
        
        public function checkDeleteViewedProduct()
        {
            $request = Context::getCurrent()->getRequest();
            if($request->isPost())
            {
                $arPost = $request->getPostList()->toArray();
                if($arPost['action'] == 'delete' && !empty($arPost['product_id']))
                {
                    $arData = $this->viewedProducts->entity_data_class::getRow([
                        'select' => [
                            'ID'
                        ],
                        'filter' => [
                            'UF_USER_ID' => CurrentUser::get()->getId(),
                            'UF_PRODUCT' => $arPost['product_id']
                        ]
                    ]);
                    if($arData['ID'])
                    {
                        $this->viewedProducts->entity_data_class::delete($arData['ID']);
                        $this->cache->clean($this->cacheId);
                    }
                }
            }
        }
        
        public function getDataProducts()
        {
            $res = [];
            
            if ($this->cache->read($this->arParams['CACHE_TIME'], $this->cacheId))
            {
                $res = $this->cache->get($this->cacheId);
            }
            else
            {
                $arData = $this->viewedProducts->entity_data_class::getList([
                    'select' => [
                        'UF_PRODUCT',
                        'UF_COUNT_VIEWED'
                    ],
                    'filter' => [
                        'UF_USER_ID' => CurrentUser::get()->getId(),
                        '>UF_COUNT_VIEWED' => $this->necessaryCountViewed
                    ]
                ])->fetchAll();
                
                if($arData)
                {
                    $productIDs = [];
                    $countViewedProduct = [];
                    foreach($arData as $data)
                    {
                        $productIDs[] = $data['UF_PRODUCT'];
                        $countViewedProduct[$data['UF_PRODUCT']] = $data['UF_COUNT_VIEWED'];
                    }
                    
                    if($productIDs)
                    {
                        $arProducts = ElementCatalogTable::getList([
                            'select' => [
                                '*',
                                'DETAIL_PICTURE',
                                'NAME',
                                'DETAIL_PAGE_URL' => 'IBLOCK.DETAIL_PAGE_URL'
                            ],
                            'filter' => [
                                '=ID' => $productIDs
                            ],
                        ]);
                        foreach($arProducts as $arProduct)
                        {
                            if($arProduct['DETAIL_PICTURE'])
                            {
                                $arProduct['DETAIL_PICTURE_SRC'] = CFile::GetPath($arProduct['DETAIL_PICTURE']);
                            }
                            
                            $arPrice = \Bitrix\Catalog\Model\Price::getList([
                                'filter' => [
                                    'CATALOG_GROUP.XML_ID'=> 'BASE', 
                                    'PRODUCT_ID' => $arProduct['ID']
                                ]
                            ])->fetch();
                            
                            if($arPrice['PRICE'])
                            {
                                $arProduct['PRICE'] = CurrencyFormat($arPrice['PRICE'], $arPrice['CURRENCY']);
                            }
                            
                            $arProduct['COUNT_VIEWED_PRODUCT'] = $countViewedProduct[$arProduct['ID']];
                            
                            $arProduct['DETAIL_PAGE_URL'] = CIBlock::ReplaceDetailUrl($arProduct['DETAIL_PAGE_URL'], $arProduct, false, 'E');
                            
                            
                            $res[] = $arProduct;
                        }
                    }
                }
                $this->cache->set($this->cacheId, $res);
            }
            
            return $res;
        }
        
        public function getResult()
        {
            $this->arResult['DATA_PRODUCTS'] = $this->getDataProducts();
        }
        
        protected function setTitle()
        {
            global $APPLICATION;

            if ($this->arParams["SET_TITLE"] == 'Y')
                $APPLICATION->SetTitle(Loc::getMessage("SPOL_DEFAULT_TITLE"));
        }
        
    }
