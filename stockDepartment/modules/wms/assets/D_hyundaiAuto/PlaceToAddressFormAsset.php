<?php
namespace stockDepartment\modules\wms\assets\hyundaiAuto;

use yii\web\AssetBundle;

class PlaceToAddressFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/hyundaiAuto/place-to-address-form.js',

    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_BEGIN
    ];
}