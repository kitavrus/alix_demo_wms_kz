<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use common\modules\transportLogistics\components\TLHelper;
use common\modules\audit\models\Audit;
use kartik\grid\DataColumn;
use kartik\grid\EditableColumn;
use common\modules\transportLogistics\models\TlAgents;

/* @var $this yii\web\View */
/* @var $model common\modules\billing\models\TlDeliveryProposalBilling */

$this->title = Yii::t('titles', 'Agent Billing'). ' № ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Agents Billings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-agent-billing-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('buttons', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
<?= Audit::haveAuditOrNot($model->id, 'TlAgentBilling') ? Html::a(Yii::t('titles', 'Show changelog'), ['/audit/default/index', 'parent_id' => $model->id, 'classname' => 'TlAgentBilling'], ['class' => 'btn btn-info']) : '' ?>
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
                'attribute' => 'agent_id',
                'value' => $model->agent->name,
            ],
            [
                'attribute' => 'status',
                'value' => $model->getStatusValue(),
            ],
            [
                'attribute' => 'cash_no',
                'value' => $model->getPaymentMethodValue(),
            ],

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
    <?= Html::a(Yii::t('titles', 'Добавить новое правило'), ['/agentBilling/condition/create', 'tl_agents_billing_id' => $model->id], ['class' => 'btn btn-primary', 'style' => 'float:right; ',]) ?>
</h1>


<?=
GridView::widget([
    'dataProvider' => new ActiveDataProvider([
        'query' => $model->getConditions(),
    ]),
    'columns' => [

        [

            'attribute' => 'route_from',
//            'value' => 'routeFrom.title',
            'value' =>  function ($data) use ($storeArray) { return isset($storeArray[$data->route_from]) ? $storeArray[$data->route_from] : '-';}
        ],
        [
            'attribute' => 'route_to',
//            'value' => 'routeTo.title',
            'value' =>  function ($data) use ($storeArray) { return isset($storeArray[$data->route_to]) ? $storeArray[$data->route_to] : '-';}
        ],
        [

            'attribute' => 'rule_type',
            'value' => function ($model) {
                return $model->getRuleType();
            },
        ],
        [

            'attribute' => 'transport_type',
            'value' => function ($model) {
                return $model->getTransportTypeValue();
            },
        ],

        [
            'attribute' => 'price_invoice',
            'value' => function ($model) {
                return $model->price_invoice;
            },
            'format' => 'currency',
            'visible' => $model->agent->flag_nds == TlAgents::FLAG_NDS_FALSE || $model->agent->flag_nds == TlAgents::FLAG_NDS_UNDEFINED ? 1 : 0,
        ],
        [
            'attribute' => 'price_invoice_with_vat',
            'value' => function ($model) {
                return $model->price_invoice_with_vat;
            },
            'format' => 'currency',
            'visible' => $model->agent->flag_nds == TlAgents::FLAG_NDS_TRUE ? 1 : 0,
        ],
        [
            'attribute' => 'status',
            'value' => function ($model) {
                return $model->getStatusValue();
            },
        ],
        ['class' => 'yii\grid\ActionColumn',
            'template'=>'{update} {delete} {changelog}',
            'urlCreator'=>function( $action, $model, $key, $index) {

//                $params[0] = $action.'-manager';
//                $url = '-';

                $params = ['id'=>$model->id];
                $params[0] = '/agentBilling/condition/' . $action;
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
                    return  Audit::haveAuditOrNot($model->id, 'TlAgentBillingConditions') ? Html::a(Yii::t('titles', 'Show changelog'), ['/audit/default/index', 'parent_id' => $model->id, 'classname' => 'TlAgentBillingConditions'], ['class' => 'btn btn-info']) : '';
                },
            ]
        ],
    ],
]);
?>
