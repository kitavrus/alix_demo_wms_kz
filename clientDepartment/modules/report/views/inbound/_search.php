<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use common\modules\client\models\Client;
use yii\helpers\ArrayHelper;
use common\modules\transportLogistics\components\TLHelper;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\kpiSettings\models\KpiSettingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="inbound-orders-grid-search">

<?php $form = ActiveForm::begin([
    'method' => 'get',
    'id' => 'inbound-orders-grid-search-form',
]); ?>

<table class="table" width="60%" cellspacing="10">
    <tr>
        <td width="20%">
            <?= $form->field($model, 'parent_order_number')->label(Yii::t('inbound/forms', 'Party number')) ?>
        </td>
        <td width="20%">
            <?= $form->field($model, 'order_number')->label(Yii::t('outbound/forms', 'Order number')) ?>
        </td>
        <td width="20%">
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
        <td width="20%">
            <?= $form->field($model, 'date_confirm')->widget(DateRangePicker::className(),
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
    <tr>
        <td width="20%">
            <?= $form->field($model, 'product_barcode')->label('Продукт штрих-код') ?>
        </td>
        <td width="20%">
            <?= $form->field($model, 'order_type')->dropDownList($model->getOrderTypeArray(), ['prompt'=>Yii::t('titles', 'Select')])?>
        </td>
        <td width="20%">
            <?= $form->field($model, 'status')->dropDownList($model->getStatusArray(), ['prompt'=>Yii::t('titles', 'Select')]) ?>
        </td>
		<td width="20%">
			<?= $form->field($model, 'comments')->label('Комментарий') ?>
	    </td>
<!--        <td width="20%">-->
<!--            --><?//= $form->field($model, 'to_point_id')->widget(Select2::className(),
//                [
//                    'data' => $clientStoreArray,
//                    'options' => [
//                        'placeholder' => Yii::t('transportLogistics/forms', 'Select')
//                    ],
//                ]
//            ) ?>
<!--        </td>-->
<!--        <td width="20%">-->
<!--            --><?//= $form->field($model, 'from_point_id')->widget(Select2::className(),
//                [
//                    'data' => $clientStoreArray,
//                    'options' => [
//                        'placeholder' => Yii::t('transportLogistics/forms', 'Select')
//                    ],
//                ]
//            ) ?>
<!--        </td>-->
    </tr>
</table>

<div class="form-group">
    <?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Yii::t('buttons', 'Clear search'), 'index', ['class' => 'btn btn-warning']) ?>
</div>
<?php ActiveForm::end(); ?>
</div>
