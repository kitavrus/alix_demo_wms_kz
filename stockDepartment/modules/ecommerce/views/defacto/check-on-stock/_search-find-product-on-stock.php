<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\ecommerce\entities\EcommerceOutboundSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ecommerce-outbound-search">

    <?php $form = ActiveForm::begin([
        'id' => 'find-product-on-stock-form',
        'action' => ['index'],
//        'method' => 'get',
		'method' => 'post',
    ]); ?>

    <table class="table">
        <tr>
            <td >
				<?= $form->field($model, 'client_product_sku')->textarea()->label(Yii::t('outbound/forms', 'Product sku')) ?>
            </td>
        </tr>
    </table>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary','id'=>'search-btn']) ?>
        <?= Html::a(Yii::t('buttons', 'Очистить поиск'), 'index', ['class' => 'btn btn-warning']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
	$(function(){

		$('#search-btn').on('click',function() {
			$('#find-product-on-stock-form').attr('action',"index");
			// $('#find-product-on-stock-form').submit();
		});
	});
</script>