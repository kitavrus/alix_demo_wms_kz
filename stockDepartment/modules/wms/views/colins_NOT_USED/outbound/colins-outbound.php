<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 12.01.15
 * Time: 11:36
 */

use yii\bootstrap\ActiveForm;
use \yii\helpers\Html;
use yii\bootstrap\Modal;

?>

<?php $this->title = Yii::t('inbound/titles', 'Colins outbound'); ?>

    <h1><?= $this->title ?></h1>
    <?php //форма загрузки первого файла ?>
<?php if (!$fileData) { ?>
    <div class="colins-outbound-form">
        <?php $form = ActiveForm::begin([
                'options' => ['enctype' => 'multipart/form-data'],
                'id' => 'colins-outbound-form',
                'validateOnChange' => false,
            ]
        ); ?>

        <?= $form->field($model, 'file')->fileInput() ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('buttons', 'Upload'), ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
<?php } ?>
<?php if ($fileData) { ?>
    <div class="panel panel-warning">
        <div class="panel-heading">
            <?= Yii::t('inbound/titles', 'If all files looks like correct you may confirm uploading by press "Upload" button'); ?>
        </div>
        <div class="panel-body">
            <?= Html::button(Yii::t('buttons', 'Upload file(s) in system'), ['class' => 'btn btn-danger', 'id' => 'colins-outbound-confirm-bt', 'data-url' => 'outbound-form']) ?>
        </div>

    </div>
<?php } ?>

<?php if ($fileData) { ?>
<?php $table_head = array_shift($fileData) ?>
    <div class="form-group">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
    <?php foreach ($table_head as $head) { ?>
                    <th><?= $head ?></th>
    <?php } ?>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($fileData as $row) { ?>
                <tr>
                <?php foreach ($row as $cell) { ?>

                    <td><?= $cell ?></td>

                <?php } ?>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>

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

<script>
    $('body').on('click','#colins-outbound-confirm-bt',function() {
        var me = $(this);
        $('#loading-modal').modal('show');
        window.location.href = me.data('url') + '?upload=1';
    });
</script>

