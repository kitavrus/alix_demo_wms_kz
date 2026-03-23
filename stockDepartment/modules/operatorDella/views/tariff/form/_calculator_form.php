<?php
use kartik\widgets\ActiveForm;
use common\modules\billing\models\TlDeliveryProposalBilling;
use yii\helpers\Html;

     $form = ActiveForm::begin([
        'id' => 'calculator_form'
    ]); ?>
    <?= $form->field($model, 'city_from',['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-map-marker"></i>']]])
        ->dropDownList($model::getDefaultRoutesFrom(TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_DEFAULT),['prompt'=>'Выберите город']) ?>
    <?= $form->field($model, 'city_to',['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-map-marker"></i>']]])
        ->dropDownList($model::getDefaultRoutesTo(TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_DEFAULT),['prompt'=>'Выберите город']) ?>
    <?= $form->field($model, 'delivery_type')
        ->dropDownList($model::getDeliveryTypeArray(),['prompt'=>'Выберите тип доставки']) ?>
    <?= $form->field($model, 'weight',['addon' => ['append'=>['content'=>'кг']]])->textInput(['maxlength' => 128,'class'=>'col-md-4']) ?>
    <?= $form->field($model, 'volume',['addon' => ['append'=>['content'=>'м³']]])->textInput(['maxlength' => 128]) ?>
    <?= $form->field($model, 'places')->textInput(['maxlength' => 128]) ?>


        <?= Html::button(Yii::t('frontend/buttons', 'Calculate'), ['class' => 'btn btn-primary', 'id'=>'calculate', 'type'=>'submit']) ?>

        <?= Html::button(Yii::t('frontend/buttons', 'Clear'), ['class' => 'btn btn-warning', 'id'=>'clear']) ?>

        <?= Html::button(Yii::t('frontend/buttons', 'Add order'), ['class' => 'btn btn-danger', 'id'=>'make-order']) ?>

        <?= Html::button(Yii::t('frontend/buttons', 'Tariff PDF export'), ['class' => 'btn btn-danger link-bt', 'data-url'=>'export-pdf']) ?>

        <?//= Html::button(Yii::t('frontend/buttons', 'Оформить заявку'), ['class' => 'btn btn-lg btn-danger pull-right', 'id'=>'make-order', 'data-href'=>'/order/default/make-order']) ?>


    <?php ActiveForm::end(); ?>
