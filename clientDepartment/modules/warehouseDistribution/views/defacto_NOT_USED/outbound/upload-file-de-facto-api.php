<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 04.02.15
 * Time: 12:05
 */

//use Yii;
//use common\modules\outbound\models\OutboundOrder;
//use common\modules\outbound\models\OutboundOrderItem;
//use common\modules\stock\models\Stock;
//use common\modules\store\models\Store;
//use common\modules\transportLogistics\models\TlDeliveryProposal;
//use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use yii\bootstrap\ActiveForm;
?>

<?php //$this->title = Yii::t('outbound/titles', 'Load outbound invoice number From DeFacto API');?>

<h1><?= Yii::t('outbound/titles', 'Upload outbound invoice number From DeFacto API') ?></h1>

<div class="load-from-defacto-api-form">
<?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'id'=>'load-from-defacto-api-form',
    ]
); ?>

<?= $form->field($model, 'file')->fileInput() ?>

<div class="form-group">
    <?= \yii\helpers\Html::submitButton(Yii::t('buttons', 'Create'), ['class' =>'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>