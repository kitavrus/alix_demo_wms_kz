<?php
/**
 * Created by PhpStorm.
 * User: Kitavrus
 * Date: 17.04.15
 * Time: 10:45
 */
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
use yii\bootstrap\Modal;
?>

<?php $this->title = Yii::t('inbound/titles', 'Confirm Cross Dock Picking List'); ?>
<div class="cross-dock-process-form">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin([
            'id' => 'cross-dock-confirm-form',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
        ]
    ); ?>

    <?= $form->field($formModel, 'cross_dock_barcode' )->textInput(['data-url' => Url::toRoute('confirm-cross-dock')])->label(Yii::t('outbound/forms', 'Picking list barcode'));?>


    <?php ActiveForm::end(); ?>

</div>


    <div id="result-table-body">

    </div>


<?php Modal::begin([
    //'header' => '<h4 id="delivery-proposal-index-header"></h4>',
    'id' => 'loading-modal',
    'closeButton' => false,
    'options' => [
        'data-backdrop' => 'static',
        'data-keyboard' => 'false',
    ],
]); ?>
<?= "<div id='loading-modal-content'>Идет обработка данных, пожалуйста подождите...</div>"; ?>
<?php Modal::end(); ?>
