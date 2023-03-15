<?php


    use Bitrix\Main\Engine\CurrentUser;
    use Bitrix\Main\Loader;
    use Bitrix\Highloadblock as HL; 
    use Bitrix\Main\Entity;
    use Bitrix\Main\UserTable;
    
    CBitrixComponent::includeComponentClass('bitrix:sale.personal.viewed_products.list');

    class ViewedProducts
    {
        
            const HL_BLOCK_ID = 4;
            public $entity_data_class;
            public $productID;
        
            public function __construct()
            {
                Loader::includeModule('highloadblock');
                $hlblock = HL\HighloadBlockTable::getById(self::HL_BLOCK_ID)->fetch();
                $entity = HL\HighloadBlockTable::compileEntity($hlblock); 
                $this->entity_data_class = $entity->getDataClass();
            }
            
            public function getIssetData()
            {
                $res = $this->entity_data_class::getRow([
                    'select' => [
                        'ID',
                        'UF_COUNT_VIEWED'
                    ],
                    'filter' => [
                        'UF_USER_ID' => CurrentUser::get()->getId(),
                        'UF_PRODUCT' => $this->productID
                    ]
                ]);
                return $res;
            }
            
            public function add($productID)
            {
                $viewedProductsComponent = new ViewedProductsComponent;
                $this->productID = $productID;
                if(CurrentUser::get()->getId())
                {
                    if($arIssetData = $this->getIssetData())
                    {
                        $arFields = [
                            'UF_COUNT_VIEWED' => $arIssetData['UF_COUNT_VIEWED'] + 1
                        ];
                        $this->entity_data_class::update($arIssetData['ID'], $arFields);
                    }
                    else
                    {
                        $arUser = UserTable::getByID(CurrentUser::get()->getId())->fetch();
                        $arFields = [
                            'UF_NAME' => $arUser['EMAIL'],
                            'UF_PRODUCT' => $this->productID,
                            'UF_USER_ID' => CurrentUser::get()->getId(),
                            'UF_COUNT_VIEWED' => 1
                        ];
                        $this->entity_data_class::add($arFields);
                    }
                }
                $viewedProductsComponent->cache->clean($viewedProductsComponent->cacheId);
            }
            
    }