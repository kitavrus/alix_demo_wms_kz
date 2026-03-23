<?php
/**
* Created by PhpStorm.
* User: KitavrusAdmin
* Date: 10.04.2017
* Time: 21:24
*/
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use app\modules\returnOrder\formHtml\AccommodationReturnForm;

\app\modules\returnOrder\assets\ReturnTmpOrderAsset::register($this);
?>
<?php $this->title = Yii::t('stock/titles', 'Accommodation'); ?>
<h1 class="text-center">ЭТО РАЗМЕЩЕНИЕ ТОЛЬКО ДЛЯ ВОЗВРАТОВ</h1>
<div class="container">
<div class="row">
    <div class="col-md-offset-4 col-md-4">

        <div id="messages-container">
            <div id="messages-base-line"></div>
            <?= Alert::widget([
                'options' => [
                    'id' => 'messages-list',
                    'class' => 'alert-info hidden',
                ],
                'body' => '<span id="messages-list-body"></span>',
            ]);
            ?>
        </div>

        <div class="stock-accommodation-form">
            <?php $form = ActiveForm::begin([
                    'id' => 'stock-accommodation-process-form',
//                        'class' => 'text-center',
                    'enableClientValidation' => false,
                    'validateOnChange' => false,
                    'validateOnSubmit' => false,
                ]
            ); ?>
            <?= $form->field($af, 'type')->hiddenInput()->label(false); ?>
            <?= $form->field($af, 'from', ['labelOptions' => ['id' => 'from-label', "style" => "width:50%"]])->textInput(); ?>
            <?= $form->field($af, 'to', ['labelOptions' => ['id' => 'to-label', "style" => "width:50%"]])->textInput(); ?>

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
    </div>
</div>