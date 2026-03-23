<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\modules\stock\models\Inventory */

$this->title = $model->order_number;
$this->params['breadcrumbs'][] = ['label' => Yii::t('inventory/forms', 'Inventories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?php if($model->status != \common\ecommerce\entities\EcommerceInventory::STATUS_DONE) { ?>

            <?php if($model->status == \common\ecommerce\entities\EcommerceInventory::STATUS_NEW) { ?>
                <?= Html::a(Yii::t('inventory/forms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?php } ?>

            <?= Html::a(Yii::t('inventory/forms', 'Start 1'), ['start', 'id' => $model->id], ['class' => 'btn btn-danger','data' => [
                'confirm' => Yii::t('app', 'Вы действительно хотите начать инвентаризацию?'),
            ]]) ?>

            <?php echo Html::a(Yii::t('buttons', 'Full inventory report').' EXCEL',['print-full-report-excel', 'id' => $model->id], ['class' => 'btn btn-info pull-right-', 'style' => 'margin-left-:15px;', 'id' => 'print-inventory-report-excel-bt','data-url'=>Url::to(['print-full-report-excel'])]) ?>

<!--            --><?php //echo Html::a(Yii::t('buttons', 'Full inventory report').' PDF',['print-full-report-pdf', 'id' => $model->id], ['class' => 'btn btn-info pull-right-', 'style' =>  'margin-left-:15px;', 'id' => 'print-inventory-report-pdf-bt','data-url'=>Url::to(['print-full-report-pdf'])]) ?>

            <?php echo Html::a(Yii::t('buttons', 'Full inventory accepted report').' EXCEL',['print-full-report-accepted-excel', 'id' => $model->id], ['class' => 'btn btn-info pull-right-', 'style' =>  'margin-left-:15px;', 'id' => 'print-inventory-report-accepted-excel-bt','data-url'=>Url::to(['print-full-report-accepted-excel'])]) ?>

            <?php echo Html::a(Yii::t('buttons', 'Full inventory accepted with address').' EXCEL',['print-full-address-report-accepted-excel', 'id' => $model->id], ['class' => 'btn btn-info pull-right-', 'style' =>  'margin-left-:15px;', 'id' => 'print-inventory-report-address-accepted-excel-bt','data-url'=>Url::to(['print-full-address-report-accepted-excel'])]) ?>

<!--            --><?php //echo Html::a(Yii::t('buttons', 'Full inventory accepted report').' PDF',['print-full-report-accepted-pdf', 'id' => $model->id], ['class' => 'btn btn-info pull-right-', 'style' =>  'margin-left-:15px;', 'id' => 'print-inventory-report-accepted-pdf-bt','data-url'=>Url::to(['print-full-report-accepted-pdf'])]) ?>

            <?php echo Html::a(Yii::t('app', 'Подготовить для размещения 2'), ['remove-secondary-address', 'id' => $model->id], ['class' => 'btn btn-danger','data' => [
                'confirm' => Yii::t('app', 'Вы действительно хотите удалить адреса полок и начать размещение?'),
            ]]) ?>

            <?php echo Html::a(Yii::t('app', 'Удалить ненайденные товары 3'), ['remove-lost', 'id' => $model->id], ['class' => 'btn btn-danger','data' => [
                'confirm' => Yii::t('app', 'Вы действительно хотите удалить ненайденные товары?'),
            ]]) ?>

            <?php echo Html::a(Yii::t('inventory/forms', 'End 4'), ['end', 'id' => $model->id], ['class' => 'btn btn-danger','data' => [
                'confirm' => Yii::t('app', 'Вы действительно хотите закончить инвентаризацию?'),
            ]]) ?>

        <?php } ?>

        <!--        --><?php /*echo Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) */?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute'=>'client_id',
                'value'=> (!empty($model->client) ? $model->client->title : ''),
//                'value'=>$model->client->title,
            ],
            'order_number',
            'expected_qty',
            [
                'attribute'=>'accepted_qty',
                'value'=>function ($model) {

                    $calc = $model->expected_qty - $model->accepted_qty;
                    $percent = 100 - ( $model->accepted_qty / ($model->expected_qty / 100)) ;
                    return $model->accepted_qty." Осталось: ".$calc." / ".round($percent,2)."%";
                },
            ],
            'expected_places_qty',
            [
                'attribute'=>'status',
                'value'=>$model->getStatusValue(),
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

<?= GridView::widget([
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query' => $model->getRows(),
        'pagination' => [
          'pageSize' => 3000,
      ],
    ]),
    'rowOptions'=> function ($model, $key, $index, $grid) {
            $class = '';
            if($model->expected_qty == $model->accepted_qty) {
                $class = 'color-dark-olive-green';
            }

        return ['class'=>$class];
    },
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'row_number',
        'floor_number',
        'column_number',
        'level_number',
        'expected_qty',
        [
            'attribute'=>'accepted_qty',
            'contentOptions'=>function ($m){
                return ['id'=>'accepted-qty-'.$m->id];
            }
        ],
		[
            'attribute'=>'diff',
            'label'=>'Осталось',
            'format'=>'raw',
            'value'=>function ($model) {
//                $model->accepted_qty = 1349;

                $calc = $model->expected_qty - $model->accepted_qty;
                $percent = 100 - ( $model->accepted_qty / ($model->expected_qty / 100)) ;
                return $calc." / ".round($percent,2)."%";
            },
        ],
        [
            'attribute'=>'status',
            'value'=>function ($model) { return $model->getStatusValue(); },
        ],
        [
            'attribute'=>'buttons',
            'label'=>'Действия',
            'format'=>'raw',
            'value'=> function($model){
                $update = Html::a(Yii::t('inventory/forms', 'Обновить'), ['update-accepted-in-row', 'id' => $model->id], ['class' => 'btn btn-danger','data' => [
                    'confirm' => Yii::t('app', 'Вы действительно хотите обновить ряд?'),
                ]]);

                $expected = Html::a(Yii::t('inventory/forms', 'отскан-е'), ['print-accepted-list', 'id' => $model->id], ['class' => 'btn btn-warning']);

                $accepted = Html::a(Yii::t('inventory/forms', 'не отскан-е'), ['print-diff-list', 'id' => $model->id], ['class' => 'btn btn-info']);

                return $update.' '.$expected.' '.$accepted;
            }
        ]
    ],
]); ?>
