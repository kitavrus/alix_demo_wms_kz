<?php
use yii\helpers\Html;

$wrap = '';

$wrap .= Html::beginTag('div',['class' => 'a4']);
$output = $wrap . $html . Html::endTag('div');

echo $output;
//Yii::$app->end();