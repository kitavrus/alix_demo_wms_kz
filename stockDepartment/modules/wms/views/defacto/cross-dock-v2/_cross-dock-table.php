<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
?>
<?php if ($crossOrders) { ?>
    <?php $form = ActiveForm::begin([
            'id' => 'cross-dock-apply-qty-form',
            'enableClientValidation' => true,
            'validateOnChange' => true,
            'validateOnSubmit' => false,
            'options' => [
                'data-printType' => \Yii::$app->params['printType']
            ]
        ]
    ); ?>
    <table class="table table-bordered table-striped">
        <tr>
            <th>Название магазина</th>
            <th>Предполагаемое кол-во</th>
            <th>Действительное кол-во</th>
            <th>M3</th>
            <th>Действие</th>
        </tr>
        <?php foreach ($crossOrders as $co) { ?>
            <?php if ($store = $co->pointTo) { ?>
            <?php $applyQtyForm->expected_number_places_qty = $co->expected_number_places_qty; ?>
            <?php $applyQtyForm->accepted_number_places_qty = empty($co->accepted_number_places_qty) ? $co->expected_number_places_qty : $co->accepted_number_places_qty; ?>
            <?php $applyQtyForm->box_m3 = $co->box_m3; ?>
                <tr id="row-cd-apply-<?php echo $co->id; ?>">
                    <td><?= $store->getPointTitleByPattern('{shopping_center_name} / {city_name} / {shop_code}') ?></td>
                    <td> <?= $form->field($applyQtyForm, '['.$co->id.']expected_number_places_qty' )->textInput(['class'=>'exp-qty form-control input-sm','data-id' => $co->id])->label(false);?></td>
                    <td> <?= $form->field($applyQtyForm, '['.$co->id.']accepted_number_places_qty' )->textInput(['class'=>'acc-qty form-control input-sm','data-id' => $co->id])->label(false);?></td>
                    <td> <?= $form->field($applyQtyForm, '['.$co->id.']box_m3' )->textInput(['class'=>'box-m3 form-control input-sm','data-id' => $co->id])->label(false);?></td>
                    <td><?= Html::tag('span', Yii::t('inbound/buttons', 'Принять'), ['class' => 'btn btn-danger pull-right', 'style' => ' margin-right:20px;', 'data-url' => Url::toRoute(['apply-by-one-shop','id'=> $co->id]),'data-id' => $co->id, 'id' => 'cross-dock-apply-by-one-shop-bt']) ?></td>

<!--                    <td>--><?php //echo $co->expected_number_places_qty ?><!--</td>-->
<!--                    <td>--><?php //echo Html::input('text', 'expected_number_places_qty', $co->expected_number_places_qty, ['class'=>'exp-qty form-control input-sm', 'data-id'=>$co->id])?><!--</td>-->
<!--                    <td>--><?php //echo Html::input('text', 'accepted_qty', $co->expected_number_places_qty, ['class'=>'acc-qty form-control input-sm', 'data-id'=>$co->id])?><!--</td>-->
                </tr>

            <?php } ?>
        <?php } ?>
    </table>
    <?php ActiveForm::end(); ?>
    <div class="form-group">
        <?= Html::tag('span', Yii::t('inbound/buttons', 'Принять'), ['class' => 'btn btn-danger pull-right', 'style' => ' margin-right:20px;', 'data-url' => Url::toRoute('apply-qty'), 'id' => 'cross-dock-confirm-bt']) ?>
    </div>
<?php }  ?>