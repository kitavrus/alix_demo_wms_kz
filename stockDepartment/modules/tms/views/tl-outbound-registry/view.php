<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use yii\bootstrap\Alert;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use common\modules\audit\models\Audit;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlOutboundRegistry */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('transportLogistics/titles', 'Tl: Outbound Registries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-outbound-registry-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('buttons', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('buttons', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger pull-right',
            'data' => [
                'confirm' => Yii::t('transportLogistics/titles', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::button(Yii::t('buttons', 'Print Registry PDF'),[
            'class' => 'btn btn-warning',
            'id'=>'print-registry-pdf-btn',
            'data' => [
                'printtype'=> Yii::$app->params['printType'],
                'href' => Url::toRoute(['print-registry-pdf?id='.$model->id])
            ],
        ]) ?>
        <?= $model->canPrintTtn() ? Html::button(Yii::t('buttons', 'Print TTN'), [
            'class' => 'btn btn-warning',
            'id'=>'print-registry-ttn-btn',
            'data' => [
                'printtype'=> Yii::$app->params['printType'],
                'href' => Url::toRoute(['print-registry-ttn?id='.$model->id])
            ],
        ]) : '' ?>
        <?= Audit::haveAuditOrNot($model->id, 'TlOutboundRegistry') ? Html::a(Yii::t('titles', 'Show changelog'), ['/audit/default/index', 'parent_id' => $model->id, 'classname' => 'TlOutboundRegistry'], ['class' => 'btn btn-info']) : '' ?>
    </p>


    <?= DetailView::widget([
        'model' => $model,
        'mode' => DetailView::MODE_VIEW,
        'hover' => true,
        'attributes' => [
            'id',
            'agent_id',
            'car_id',
            'driver_name',
            'driver_phone',
            'driver_auto_number',
            'price_invoice:currency',
            'price_invoice_with_vat:currency',

            [
                'attribute' => 'created_user_id',
                'value' => $model->createdUser->username,
            ],
            [
                'attribute' => 'created_user_id',
                'value' => $model->updatedUser->username,
            ],
            'created_at:datetime',
            'updated_at:datetime',
            [
                'attribute' => 'weight',
                'valueColOptions' => [
                    'id' => 'weight-value'
                ]
            ],
            [
                'attribute' => 'volume',
                'valueColOptions' => [
                    'id' => 'volume-value'
                ]
            ],
            [
                'attribute' => 'places',
                'valueColOptions' => [
                    'id' => 'places-value'
                ]
            ],
        ],
    ]) ?>
    <br>
    <h1>Сканирование заявок на доставку</h1>

    <?php $form = ActiveForm::begin([
            'id' => 'outbound-registry-scanning-form',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
        ]
    ); ?>

    <?= $form->field($formModel, 'proposal_barcode')->textInput(['class' => 'form-control input-lg', 'data-url' => Url::toRoute('scan-proposal-barcode'), 'data-id' => $model->id]); ?>
    <?= $form->field($formModel, 'registry_id')->hiddenInput()->label(false); ?>

    <?php ActiveForm::end(); ?>
    <div id="error-container">
        <div id="error-base-line"></div>
        <?= Alert::widget([
            'options' => [
                'id' => 'error-list',
                'class' => 'alert-danger hidden',
            ],
            'body' => '',
        ]);
        ?>

        <div id="tl-delivery-outbound-items">
            <?= $this->render('_items-grid', ['model' => $model, 'storeArray' => $storeArray]) ?>
        </div>

    </div>
    <iframe style="display: none" name="print-ttn-frame" src="#" width="468" height="468">
    </iframe>
    <script type="text/javascript">

        $(function() {
            var b = $('body'),
                dBarcode = $('#registryscanningform-proposal_barcode');


            dBarcode.focus().select();

            dBarcode.on('click', function() {
                dBarcode.focus().select();
            });

            $('#outbound-registry-scanning-form').on('submit', function(e){
                e.preventDefault();
            });

            $('#print-registry-pdf-btn').on('click', function () {
                console.info('#print-registry-pdf-btn CLICK');

                    var printType = $(this).data('printtype'),
                        href =$(this).data('href');

                    if(printType == 'pdf'){
                        window.location.href = href;
                    } else if (printType == 'html'){
                        autoPrintTtn(href,2000,'0')
                    }


            });

            $('#print-registry-ttn-btn').on('click', function () {
                console.info('#print-ttn-bt CLICK');
                if(confirm('Вы точно хотите распечатать ТТН для этого реестра?')){
                    var printType = $(this).data('printtype'),
                        href =$(this).data('href');

                    if(printType == 'pdf'){
                        window.location.href = href;
                    } else if (printType == 'html'){
                        autoPrintTtn(href,30000)
                    }
                }

            });

            b.on('click', '.delete-item-btn', function (e) {

                if (confirm('Вы точно хотите удалить эту запись?')) {

                    var me = $(this),
                        url = me.data('url'),
                        id = me.data('id'),
                        form = $('#outbound-registry-scanning-form'),
                        resultGrid = $('#tl-delivery-outbound-items');

                    errorBase.setForm(form);
                    me.focus().select();


                    $.post(url, {'id':id, 'parent-id':$('#registryscanningform-proposal_barcode').data('id')},function (result) {

                        if (result.success == 0 ) {
                            errorBase.eachShow(result.errors);
                            me.focus().select();

                        } else {
                            errorBase.hidden();
                            resultGrid.html(result.data.grid);
                            $('#weight-value').find('div.kv-attribute').text(result.data.kg);
                            $('#volume-value').find('div.kv-attribute').text(result.data.mc);
                            $('#places-value').find('div.kv-attribute').text(result.data.places);
                        }
//
                    }, 'json').fail(function (xhr, textStatus, errorThrown) {

                    });
                }

                e.preventDefault();
            });

            b.on('keyup', '#registryscanningform-proposal_barcode', function (e) {

                if (e.which == 13) {

                    var me = $(this),
                        form = $('#outbound-registry-scanning-form'),
                        url = me.data('url'),
                        resultGrid = $('#tl-delivery-outbound-items');

                    errorBase.setForm(form);
                    me.focus().select();

                    $('#registryscanningform-registry_id').val(me.data('id'));

                    $.post(url, form.serialize(),function (result) {

                        console.info(result);

                        if (result.success == 0 ) {
                            errorBase.eachShow(result.errors);
                            me.focus().select();

                        } else {
                            errorBase.hidden();
                            resultGrid.html(result.data.grid);
                            $('#weight-value').find('div.kv-attribute').text(result.data.kg);
                            $('#volume-value').find('div.kv-attribute').text(result.data.mc);
                            $('#places-value').find('div.kv-attribute').text(result.data.places);
                        }
//
                    }, 'json').fail(function (xhr, textStatus, errorThrown) {

                    });
                }

                e.preventDefault();
            });

        });

    </script>
