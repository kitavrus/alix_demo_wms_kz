<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 08.01.15
 * Time: 7:02
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $clientsArray common\modules\client\models\Client */
/* @var $boxToBoxForm common\clientObject\main\inbound\forms\BoxToBoxForm */

\app\modules\ecommerce\assets\defacto\ProductToBoxFormAsset::register($this);

?>
<?php $this->title = "Товар из короба в короб"; ?>
<a href="<?= Url::to('/ecommerce/defacto/change-address-place/index')?>" class="btn btn-primary">короб на полку</a>
<a href="<?= Url::to('/ecommerce/defacto/change-address-place/box-to-box')?>" class="btn btn-primary">все товары из короба в короб</a>
<h1><?= $this->title ?></h1>
<?= Alert::widget([
    'options' => [
        'id' => 'success-list',
        'class' => 'alert-success hidden',
    ],
    'body' => '',
]);
?>
<div class="scan-inbound-form">
    <?php $form = ActiveForm::begin([
            'id' => 'producttoboxform',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
        ]
    ); ?>


    <?= $form->field($boxToBoxForm, 'fromBox')->textInput([
        'class' => 'form-control input-lg',
        'data-url' => Url::to('scan-product-from-box'),
    ]); ?>

    <?= $form->field($boxToBoxForm, 'productBarcode')->textInput([
        'class' => 'form-control input-lg',
        'data-url' => Url::to('scan-product-barcode'),
    ]); ?>

    <?= $form->field($boxToBoxForm, 'toBox')->textInput([
        'class' => 'form-control input-lg',
        'data-url' => Url::to('scan-product-to-box'),
    ]); ?>

    <?php ActiveForm::end(); ?>

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