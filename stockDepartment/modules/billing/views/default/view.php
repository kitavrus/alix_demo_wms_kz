<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use common\modules\transportLogistics\components\TLHelper;
use common\modules\audit\models\Audit;

/* @var $this yii\web\View */
/* @var $model common\modules\billing\models\TlDeliveryProposalBilling */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Billings'), 'url' => ['index', 'tariffType'=>$model->tariff_type]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-delivery-proposal-billing-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('buttons', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('buttons', 'Copy record'), ['copy', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::tag('span',
                      Yii::t('buttons', 'Recalculate').
                      '<span id="show-count"></span>'
                      ,
                      ['class' => 'btn btn-warning',
                       'id'=>'recalculate-invoice-price-bt',
                       'data-url-value'=>
                           Url::toRoute(['recalculate-invoice-price'])
                       ,
                        'data-id-value'=>$model->id
                      ]
        ) ?>
<!--        --><?//= Html::a(Yii::t('buttons', 'Recalculate'), ['recalculate-invoice-price', 'id' => $model->id], ['class' => 'btn btn-warning','id'=>'recalculate-invoice-price-bt']) ?>

        <?= Audit::haveAuditOrNot($model->id, 'TlDeliveryProposalBilling') ? Html::a(Yii::t('titles', 'Show changelog'), ['/audit/default/index', 'parent_id' => $model->id, 'classname' => 'TlDeliveryProposalBilling'], ['class' => 'btn btn-info']) : '' ?>
        <?= Html::a(Yii::t('buttons', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger pull-right',
            'data' => [
                'confirm' => Yii::t('forms', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'rule_type',
                'value' => $model->getRuleType(),
            ],
            [
                'label' => Yii::t('forms', 'From Country'),
                'value' => $model->getCountryName($model->from_country_id),
            ],
            [
                'label' => Yii::t('forms', 'From Region'),
                'value' => $model->getRegionName($model->from_region_id),
            ],
            [
                'label' => Yii::t('forms', 'From City'),
                'value' => $model->getCityName($model->from_city_id),
            ],
            [
                'label' => Yii::t('forms', 'To City'),
                'value' => $model->getCityName($model->to_city_id),
            ],
            [
                'label' => Yii::t('forms', 'From Point'),
                'value' => $model->getRouteTitle($model->route_from),
            ],
            [
                'label' => Yii::t('forms', 'To Point'),
                'value' => $model->getRouteTitle($model->route_to),
            ],
            'price_invoice_kg:currency',
            'price_invoice_kg_with_vat:currency',
            'price_invoice_mc:currency',
            'price_invoice_mc_with_vat:currency',
            'price_invoice:currency',
            'price_invoice_with_vat:currency',
            [
                'attribute' => 'status',
                'value' => $model->getStatus(),
            ],
            'delivery_term',
            [
                'attribute' => 'tariff_type',
                'value' => $model->getTariffType(),
            ],
            [
                'attribute' => 'cooperation_type',
                'value' => $model->getCooperationType(),
            ],
            [
                'attribute' => 'delivery_type',
                'value' => $model->getDeliveryType(),
            ],
            'comment:ntext',
            [
                'attribute' => 'created_user_id',
                'value' => $model::getUserName($model->created_user_id),

            ],
            [
                'attribute' => 'updated_user_id',
                'value' => $model::getUserName($model->updated_user_id),

            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>

<h1 id="title-cars">
    <?= Html::encode(Yii::t('titles','Правила для тарифа')) ?>
    <?= Html::a(Yii::t('titles', 'Добавить новое правило'), ['/billing/condition/create', 'rule_id' => $model->id], ['class' => 'btn btn-primary', 'style' => 'float:right; ',]) ?>
</h1>


<?=
GridView::widget([
    'dataProvider' => new ActiveDataProvider([
        'query' => $model->getConditions(),
    ]),
    'columns' => [
        'title',
        'formula_tariff',
        'price_invoice:currency',
        'price_invoice_with_vat:currency',
        [
            'attribute' => 'delivery_type',
            'value' => function ($model) {
                return $model->getDeliveryType();
            },
        ],
        [
            'attribute' => 'status',
            'value' => function ($model) {
                return $model->getStatus();
            },
        ],
        'sort_order',
        ['class' => 'yii\grid\ActionColumn',
            'template'=>'{update} {delete} {changelog}',
            'urlCreator'=>function( $action, $model, $key, $index) {

//                $params[0] = $action.'-manager';
//                $url = '-';

                $params = ['id'=>$model->id];
                $params[0] = '/billing/condition/' . $action;
                $url = Url::toRoute($params);
//                }

                return $url;
            },
            'buttons'=>[
                'delete'=> function ($url, $model, $key) {
                    return   Html::a(Yii::t('buttons', 'Delete'), $url, [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => Yii::t('titles', 'Are you sure you want to delete this item?'),
                            'method' => 'post',
                        ],
                    ]);
                },

                'update'=> function ($url, $model, $key) {
                    return  Html::a(Yii::t('buttons', 'Edit'), $url,['class'=>'btn btn-primary']);
                },

                'changelog'=> function ($url, $model, $key) {
                    return  Audit::haveAuditOrNot($model->id, 'TlDeliveryProposalBillingConditions') ? Html::a(Yii::t('titles', 'Show changelog'), ['/audit/default/index', 'parent_id' => $model->id, 'classname' => 'TlDeliveryProposalBillingConditions'], ['class' => 'btn btn-info']) : '';
                },
            ]
        ],
    ],
]);
?>

<script type="text/javascript">

    function sendPost(url,data) {

        $.post(url,data,function (result) {

            $('#show-count').html(' Обновлено : [ ' + result.offset + ' ИЗ ' + result.count+' ] ');

            if(result.count >= result.offset) {
                data.offset = result.offset;
                sendPost(url,data);
            } else {
                $('#show-count').html(' [ '+'Тариф испешно обновлен ] ').fadeOut( 5000 );
            }

        }).fail(function () {
            console.log("server error");
        });
    }

    $(function() {

        $('body').on('click','#recalculate-invoice-price-bt',function() {

            var url = $(this).data('url-value'),
                id = $(this).data('id-value'),
                data = {'id': id,'offset':0};

                $('#show-count').html(' [ Подождите ... ] ');
                $('#show-count').show();

                sendPost(url,data);
        });

    });

</script>