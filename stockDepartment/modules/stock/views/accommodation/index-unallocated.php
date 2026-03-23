<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use common\modules\stock\models\Stock;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\stock\models\StockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('stock/titles', 'Unallocated box');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lost-item-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search-filter', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'id' => 'unalloc-grid',
        'columns' => [
           ['class' => 'yii\grid\CheckboxColumn'],
            'id',
            [
                'label' => Yii::t('forms', 'Client ID'),
                'value' => function($data){
                    return $client = (!empty($data->client) ? $data->client->title : '-Не задано-');
                }
            ],
            'primary_address',
//            'secondary_address',
            [
                'label' => Yii::t('forms', 'Parent Order Number'),
                'format' => 'html',
                'value' => function($data){
                    $html='';
                    if($coo = $data->consignmentOutboundOrder){
                        $html .= 'Отгрузка: '.$coo->party_number . '<br>';
                    }
                    if($cio = $data->consignmentInboundOrder){
                        $html .= 'Поступление: '.$cio->party_number . '<br>';
                    }

                    return $html;
                }
            ],
            [
                'label' => Yii::t('stock/titles', 'Orders number'),
                'format' => 'html',
                'value' => function($data){
                    $html='';
                    if(is_object($data->outboundOrder)){
                        $html .= 'Отгрузка: '.$data->outboundOrder->order_number . '<br>';
                    }
                    if(is_object($data->inboundOrder)){
                        $html .= 'Поступление: '.$data->inboundOrder->order_number . '<br>';
                    }

                    return $html;
                }
            ],


//            [
//                'attribute'=>'actions',
//                'label' => Yii::t('outbound/forms','Actions'),
//                'format' => 'raw',
//                'value' => function($model) {
//                    $bt = '';
//                    if($model->status_lost==Stock::STATUS_LOST_PARTIAL){
//                        $bt.= \yii\helpers\Html::tag('span', Yii::t('buttons', 'Full lost'),
//                            [
//                                'class' => 'btn btn-danger',
//                                'style' => ' margin-left:10px;',
//                                'id' => 'item-lost-full-bt',
//                                'data-url-value'=>Url::to(['item-lost?id='.$model->id])
//                            ]);
//
//                    }
//
//                    $bt.=\yii\helpers\Html::tag('span', Yii::t('buttons', 'Found'),
//                        [
//                            'class' => 'btn btn-success',
//                            'style' => ' margin-left:10px;',
//                            'id' => 'item-lost-found-bt',
//                            'data-url-value'=>Url::to(['item-found?id='.$model->id])
//                        ]);
//
//                    return $bt;
//                },
//            ]
        ],
    ]); ?>

</div>
