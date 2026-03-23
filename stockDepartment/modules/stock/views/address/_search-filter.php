<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\modules\stock\models\RackAddressSearch;

/* @var $this yii\web\View */
/* @var $model RackAddressSearch */
/* @var $form ActiveForm */
?>

<div class="rack-address-search">
    <?php $form = ActiveForm::begin([
        'action' => ['generate-address'],
        'method' => 'get',
        'options' => [
            'id' => 'rack-address-search-form'
        ]
    ]); ?>

    <?= $form->field($model, Yii::t('stock/forms', 'address'))->textInput([
        'placeholder' => Yii::t('app', 'Введите адрес'),
        'class' => 'form-control'
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('buttons', 'Очистить поиск'), ['generate-address'], ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div> 