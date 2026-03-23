<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 11.02.2016
 * Time: 10:18
 */
use app\modules\operatorDella\models\DeliveryOrderSearch;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\Select2;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'action' => Url::toRoute('add-order'),
    'id' => 'transportation-order-form0',
    'enableClientValidation' => true,
    'validateOnType' => true,
//    'fieldConfig' => [
//        'template' => '{label}{input}{hint}{error}',
//    ],
]); ?>

<?= $form->field($modelForm, 'sender',
    [
        'template' => "{label}\n{input-group-begin}{input}{counter}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{counter}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" >
                            <span class="btn btn-warning btn-xs add-route-bt" data-value="' . Url::to(['add-store', 'client_id' => $data->id]) . '">Добавить</span>
                            <span class="btn btn-success btn-xs" data-value="' . Url::to(['edit-store']) . '" data-id="" id="edit-sender-store-bt">Изменить</span>
                            </div>',
        ]
    ]
)->widget(Select2::classname(), [
    'language' => 'ru',
    'data' => DeliveryOrderSearch::getPointsByClient($data->id),
    'options' => ['placeholder' => 'Выберите адрес отправителя'],
    'pluginOptions' => [
        'allowClear' => true
    ],
]);
?>

<?= $form->field($modelForm, 'senderContact',
    [
        'template' => "{label}\n{input-group-begin}{input}{counter}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{counter}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" >
                            <span class="btn btn-warning btn-xs add-route-bt" data-value="' . Url::to(['add-employee','client_id'=>$data->id]) . '">Добавить</span>
                            <span class="btn btn-success btn-xs" data-value="' . Url::to(['edit-employee']) . '" data-id="" id="edit-sender-contact-bt">Изменить</span>
                            </div>',
        ],
    ]
)->widget(Select2::classname(), [
    'language' => 'ru',
    'data' => ArrayHelper::map($data->employees,'id',function($m) {
        return $m->first_name.' '.$m->last_name;
    }),
    'options' => ['placeholder' => 'Выберите контакт отправителя'],
    'pluginOptions' => [
        'allowClear' => true
    ],
]);
?>

<?= $form->field($modelForm, 'recipient',
    [
        'template' => "{label}\n{input-group-begin}{input}{counter}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{counter}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" >
                                <span class="btn btn-warning btn-xs add-route-bt" data-value="' . Url::to(['add-store','client_id'=>$data->id]) . '">Добавить</span>
                                <span class="btn btn-success btn-xs" data-value="' . Url::to(['edit-store']) . '" data-id="" id="edit-recipient-store-bt">Изменить</span>
                                </div>',
        ]
    ]
)->widget(Select2::classname(), [
    'language' => 'ru',
    'data' => DeliveryOrderSearch::getPointsByClient($data->id),
    'options' => ['placeholder' => 'Выберите адрес получателя'],
    'pluginOptions' => [
        'allowClear' => true
    ],
]); ?>

<?= $form->field($modelForm, 'recipientContact',
    [
        'template' => "{label}\n{input-group-begin}{input}{counter}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{counter}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" >
                            <span class="btn btn-warning btn-xs add-route-bt" data-value="' . Url::to(['add-employee','client_id'=>$data->id]) . '">Добавить</span>
                            <span class="btn btn-success btn-xs" data-value="' . Url::to(['edit-employee']) . '" data-id="" id="edit-recipient-contact-bt">Изменить</span>
                            </div>',
        ],
    ]
)->widget(Select2::classname(), [
    'language' => 'ru',
    'data' => ArrayHelper::map($data->employees,'id',function($m) {
        return $m->first_name.' '.$m->last_name;
    }),
    'options' => ['placeholder' => 'Выберите контакт получателя'],
    'pluginOptions' => [
        'allowClear' => true
    ],
]); ?>
<?= $form->field($modelForm, 'whoPays',['addon' => ['prepend' => ['content'=>'Кто платит']]])->dropDownList($modelForm->getTransportWhoPaysArray(),['placeholder' => 'Кто платит'])->label(false); ?>
<?= $form->field($modelForm, 'typeLoading',['addon' => ['prepend' => ['content'=>'Тип Погрузки']]])->dropDownList($modelForm->getTransportTypeLoadingArray(),['placeholder' => 'Тип Погрузки'])->label(false); ?>
<?= $form->field($modelForm, 'deliveryType',['addon' => ['prepend' => ['content'=>'Тип доставки']]])->dropDownList($modelForm->getDeliveryTypeArray(),['placeholder' => 'Тип доставки'])->label(false); ?>
<?= $form->field($modelForm, 'm3',['addon' => ['prepend' => ['content'=>'M3']]])->textInput(['placeholder' => 'M3'])->label(false); ?>
<?= $form->field($modelForm, 'kg',['addon' => ['prepend' => ['content'=>'Кг']]])->textInput(['placeholder' => 'Кг'])->label(false); ?>
<?= $form->field($modelForm, 'places',['addon' => ['prepend' => ['content'=>'Мест']]])->textInput(['placeholder' => 'Мест'])->label(false); ?>
<?= $form->field($modelForm, 'price',['addon' => ['prepend' => ['content'=>'Цена']]])->textInput(['placeholder' => 'Цена'])->label(false); ?>


<?= $form->field($modelForm, 'declaredValue',['addon' => ['prepend' => ['content'=>'Заявленная стоимость']]])->textInput(['placeholder' => 'Заявленная стоимость'])->label(false); ?>


<?= $form->field($modelForm, 'description',['addon' => ['prepend' => ['content'=>'Описание груза']]])->textInput(['placeholder' => 'Описание груза'])->label(false); ?>

<?= $form->field($modelForm, 'clientId')->hiddenInput()->label(false); ?>

<?= Html::submitButton(Yii::t('buttons', 'Create'), ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end(); ?>