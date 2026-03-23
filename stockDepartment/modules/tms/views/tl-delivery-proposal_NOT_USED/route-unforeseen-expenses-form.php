<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 17.09.14
 * Time: 17:21
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datecontrol\DateControl;
use app\modules\transportLogistics\transportLogistics;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposalRouteUnforeseenExpenses */
/* @var $modelDRoute common\modules\transportLogistics\models\TlDeliveryRoutes */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tl-delivery-proposal-route-unforeseen-expenses-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

<!--    --><?//=
//    $form->field($model, 'delivery_date')->widget(DateControl::className(), [
//            'type'=>DateControl::FORMAT_DATETIME,
//
//        ]); ?>
    <?= $form->field($model, 'type_id')->dropDownList($model->getTypeArray(),['prompt'=>'']) ?>
    <?= $form->field($model, 'price_cache')->textInput(['maxlength' => 26]) ?>
    <?= $form->field($model, 'cash_no')->dropDownList($modelDRoute->getPaymentMethodArray()) ?>
    <?= $form->field($model, 'who_pays')->dropDownList($model->getWhoPaysArray()) ?>
    <?= $form->field($model, 'price_with_vat')->textInput(['maxlength' => 26]) ?>


<!--    --><?php //if (!$model->isNewRecord) { ?>
<!--        --><?//= $form->field($model, 'status')->dropDownList($modelDRoute->getStatusArray()) ?>
        <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>
<!--    --><?php //} ?>

<?php //\yii\helpers\VarDumper::dump($modelDRoute,10,true) ; ?>
    <?= $form->field($model, 'tl_delivery_proposal_id',['template'=>'{input}'])->hiddenInput(['value'=>$modelDRoute->tl_delivery_proposal_id]) ?>
    <?= $form->field($model, 'tl_delivery_route_id',['template'=>'{input}'])->hiddenInput(['value'=>$modelDRoute->id]) ?>
    <?= $form->field($model, 'client_id',['template'=>'{input}'])->hiddenInput(['value'=>$modelDRoute->client_id]) ?>

<!--    --><?//= $form->field($model, 'created_user_id')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'updated_user_id')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'created_at')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('transportLogistics/buttons', 'Create') : Yii::t('transportLogistics/buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
