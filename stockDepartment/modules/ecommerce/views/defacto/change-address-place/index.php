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
/* @var $orderNumberArray common\modules\inbound\models\InboundOrder */
/* @var $clientsArray common\modules\client\models\Client */
/* @var $inboundForm stockDepartment\modules\inbound\models\InboundForm */

\app\modules\ecommerce\assets\defacto\PlaceToAddressFormAsset::register($this);

?>
<?php $this->title = "Размещение товара в адрес на полке"; ?>
<a href="<?= Url::to('/ecommerce/defacto/change-address-place/product-to-box')?>" class="btn btn-primary">Товар из короба в короб</a>
<a href="<?= Url::to('/ecommerce/defacto/change-address-place/box-to-box')?>" class="btn btn-primary">все товары из короба в короб</a>
<h1><?= $this->title ?></h1>

<div class="scan-inbound-form">
    <?php $form = ActiveForm::begin([
            'id' => 'boxtoplaceform',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
        ]
    ); ?>


    <?= $form->field($placeToAddressForm, 'fromPlaceAddress')->textInput([
        'class' => 'form-control input-lg',
        'data-url' => Url::to('scan-box-barcode'),
    ]); ?>

    <?= $form->field($placeToAddressForm, 'toPlaceAddress')->textInput([
        'class' => 'form-control input-lg',
        'data-url' => Url::to('scan-place-address'),
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