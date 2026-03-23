<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\kpiSettings\models\KpiSettingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="report-search">

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    'id' => 'report-search-form',
]); ?>

<table class="table" width="100%" cellspacing="10" cellpadding="100">
    <tr>
        <td width="10%">
            <?= $form->field($model, 'id') ?>
        </td>
        <td width="10%">
            <?= $form->field($model, 'orders') ?>
        </td>
        <td width="10%">
        <?= $form->field($model, 'shipped_datetime')->widget(DateRangePicker::className(),
                [
                    'convertFormat'=>true,
                    'pluginOptions'=>[
                        'locale'=>[
                            'separator'=> ' / ',
                            'format'=>'Y-m-d',
                        ]
                    ]
//                    'pluginOptions'=>[
//                        'separator'=> ' / ',
//                        'format'=>'Y-m-d',
//                    ]
                ]
            ) ?>
        </td>
        <td width="10%">
        <?= $form->field($model, 'client_id')->widget(Select2::className(),
                [
                    'data' => \common\modules\client\models\Client::getActiveTMSItems(),
                    'options' => [
                        'placeholder' => Yii::t('transportLogistics/forms', 'Select client')
                    ],
                ]
            ) ?>
        </td>
        <td width="10%">
        <?= $form->field($model, 'agent_id')->widget(Select2::className(),
                [
                    'data' => \common\modules\transportLogistics\models\TlAgents::getActiveAgentsArray(),
                    'options' => [
                        'placeholder' => Yii::t('titles', 'Select agent'),
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]
            ) ?>
        </td>
    </tr>
</table>

<div class="form-group">
    <?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Yii::t('buttons', 'Очистить поиск'), ['index'], ['class' => 'btn btn-warning']) ?>
</div>
<?php ActiveForm::end(); ?>
</div>
