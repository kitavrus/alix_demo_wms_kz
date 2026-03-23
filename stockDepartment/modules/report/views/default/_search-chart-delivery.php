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
//    'action' => ['index'],
    'method' => 'get',
    'id' => 'report-search-form',
]); ?>

<table class="table" width="100%" cellspacing="10" cellpadding="100">
    <tr>
        <td width="10%">
            <?= $form->field($model, 'country_id')->dropDownList(\common\modules\city\models\Country::getArrayData(),['prompt'=>''])->label('Страна') ?>
        </td>
        <td width="10%">
            <?= $form->field($model, 'city_or_shop')->dropDownList(['city'=>'По городам','shop'=>'По магазинам'])->label('Фильтр'); //'country'=>'По странам' ?>
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
    </tr>
</table>

<div class="form-group">
    <?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Yii::t('buttons', 'Очистить поиск'), ['chart-delivery'], ['class' => 'btn btn-warning']) ?>
</div>
<?php ActiveForm::end(); ?>
</div>