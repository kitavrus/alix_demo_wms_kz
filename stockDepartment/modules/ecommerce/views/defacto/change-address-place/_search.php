<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model common\ecommerce\entities\EcommerceInboundSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ecommerce-inbound-search">

    <?php $form = ActiveForm::begin([
    	'id'=>'change-address-search-form',
        'action' => ['report'],
        'method' => 'get',
    ]); ?>

    <table class="table" width="100%" cellspacing="10">
        <tr>
            <td width="10%">
                <?= $form->field($model, 'anyPlace')->label(Yii::t('outbound/forms', 'Короб или место')) ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'created_at')->widget(DateRangePicker::className(),
                    [
                        'convertFormat'=>true,
                        'pluginOptions'=>[
                            'locale'=>[
                                'separator'=> ' / ',
                                'format'=>'Y-m-d',
                            ]
                        ]
                    ]
                ) ?>
            </td>
        </tr>
    </table>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('buttons', 'Очистить поиск'), 'report', ['class' => 'btn btn-warning']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
