<?php
/**
 * Created by PhpStorm.
 * User: Kitavrus
 * Date: 17.04.15
 * Time: 10:45
 */
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
//use yii\bootstrap\ActiveForm;
use kartik\widgets\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
use kartik\widgets\Select2;
use common\modules\transportLogistics\components\TLHelper;

?>
<?php $this->title = Yii::t('inbound/titles', 'Generate Cross Dock Picking List'); ?>
<div class="cross-dock-process-form">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin([
            'id' => 'cross-dock-add-new-item-form',
            'enableClientValidation' => true,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
        ]
    ); ?>

    <?= $form->field($formModel, 'client_id', ['labelOptions' => ['label' => false]])->dropDownList(
        $clientsArray,
        ['prompt' => '',
            'id' => 'cross-dock-form-client-id',
            'class' => 'form-control input-lg hidden',
        ]
    ); ?>

    <?= $form->field($formModel, 'order_number')->dropDownList([],
        ['prompt' => '',
            'id' => 'cross-dock-form-order-number',
            'class' => 'form-control input-lg',
        ]
    )->label(Yii::t('inbound/forms', 'Party number')); ?>

    <?= $form->field($formModel, 'route_to')->widget(Select2::classname(), [
        'language' => 'ru',
        'data' => $route_to,
        'options' => ['placeholder' => Yii::t('transportLogistics/titles', 'Select route from')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>

    <?= $form->field($formModel, 'number_places_qty')->textInput() ?>
    <?= $form->field($formModel, 'box_m3')->textInput() ?>

    <div class="form-group">
        <?= Html::tag('span', Yii::t('inbound/buttons', 'Добавить'), [
            'class' => 'btn btn-danger pull-right',
            'style' => ' margin-left:10px;',
            'data-url' => Url::toRoute('save-new-item-cross-dock'),
            'id' => 'cross-dock-add-new-item-bt'
        ]) ?>
    </div>
    <?php ActiveForm::end(); ?>
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
</div>