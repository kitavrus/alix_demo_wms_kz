<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\inbound\models\InboundOrder */
//\yii\helpers\VarDumper::dump($model,10,true);
//\yii\helpers\VarDumper::dump($model->order_number,10,true);
//die;
//$this->title = Yii::t('titles', 'Update inbound comments: ') . ' ' . $model->order_number;
$this->params['breadcrumbs'][] = Yii::t('titles', 'Update');
?>
<div class="store-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
