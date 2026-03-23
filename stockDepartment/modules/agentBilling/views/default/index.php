<?php

use yii\helpers\Html;
use common\modules\store\models\Store;
use kartik\grid\GridView;
use kartik\grid\EditableColumn;
use kartik\grid\DataColumn;
use common\modules\transportLogistics\components\TLHelper;
use app\modules\transportLogistics\transportLogistics;
use common\modules\agentBilling\models\TlAgentBilling;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel stockDepartment\modules\billing\models\TlDeliveryProposalBillingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title =Yii::t('titles', 'Agents Billings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-agent-billing-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('buttons', 'Create Agent Billing'), ['create'],['class' => 'btn btn-success']) ?>

    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            [
                'attribute'=> 'id',
                'format'=> 'html',
                'value' => function ($data) { return Html::tag('a', $data->id, ['href'=>Url::to(['view', 'id' => $data->id]), 'target'=>'_blank']);},

            ],
            [
                'class' => DataColumn::className(),
                'attribute' => 'agent_id',
                'value' => 'agent.name',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => $agentsArray,
                    'options' => [
                        'placeholder' => Yii::t('titles', 'Select agent'),
                    ],

                ],

            ],
            [
                'attribute' => 'cash_no',
                'value' => function ($model) {
                    return $model->getPaymentMethodValue();
                },
                'filter' => $searchModel::getPaymentMethodArray(),
            ],

            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return $model->getStatusValue();
                },
                'filter' => $searchModel::getStatusArray(),
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
