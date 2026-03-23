<?php
/* @var $pickScanListForm stockDepartment\modules\wms\models\defacto\PickListScanForm; */

\stockDepartment\modules\wms\assets\DeFactoPickListAsset::register($this);
?>
<?php $this->title = Yii::t('inbound/titles', 'Pick list scan'); ?>
<div class="pick-list-scan-form">
    <?php $form = \yii\bootstrap\ActiveForm::begin([
            'id' => 'pick-list-scan-form',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
        ]
    ); ?>
    <?= $form->field($pickScanListForm, 'pickListBarcode')->textInput(
        [
            'class' => 'form-control input-lg',
            'data-url' => \yii\helpers\Url::to('/wms/defacto/pick-list/scan-pick-list')
        ]
    ); ?>

    <?= $form->field($pickScanListForm, 'lotBarcode')->textInput([
        'class' => 'form-control input-lg',
        'data-url' => \yii\helpers\Url::to('/wms/defacto/pick-list/scan-lot')
    ]); ?>

    <?php \yii\bootstrap\ActiveForm::end(); ?>
</div>
<div class="container" id="container-button">
    <div class="row">
       <button type="button" class="btn btn-default key">0</button>
        <button type="button" class="btn btn-default key">1</button>
        <button type="button" class="btn btn-default key">2</button>
        <button type="button" class="btn btn-default key">3</button>
        <button type="button" class="btn btn-default key">4</button>
        <button type="button" class="btn btn-default key">5</button>
        <button type="button" class="btn btn-default key">6</button>
        <button type="button" class="btn btn-default key">7</button>
        <button type="button" class="btn btn-default key">8</button>
        <button type="button" class="btn btn-default key">9</button>
        <button type="button" class="btn btn-danger  key">enter</button>
        <button type="button" class="btn btn-warning key">del</button>

    </div>
</div>
<div id="error-container">
    <div id="error-base-line"></div>
    <?= \yii\bootstrap\Alert::widget([
        'options' => [
            'id' => 'error-list',
            'class' => 'alert-danger hidden',
        ],
        'body' => '',
    ]);
    ?>
</div>
<div id="inbound-items" class="table-responsive">
    <table class="table">
        <tr>
            <th><?= Yii::t('inbound/forms', 'Product Barcode'); ?></th>
            <th><?= Yii::t('inbound/forms', 'Product Model'); ?></th>
            <th><?= Yii::t('inbound/forms', 'Expected Qty'); ?></th>
            <th><?= Yii::t('inbound/forms', 'Accepted Qty'); ?></th>
            <th><?= Yii::t('inbound/forms', 'Короб клиента'); ?></th>
            <th><?= Yii::t('inbound/forms', 'Кол-во в коробе(клиента)'); ?></th>
        </tr>
        <tbody id="inbound-item-body"></tbody>
    </table>
</div>