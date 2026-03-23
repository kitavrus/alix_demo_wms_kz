<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use app\modules\transportLogistics\transportLogistics;
use common\modules\audit\models\Audit;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlAgents */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('transportLogistics/titles', 'Tl Agents'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="tl-agents-view">

        <h1>
            <?= Html::encode($this->title) ?>

            <?= Html::a(Yii::t('transportLogistics/buttons', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

            <?=
            Html::a(Yii::t('transportLogistics/buttons', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('transportLogistics/titles', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>

            <?= Html::a(Yii::t('transportLogistics/titles', 'Cars'), '#title-cars') ?>

            <?= Audit::haveAuditOrNot($model->id, 'TlAgents') ? Html::a(Yii::t('titles', 'Show changelog'), ['/audit/default/index', 'parent_id' => $model->id, 'classname' => 'TlAgents'], ['class' => 'btn btn-info']) : '' ?>

            <?= Html::a(Yii::t('transportLogistics/buttons', 'Add Car'), ['create-car', 'agent_id' => $model->id], ['class' => 'btn btn-primary', 'style' => 'float:right; ',]) ?>
            <?= Html::a(Yii::t('transportLogistics/buttons', 'Напечатать ШК'), ['print-barcode', 'id' => $model->id], ['class' => 'btn btn-primary', 'style' => 'margin-left:10px; ',]) ?>
        </h1>


        <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                'name',
//                'title',
                'phone',
                'phone_mobile',
                'description:ntext',
                [
                    'attribute' => 'status',
                    'value' => $model->getStatusArray($model->status),
                ],
                [
                    'attribute' => 'flag_nds',
                    'value' => $model->getNdsFlagValue(),
                ],
                [
                    'attribute' => 'payment_period',
                    'value' => $model->getPaymentPeriodValue(),
                ],
                'contact_first_name',
                'contact_middle_name',
                'contact_last_name',
                'contact_phone',
                'contact_phone_mobile',
                'contact_first_name2',
                'contact_middle_name2',
                'contact_last_name2',
                'contact_phone2',
                'contact_phone_mobile2',
                'address_title',
                'country_id',
                'region_id',
                'city_id',
                'zip_code',
                'street',
                'house',
                'entrance',
                'flat',
                'intercom',
                'floor',
                'comment:ntext',
                'created_at:datetime',
                'updated_at:datetime',
            ],
        ]) ?>
    </div>
    <h1 id="title-cars">
        <?= Html::encode(Yii::t('transportLogistics/titles','Cars')) ?>
        <?= Html::a(Yii::t('transportLogistics/buttons', 'Add Car'), ['create-car', 'agent_id' => $model->id], ['class' => 'btn btn-primary', 'style' => 'float:right; ',]) ?>
    </h1>
<?=
GridView::widget([
    'dataProvider' => new ActiveDataProvider([
            'query' => $model->getCars(),
        ]),
    'columns' => [
        'title',
        'name',
        [
            'attribute' => 'status',
            'value' => function ($model) {
                    return $model::getStatusArray($model->status);
                },
        ],
        'mc',
        'kg',

        ['class' => 'yii\grid\ActionColumn',
            'template'=>'{update} {delete}',
            'urlCreator'=>function( $action, $model, $key, $index) {
                    $params = ['id'=>$model->id];
                    $params[0] = $action.'-car';

                    return Url::toRoute($params);
                }
        ],
    ],
]); ?>


    <h1 id="title-cars">
        <?= Html::encode(Yii::t('titles','Менеджеры компании')) ?>
        <?= Html::a(Yii::t('titles', 'Добавить менеджера'), ['/tms/agent-employee/create', 'tl_agent_id' => $model->id], ['class' => 'btn btn-primary', 'style' => 'float:right; ',]) ?>
    </h1>
<?=
GridView::widget([
    'dataProvider' => new ActiveDataProvider([
        'query' => $model->getEmployees(),
    ]),
    'columns' => [
        'username',
//         'first_name',
//         'middle_name',
//         'last_name',
        'phone',
        'phone_mobile',
        'email:email',
        [
            'attribute' => 'manager_type',
            'value' => function ($model) {
                return $model->getType();
            },
        ],
        [
            'attribute' => 'status',
            'value' => function ($model) {
                return $model->getStatus();
            },
        ],
        ['class' => 'yii\grid\ActionColumn',
            'template'=>'{update} {delete}',
            'urlCreator'=>function( $action, $model, $key, $index) {

//                $params[0] = $action.'-manager';
//                $url = '-';

                $params = ['id'=>$model->id];
                $params[0] = '/tms/agent-employee/' . $action;
                $url = Url::toRoute($params);
//                }

                return $url;
            },
            'buttons'=>[
                'delete'=> function ($url, $model, $key) {
                    $a =  Html::a(Yii::t('forms', 'Delete'), $url, [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => Yii::t('forms', 'Are you sure you want to delete this item?'),
                            'method' => 'post',
                        ],
                    ]);
                    return $model->manager_type != $model::TYPE_BASE_ACCOUNT ? $a : '';
                },
//                'delete'=> function ($url, $model, $key) {
//                    return $model->manager_type != $model::TYPE_BASE_ACCOUNT ? Html::a('Delete', $url,['class'=>'btn btn-danger']) : '';
//                },
                'update'=> function ($url, $model, $key) {
                    return $model->manager_type != $model::TYPE_BASE_ACCOUNT ? Html::a('Update', $url,['class'=>'btn btn-primary']) : '';
                },
            ]
        ],
    ],
]);
?>