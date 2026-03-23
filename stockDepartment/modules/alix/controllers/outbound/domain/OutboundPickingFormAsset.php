<?php
namespace stockDepartment\modules\alix\controllers\outbound\domain;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class OutboundPickingFormAsset extends AssetBundle
{
	public $basePath = '@webroot';
	public $baseUrl = '@web';

	public $js = [
		'js/alix/outbound/outbound-picking-form.js',
	];

	public $depends = [
		'yii\web\YiiAsset',
		'yii\bootstrap\BootstrapAsset',
	];

	public $jsOptions = [
		'position' => \yii\web\View::POS_HEAD
	];
}
