<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 03.12.14
 * Time: 14:42
 */
use yii\helpers\Html;
//use yii\widgets\DetailView;
//use yii\widgets\ActiveForm;
//use yii\bootstrap\Modal;
use yii\helpers\Url;
use common\modules\transportLogistics\components\TLHelper;
use app\modules\transportLogistics\transportLogistics;
use kartik\datecontrol\DateControl;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;


/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryRoutes */
/* @var $deliveryProposalModel common\modules\transportLogistics\models\TlDeliveryProposal */
/* @var $form yii\widgets\ActiveForm */

?>
<div class="tl-delivery-proposal-route-model-popup-form">

    <?php $form = ActiveForm::begin([
//        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL],
        'id'=>'route-model-popup-form',
        'enableClientValidation' => true,
        'validateOnType' => true,
    ]); ?>
    <?= $form->field($model, 'route_from')->dropDownList(TLHelper::getStockPointArray(),['readonly'=>true]); ?>
    <?= $form->field($model, 'route_to',[])->widget(Select2::classname(), [
        'language' => 'ru',
        'data' => TLHelper::getStockPointArray($client_id),
        'options' => ['placeholder' => Yii::t('transportLogistics/titles', 'Select route to')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 2]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('transportLogistics/buttons', 'Add'), ['class' =>'btn btn-success col-sm-offset-4']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>