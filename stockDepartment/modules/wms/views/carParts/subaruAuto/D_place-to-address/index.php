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

\stockDepartment\modules\wms\assets\subaruAuto\PlaceToAddressFormAsset::register($this);

?>
<?php $this->title = "Размещение товара в адрес на полке"; ?>
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