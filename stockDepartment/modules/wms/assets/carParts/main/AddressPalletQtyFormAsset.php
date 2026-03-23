<?php
namespace stockDepartment\modules\wms\assets\carParts\main;

use yii\web\AssetBundle;

class AddressPalletQtyFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/carParts/main/address-pallet-qty-form.js',
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_BEGIN
    ];
}