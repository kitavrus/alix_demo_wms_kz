<?php

namespace stockDepartment\modules\wms\managers\erenRetail\placement;

use yii\web\AssetBundle;

class PlaceToAddressFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        '/js/erenRetail/place-to-address-form.js',

    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_BEGIN
    ];
}