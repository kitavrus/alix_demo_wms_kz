<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $inboundForm stockDepartment\modules\intermode\controllers\ecommerce\inbound\domain\InboundForm */

$this->title = Yii::t('inbound/titles', 'Создать возврат');
?>
<h1><?= Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin([
    'id' => 'scanning-returns',
    'method' => 'post',
    'action' => Url::to(['/intermode/ecommerce/inbound/returns/scanning/create-new-order']),
]); ?>

<?= $form->field($inboundForm, 'order_number')
    ->textInput(
        [
            'class' => 'form-control ext-large-input',
            'placeholder' => Yii::t('inbound/forms', 'Введите название заказа'),
        ]
    )
    ->label(Yii::t('inbound/forms', 'Название')) ?>

<?= Html::submitButton(
    Yii::t('inbound/buttons', 'Создать'),
    ['class' => 'btn btn-success']
) ?>

<?php ActiveForm::end(); ?>