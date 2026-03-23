<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 08.01.15
 * Time: 7:02
 */

use stockDepartment\modules\wms\managers\erenRetail\placement\PlaceToAddressFormAsset;
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

PlaceToAddressFormAsset::register($this);

?>
<?php $this->title = "Короб на полку"; ?>
<a href="<?= Url::to('/wms/erenRetail/place-to-address/box-to-box')?>" class="btn btn-primary">из короба в короб</a>
<a href="<?= Url::to('/wms/erenRetail/place-to-address/product-in-box-to-box')?>" class="btn btn-primary">из короба товар в короб</a>
<h1><?= $this->title ?></h1>

<div class="scan-inbound-form">
    <?php $form = ActiveForm::begin([
            'id' => 'placetoaddressform',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
        ]
    ); ?>


    <?= $form->field($placeToAddressForm, 'fromPlaceAddress')->textInput([
        'class' => 'form-control input-lg',
        'data-url' => Url::to('scan-from-placement-unit-address'),
    ]); ?>

    <?= $form->field($placeToAddressForm, 'toPlaceAddress')->textInput([
        'class' => 'form-control input-lg',
        'data-url' => Url::to('scan-to-place-address'),
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