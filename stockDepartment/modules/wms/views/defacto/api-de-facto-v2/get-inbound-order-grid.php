<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 01.04.15
 * Time: 17:45
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
use common\modules\store\models\Store;
use common\modules\stock\models\Stock;
use common\helpers\iHelper;
?>
<?= $this->render('_alertMessage',['errorMessage'=>$errorMessage]); ?>
<?php if(!empty($dataProvider)) { ?>
    <?= \yii\grid\GridView::widget([
        'tableOptions' => ['class' => 'table table-bordered'],
        'id' => 'grid-view-inbound-order-items',
        'dataProvider' => $dataProvider,
        'layout'=>'{items}',
        'pager'=>false,
        'sorter'=>false,
        'rowOptions'=> function ($model, $key, $index, $grid) {
            $class = \stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2Manager::getColorClassByClientStatus($model['status_created_on_client']);
            return ['class'=>$class];
        },
        'columns' => [
            'party_number',
            'field_extra1',
            'status_created_on_client',
            'data_created_on_client:datetime',
            [
                'attribute'=>'actions',
                'label' => Yii::t('outbound/forms','Actions'),
                'format' => 'raw',
                'value' => function($model) {
                    $bt = '';

                    if($model->status_created_on_client == \stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2::INBOUND_STATUS_NOTHING
                       && $model->status == \common\modules\stock\models\ConsignmentUniversal::STATUS_INBOUND_NEW)
                    {
                        $bt = \yii\helpers\Html::tag('span', Yii::t('outbound/buttons', 'Груз прибыл к нам на склад'), [
                            'data'=>['url'=>\yii\helpers\Url::to(['marked-inbound-party','id'=>$model->id])],
                            'class' => 'btn btn-danger defacto-marked-inbound-party-bt',
                            'id' => 'defacto-marked-inbound-party-bt-'.$model->id,
                        ]);
                    }

                    if($model->status_created_on_client == \stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2::INBOUND_STATUS_DATA_IS_PREPARED
                       && $model->status == \common\modules\stock\models\ConsignmentUniversal::STATUS_INBOUND_LOADED_FROM_API)
                    {
                        $bt = \yii\helpers\Html::tag('span', Yii::t('outbound/buttons', 'Загружаем приходную накладную к нам в систему'), [
                            'data'=>[
                                'url'=>\yii\helpers\Url::toRoute(['get-inbound-party-items','id'=>$model->id]),
                                'value'=>$model->field_extra1
                            ],
                            'class' => 'btn btn-danger outbound-order-complete-bt_ get-inbound-party-items-bt',
                            'id' => 'get-inbound-party-items-bt-'.$model->id,
                        ]);
                    }

                    if($model->status == \common\modules\stock\models\ConsignmentUniversal::STATUS_INBOUND_COMPLETE) {
                        $bt='Загружено';
                    }

//                    switch($model->status_created_on_client) {
//                        case \stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2::INBOUND_STATUS_NOTHING:
//
//                            $bt = \yii\helpers\Html::tag('span', Yii::t('outbound/buttons', 'Груз прибыл к нам на склад'), [
//                                'data'=>['url'=>\yii\helpers\Url::to(['marked-inbound-party','id'=>$model->id])],
//                                'class' => 'btn btn-danger defacto-marked-inbound-party-bt',
//                            ]);
//
//                            break;
//
//                        case \stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2::INBOUND_STATUS_DATA_IS_PREPARED:
//
//                            $bt = \yii\helpers\Html::tag('span', Yii::t('outbound/buttons', 'Загружаем приходную накладную к нам в систему'), [
//                                'data'=>[
//                                    'url'=>\yii\helpers\Url::toRoute(['get-inbound-party-items','id'=>$model->id]),
//                                    'value'=>$model->field_extra1
//                                ],
//                                'class' => 'btn btn-danger outbound-order-complete-bt_ get-inbound-party-items-bt',
//                            ]);
//
//                            break;
//                    }
                    return $bt;
                },
            ]
        ],
    ]); ?>
<?php } ?>