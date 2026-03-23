<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 07.06.2017
 * Time: 16:39
 */

namespace stockDepartment\modules\sheetShipment\assets;


use yii\web\AssetBundle;
use Yii;

class SheepShipmentAssets extends AssetBundle
{
    public $sourcePath = '@stockDepartment/modules/sheetShipment/assets/src';

    public $js = [
        'sheep-shipment.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD
    ];
}