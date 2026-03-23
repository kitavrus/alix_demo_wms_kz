<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\stock\models\StockSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="defect-search">
    <?php $form = ActiveForm::begin([
        'id'=>'defect-search-form',
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <table class="table" width="100%" cellspacing="10">
        <tr>
            <td width="10%">
                <?= $form->field($model, 'secondary_address')->label(Yii::t('stock/forms', 'Secondary address')) ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'primary_address')->label(Yii::t('stock/forms', 'Primary address')) ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'product_barcode')->label(Yii::t('stock/forms', 'Product barcode')) ?>
            </td>
        </tr>
    </table>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('buttons', 'Очистить поиск'), 'index', ['class' => 'btn btn-warning']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>