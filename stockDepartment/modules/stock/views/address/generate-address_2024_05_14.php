<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\modules\stock\models\RackAddress;
use yii\bootstrap\ActiveForm;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\stock\models\StockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('stock/titles', 'Generate address');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="generate-address">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php //echo $this->render('_search-filter', ['model' => $searchModel]); ?>

    <div class="stock-item-search">

        <?php $form = ActiveForm::begin([
            'id' => 'generate-address-form',
        ]); ?>

        <table class="table" width="60%" cellspacing="10">
            <tr>
                <td width="20%">
                    <?= $form->field($model, 'stage')->dropDownList(RackAddress::getStageValuesArray()) ?>
                </td>
                <td width="20%">
                    <?= $form->field($model, 'row') ?>
                </td>
                <td width="20%">
                    <?= $form->field($model, 'rack') ?>
                </td>
                <td width="20%">
                    <?= $form->field($model, 'level')->dropDownList(RackAddress::getLevelValuesArray()) ?>
                </td>

            </tr>
        </table>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('buttons', 'Submit'), ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('buttons', 'Очистить поиск'), ['index'], ['class' => 'btn btn-primary']) ?>
            <?= Html::tag('span', Yii::t('buttons', 'Print lost list'), ['class' => 'btn btn-warning ', 'style' => '', 'id' => 'print-lost-list-bt','data-url-value'=>Url::to(['print-lost-list'])]) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

</div>
