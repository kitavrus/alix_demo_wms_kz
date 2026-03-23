<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\modules\stock\models\RackAddress;
use yii\bootstrap\ActiveForm;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\stock\models\StockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('stock/titles', 'Print rack address barcode');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="generate-address">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php //echo $this->render('_search-filter', ['model' => $searchModel]); ?>

    <div class="stock-item-search col-md-4">
        <?= Html::textInput('address','', ['class'=>'form-control', 'id' =>'address']) ?>

    </div>
    <div class="form-group">
        <?= Html::tag('span', Yii::t('buttons', 'Print barcode'), ['class' => 'btn btn-warning ', 'style' => '', 'id' => 'print-stock-address-barcode-bt','data-url-value'=>Url::to(['stock-address'])]) ?>
<!--        --><?//= Html::tag('span', Yii::t('buttons', 'Print lost list'), ['class' => 'btn btn-warning ', 'style' => '', 'id' => 'print-lost-list-bt','data-url-value'=>Url::to(['print-lost-list'])]) ?>
<!--        --><?//= Html::tag('span', Yii::t('buttons', 'Print lost list'), ['class' => 'btn btn-warning ', 'style' => '', 'id' => 'print-lost-list-bt','data-url-value'=>Url::to(['print-lost-list'])]) ?>
    </div>

</div>
