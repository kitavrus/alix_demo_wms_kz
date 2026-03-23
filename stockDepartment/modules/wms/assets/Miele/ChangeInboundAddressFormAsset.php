<?php
namespace stockDepartment\modules\wms\assets\Miele;

use yii\web\AssetBundle;

class ChangeInboundAddressFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/miele/change-inbound-address-form.js',

    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_BEGIN
    ];
}