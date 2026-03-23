<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 04.02.15
 * Time: 12:03
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
?>

<h1><?= Yii::t('outbound/titles', 'Download outbound invoice number From DeFacto API') ?></h1>

<div class="confirm-for-api-defacto-api-form">
<?php $form = ActiveForm::begin([
        'id'=>'download-confirm-for-api-form',
    ]
); ?>

<?= $form->field($model, 'client_id', ['labelOptions'=>['label'=>Yii::t('outbound/forms', 'Client ID')]])->dropDownList(
    $clientsArray,
    ['prompt'=>'',
        'id'=>'download-confirm-for-api-client-id'
    ]
); ?>

<?= $form->field($model, 'order_number', ['labelOptions'=>['label'=>Yii::t('outbound/forms', 'Parent Order Number')]])->dropDownList(
    [],
    ['prompt'=>'',
        'id'=>'download-confirm-for-api-order-number'
    ]
); ?>

<?php ActiveForm::end(); ?>
    <div class="form-group">
        <?= \yii\helpers\Html::tag('span', Yii::t('outbound/buttons', 'Download file'), ['class' => 'btn btn-primary', 'style' => ' margin-left:10px;', 'id' => 'download-confirm-outbound-print-bt','data-url-value'=>Url::to(['/outbound/default/download-file-for-de-facto-api'])]) ?>
    </div>
<div id="download-confirm-for-api-grid-orders-container"></div>
