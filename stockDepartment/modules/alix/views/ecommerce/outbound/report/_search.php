<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use stockDepartment\modules\intermode\controllers\ecommerce\outbound\domain\constants\OutboundStatus;

/* @var $this yii\web\View */
/* @var $model common\ecommerce\entities\EcommerceOutboundSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ecommerce-outbound-search">

    <?php $form = ActiveForm::begin([
        'id' => 'outbound-order-search-form',
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <table class="table" width="100%" cellspacing="10">
        <tr>
            <td width="10%">
                <?= $form->field($model, 'order_number')->label(Yii::t('outbound/forms', 'Order number')) ?>
            </td>
<!--            <td width="10%">-->
<!--                --><?//= $form->field($model, 'client_ReferenceNumber')->label(Yii::t('outbound/forms', 'ТТН')); ?>
<!--            </td>-->
            <td width="10%">
                <?= $form->field($model, 'packing_date')->widget(DateRangePicker::className(),
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
	        <td width="10%">
				<?= $form->field($model, 'date_left_warehouse')->widget(DateRangePicker::className(),
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
<!--            <td width="10%">-->
<!--                --><?//= $form->field($model, 'updated_at')->widget(DateRangePicker::className(),
//                    [
//                        'convertFormat'=>true,
//                        'pluginOptions'=>[
//                            'locale'=>[
//                                'separator'=> ' / ',
//                                'format'=>'Y-m-d',
//                            ]
//                        ]
//                    ]
//                ) ?>
<!--            </td>-->
<!--            <td width="10%">-->
<!--                --><?//= $form->field($model, 'client_ShipmentSource')->dropDownList(\common\ecommerce\constants\OutboundShipmentSource::getAll(), ['prompt'=>'Выберите статус']) ?>
<!--            </td>-->
        </tr>
        <tr>


            <td width="10%">
                <?= $form->field($model, 'status')->dropDownList(OutboundStatus::getAll(), ['prompt'=>'Выберите статус']) ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'productBarcode')->label('Продукт штрих-код') ?>
            </td>
<!--            <td width="10%">-->
<!--                --><?//= $form->field($model, 'id')->label(Yii::t('outbound/forms', 'Id')) ?>
<!--            </td>-->
            <td width="10%">
                <?= $form->field($model, 'outboundBoxBarcode')->label(Yii::t('outbound/forms', 'Шк короба отгрузки')) ?>
            </td>

            <td width="10%">
                <?= $form->field($model, 'findType')->dropDownList(\common\ecommerce\entities\EcommerceOutboundSearch::findTypeList(), ['prompt'=>'Выберите ...'])->label(Yii::t('outbound/forms', 'Тип поиска')) ?>
            </td>
        </tr>
		<tr>
			<td width="10%">
				<?= $form->field($model, 'productArticle')->label(Yii::t('outbound/forms', 'Article')) ?>
			</td>
        </tr>
    </table>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('buttons', 'Очистить поиск'), 'index', ['class' => 'btn btn-warning']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>