<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 06.10.2015
 * Time: 11:04
 */
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
use yii\bootstrap\Modal;

?>
<?php $this->title = Yii::t('cross-dock/titles', 'Scanning by store'); ?>

<div class="cross-dock-process-form">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
            'id' => 'outbound-cross-dock-form',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
        ]
    ); ?>

    <?= $form->field($formModel, 'internal_barcode')->textInput(['data-url' => Url::toRoute('internal-barcode-search-by-product'), 'class' => 'form-control input-lg']); ?>
    <?= $form->field($formModel, 'product_barcode')->textInput(['data-url' => Url::toRoute('search-barcode-by-product'), 'class' => 'form-control input-lg',]); ?>
    <?= $form->field($formModel, 'scanned_product_barcodes')->textarea(['class'=>'hidden'])->label(false); ?>

    <?php ActiveForm::end(); ?>
</div>

<?php /* Html::tag('span', Yii::t('cross-dock/buttons','Print'), [
    'class' => 'btn btn-danger btn-lg',
    'id' => 'print-search-by-product-pdf-bt',
    'data' => ['url' => Url::toRoute('print-search-by-product-pdf')],
]) */?>
<br />
<br />
<div class="container-fluid">
    <div class="row" id="search-by-product-scanned-list"></div>
</div>
<br />
<div class="container-fluid">
    <div class="row" id="search-by-product-list"></div>
</div>
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