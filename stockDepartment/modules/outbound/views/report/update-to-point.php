<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 08.01.15
 * Time: 7:02
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>



<?php
$this->title = Yii::t('outbound/forms', 'Edit destination for №') . $model->order_number;
$this->params['breadcrumbs'][] = ['label' => Yii::t('outbound/titles', 'Reports: orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('outbound/titles', 'Order №') . $model->order_number, 'url' => ['view', 'id' => $model->order_id]];
$this->params['breadcrumbs'][] = Yii::t('outbound/forms', 'Edit destination');
?>

<div id="messages-container">
    <div id="messages-base-line"></div>
</div>

<h1><?= $this->title; ?> </h1>

<div class="update-to-point-form">
    <?php $form = ActiveForm::begin([
            'id' => 'update-to-point-form',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
            'options' => ['enctype' => 'multipart/form-data']
        ]
    ); ?>

    <?= $form->field($model, 'to_point_id')->dropDownList($filterWidgetOptionDataRoute, ['prompt' => Yii::t('titles', 'Select')]); ?>

    <div class="row" style="margin: 20px 1px">
        <?= Html::submitButton(Yii::t('return/buttons', 'Сохранить'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
