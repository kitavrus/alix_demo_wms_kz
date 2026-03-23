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
use stockDepartment\modules\crossDock\assets\CrossDockAsset;
use yii\bootstrap\Modal;

CrossDockAsset::register($this);
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

    <?= $form->field($formModel, 'cross_dock_barcode')->textInput()->label(Yii::t('outbound/forms', 'Picking list barcode'));?>


    <?php ActiveForm::end(); ?>

<!--    <div class="form-group">-->
<!--        --><?//= Html::tag('span', Yii::t('inbound/buttons', 'Print'), ['class' => 'btn btn-danger pull-right', 'style' => ' margin-left:10px;', 'data-url' => 'print-cross-dock-list', 'id' => 'cross-dock-print-bt']) ?>
<!--    </div>-->


</div>
<?php if ($crossOrders) { ?>
    <div id="result-table-body">
        <table class="table table-bordered table-striped">
            <tr>
                <th>Название магазина</th>
                <th>Предполагаемое кол-во</th>
                <th>Действительное кол-во</th>
            </tr>

            <?php foreach ($crossOrders as $co) {
                if ($store = $co->pointTo) { ?>

                    <tr>
                        <td><?= $store->getPointTitleByPattern('{shopping_center_name} / {city_name}') ?></td>
                        <td><?= $co->expected_number_places_qty ?></td>
                        <td><?= Html::input('text', 'accepted_qty', $co->expected_number_places_qty, ['class'=>'acc-qty form-control input-sm', 'data-id'=>$co->id])?></td>
                    </tr>

                <?php } ?>
            <?php } ?>
        </table>
        <div class="form-group">
            <?= Html::tag('span', Yii::t('inbound/buttons', 'Принять'), ['class' => 'btn btn-danger pull-right', 'style' => ' margin-right:20px;', 'data-url' => 'confirm-cross-dock-list', 'id' => 'cross-dock-confirm-bt']) ?>
        </div>
    </div>
<?php } ?>

<?php Modal::begin([
    //'header' => '<h4 id="delivery-proposal-index-header"></h4>',
    'id' => 'loading-modal'
]); ?>
<?= "<div id='loading-modal-content'>Идет обработка данных, пожалуйста подождите...</div>"; ?>
<?php Modal::end(); ?>

