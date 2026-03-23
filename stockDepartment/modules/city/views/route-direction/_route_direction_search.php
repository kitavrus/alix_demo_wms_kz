<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 11.07.2016
 * Time: 7:09
 */
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
        'action' => ['view','id'=>$routeDirectionModel->id],
        'method' => 'get',
        'id' => 'dr-city-search-form',
    ]); ?>

    <table class="table" width="100%" cellspacing="10" cellpadding="100">
        <tr>

            <td width="10%">
                <?= $form->field($model, 'cityId')->widget(Select2::className(),
                    [
                        'data' =>$cityArray,
                        'options' => [
                            'placeholder' => Yii::t('transportLogistics/forms', 'Select client')
                        ],
                    ]
                ) ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'regionId')->widget(Select2::className(),
                    [
                        'data' =>$regionArray,
                        'options' => [
                            'placeholder' => Yii::t('transportLogistics/forms', 'Select client')
                        ],
                    ]
                ) ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'countryId')->widget(Select2::className(),
                    [
                        'data' =>$countryArray,
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
        <?= Html::a(Yii::t('buttons', 'Очистить поиск'), ['view','id'=>$routeDirectionModel->id], ['class' => 'btn btn-warning']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>