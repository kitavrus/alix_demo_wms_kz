<?php
/**
 * Created by PhpStorm.
 * User: Kitavrus
 * Date: 17.04.15
 * Time: 10:45
 */
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
use common\modules\transportLogistics\components\TLHelper;
use yii\bootstrap\Modal;

?>
<?php $this->title = Yii::t('wms/titles', 'Create new Cross Dock'); ?>
<div class="cross-dock-process-form">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin([
            'id' => 'create-cross-dock-form',
            'enableClientValidation' => true,
            'validateOnChange' => true,
            'validateOnSubmit' => false,
        ]
    ); ?>

    <?= $form->field($formModel, 'client_id', ['labelOptions' => ['label' => false]])->dropDownList(
        $clientsArray,
        ['prompt' => '',
            'id' => 'cross-dock-form-client-id',
            'class' => 'form-control input-lg hidden',
        ]
    ); ?>

    <?= $form->field($formModel, 'order_number')->textInput(
        ['prompt' => '',
//            'id' => 'cross-dock-form-order-number',
            'class' => 'form-control input-lg',
        ]
    )->label(Yii::t('inbound/forms', 'Party number')); ?>

    <table class="table table-bordered table-striped">
        <tr>
            <th>Название магазина</th>
            <th>Предполагаемое кол-во</th>
            <th>M3</th>
        </tr>
        <?php foreach ($stores as $store) { ?>
                <tr>
                    <td><?= $store->getPointTitleByPattern('{shopping_center_name} / {city_name} / {shop_code} :: {street} / {name}') ?></td>
                    <td> <?= $form->field($formModel, '['.$store->id.']expected_number_places_qty' )->textInput(['class'=>'exp-qty form-control input-sm','data-id' => $store->id])->label(false);?></td>
                    <td> <?= $form->field($formModel, '['.$store->id.']box_m3' )->textInput(['class'=>'box-m3 form-control input-sm','data-id' => $store->id])->label(false);?></td>
                </tr>
        <?php } ?>
    </table>

    <div class="form-group">
        <?= Html::tag('span', Yii::t('inbound/buttons', 'Добавить'), [
            'class' => 'btn btn-danger pull-right',
            'style' => ' margin-left:10px;',
            'data-url' => Url::toRoute('save-create-cross-dock-form'),
            'id' => 'save-create-cross-dock-form-bt'
        ]) ?>
        <?= Html::tag('span', Yii::t('inbound/buttons', 'Пред просмотр'), [
            'class' => 'btn btn-info pull-right',
            'style' => ' margin-left:10px;',
            'data-url' => Url::toRoute('preview-cross-dock-form'),
            'id' => 'preview-cross-dock-form-bt'
        ]) ?>
    </div>
    <?php ActiveForm::end(); ?>
        <br />
        <br />
        <div id="error-container">
            <div id="error-base-line"></div>
            <?= Alert::widget([
                'options' => [
                    'id' => 'error-list',
                    'class' => 'alert-danger hidden',
                ],
                'body' => '',
            ]);
            ?>
        </div>
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
<?= "<div id='loading-modal-content' style='font-size: 21px;'>Идет обработка данных, пожалуйста подождите...</div>"; ?>
<?php Modal::end(); ?>