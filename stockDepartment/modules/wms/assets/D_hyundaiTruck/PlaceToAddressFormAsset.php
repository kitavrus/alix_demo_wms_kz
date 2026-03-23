<?php
namespace stockDepartment\modules\wms\assets\hyundaiTruck;

use yii\web\AssetBundle;

class PlaceToAddressFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/hyundaiTruck/place-to-address-form.js',

    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_BEGIN
    ];
}