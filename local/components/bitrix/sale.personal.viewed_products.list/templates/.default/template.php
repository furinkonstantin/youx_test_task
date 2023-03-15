<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
    
    use Bitrix\Main\Localization\Loc;
    
    $this->setFrameMode(true);
?>
<div id="viewed_products">
    <? if($arResult['DATA_PRODUCTS']):?>
        <table class="table">
            <tr>
                <th>Изображение</th>
                <th>Название</th>
                <th>Цена</th>
                <th>Ссылка</th>
            </tr>
            <? foreach($arResult['DATA_PRODUCTS'] as $arProduct):?>
                <tr data-product_id="<?=$arProduct['ID']?>">
                    <td>
                        <img class="img-thumbnail" width="50" height="50" src="<?=$arProduct['DETAIL_PICTURE_SRC']?>" title="<?=$arProduct['NAME']?>" />
                    </td>
                    <td><?=$arProduct['NAME']?></td>
                    <td><?=$arProduct['PRICE']?></td>
                    <td>
                        <a href="<?=$arProduct['DETAIL_PAGE_URL']?>">Перейти в карточку товара</a>
                    </td>
                    <td>
                        <button type="button" class="close js-delete-viewed-product" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?=Loc::getMessage('COUNT_VIEWED_PRODUCT_TEXT', [
                            'COUNT_VIEWED_PRODUCT' => $arProduct['COUNT_VIEWED_PRODUCT']
                        ])?>
                    </td>
                </tr>
            <? endforeach;?>
        </table>
    <? else:?>
        <?=Loc::getMessage('EMPTY_DATA_PRODUCTS');?>
    <? endif;?>
</div>