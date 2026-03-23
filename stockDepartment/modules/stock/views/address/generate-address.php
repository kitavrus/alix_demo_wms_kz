<?php

use yii\bootstrap\Alert;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\stock\models\RackAddressSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $generateModel stockDepartment\modules\stock\models\GenerateAddressForm */
/* @var $printModel stockDepartment\modules\stock\models\PrintAddressForm */

$this->title = Yii::t('stock/titles', 'Generate address');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php foreach (Yii::$app->session->getAllFlashes(true) as $type => $messages): ?>
    <?php
    if (!is_array($messages)) {
        $messages = [$messages];
    }

    $bootstrapClassMap = [
        'error' => 'alert-danger',
        'success' => 'alert-success',
        'info' => 'alert-info',
        'warning' => 'alert-warning',
    ];

    $alertClass = $bootstrapClassMap[$type] ?: 'alert-info';

    foreach ($messages as $message):
        ?>
        <?= Alert::widget([
        'options' => ['class' => $alertClass],
        'body' => $message,
    ]) ?>
    <?php endforeach; ?>
<?php endforeach; ?>

<div class="generate-address">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="search-form">
        <?= $this->render('_search-filter', [
            'model' => $searchModel,
        ]) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => null,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn'
            ],
            'address'
        ],
    ]); ?>

    <div class="form-group">
        <?php
        $formGenerate = ActiveForm::begin([
            'id' => 'generate-address-form',
            'action' => ['generate-address'],
        ]);
        ?>

        <div class="form-group">
            <h4>Генерация адреса</h4>
            <div class="row">
                <?= $this->render('_address-fields', ['form' => $formGenerate, 'model' => $generateModel]) ?>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 24px;">
            <?= Html::submitButton(Yii::t('buttons', 'Generate'), [
                'class' => 'btn btn-success',
                'style' => 'min-width: 150px;',
            ]) ?>
        </div>

        <?php ActiveForm::end(); ?>

        <?php
        $formPrint = ActiveForm::begin([
            'id' => 'print-range-form',
            'action' => ['print-address'],
        ]);
        ?>

        <div class="form-section">
            <h4>Распечатать диапазон</h4>
            <div class="row">
                <?= $this->render('_address-fields', ['form' => $formPrint, 'model' => $printModel]) ?>
            </div>

            <?= $formPrint->field($printModel, 'printSize')->dropDownList(
                array_combine($printModel->getPrintTypesValues(), $printModel->getPrintTypesValues())
            ) ?>
        </div>

        <div class="form-group" style="margin-top: 28px;">
            <?= Html::submitButton(Yii::t('buttons', 'Print label'), [
                'class' => 'btn btn-warning',
                'style' => 'min-width: 150px;',
            ]) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>