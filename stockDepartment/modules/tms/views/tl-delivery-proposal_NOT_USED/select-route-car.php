<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use common\modules\transportLogistics\components\TLHelper;
use app\modules\transportLogistics\transportLogistics;

/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 01.10.14
 * Time: 16:41
 */
?>

<div class="tl-select-route-car-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'route_car_id'
        , [
//            'template' => "{label}\n{input-group-begin}{input}{counter}{input-group-end}\n{hint}\n{error}\n",
//            'parts' => [
//                '{input-group-begin}' => '<div class="input-group">',
//                '{input-group-end}' => '</div>',
//                '{counter}' => '<div class="input-group-addon" > <span class="btn btn-info btn-xs update-route-car-bt" data-value="' . Url::to(['/tms/default/update-route-car']) . '">Редактировать</span> </div>',
//            ]
        ]
    )->dropDownList(TLHelper::getFreeCarByCity($dpr_city_from, $dpr_city_to,$excludeUsedId),['prompt'=>Yii::t('titles','Пожалуйста выберите машину')]); //,[$dpr_city_from,$dpr_city_to]  ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('transportLogistics/buttons', 'Add Car'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('transportLogistics/buttons', 'Cancel'),['view', 'id' => $modelDpRoute->tl_delivery_proposal_id,'#'=>'title-order'], ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <div id="selected-route-car-info">
        <table>

        </table>
    </div>

</div>

<script type="text/javascript">
    $(function(){
            $('#selectroutecar-route_car_id').on('change',function(){
               console.log($(this));
                var id = $(this).val();
                if(id) {
                    $.get('/tms/tl-delivery-proposal-route-cars/get-route-car-info', {'id': id}, function (data) {
                        $('#selected-route-car-info table').html(data);
                    });
                }
            });
    });
</script>