<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $inboundForm stockDepartment\modules\alix\controllers\ecommerce\returns\domain\ReturnScanningForm */

$this->title = Yii::t('inbound/titles', 'Создать возврат');
?>
<h1><?= Html::encode($this->title) ?></h1>

<?php if (($err = Yii::$app->session->getFlash('error')) !== null) { ?>
    <div class="alert alert-danger"><?= Html::encode($err) ?></div>
<?php } ?>

<?php $form = ActiveForm::begin([
    'id' => 'scanning-returns',
    'method' => 'post',
    'action' => Url::to(['/alix/ecommerce/returns/scanning/create-new-order']),
]); ?>

<?= $form->field($inboundForm, 'order_number')
    ->textInput([
        'class' => 'form-control ext-large-input',
        'placeholder' => Yii::t('inbound/forms', 'Введите название заказа'),
    ])
    ->label(Yii::t('inbound/forms', 'Название')) ?>

<?= Html::submitButton(Yii::t('inbound/buttons', 'Создать'), ['class' => 'btn btn-success']) ?>

<?php ActiveForm::end(); ?>
