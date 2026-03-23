<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 04.08.2015
 * Time: 17:25
 */
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\outbound\models\OutboundOrder */
$this->title = Yii::t('outbound/titles', 'Order №').$model->order_number;
$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->order_number, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('titles', 'Update');
?>
<div class="store-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>