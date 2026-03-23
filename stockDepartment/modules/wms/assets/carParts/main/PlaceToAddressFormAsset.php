<?php
namespace stockDepartment\modules\wms\assets\carParts\main;

use yii\web\AssetBundle;

class PlaceToAddressFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/carParts/main/place-to-address-form.js',

    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_BEGIN
    ];
}