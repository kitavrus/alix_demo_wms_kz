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
            'expected_qty',
            'status_created_on_client',
            [
                'attribute'=>'actions',
                'label' => Yii::t('outbound/forms','Actions'),
                'format' => 'raw',
                'value' => function($model) {
                    $bt = '';
                    if(in_array($model->status,[\common\modules\stock\models\ConsignmentUniversal::STATUS_OUTBOUND_NEW])) {
                        $bt = \yii\helpers\Html::tag('span', Yii::t('outbound/buttons', 'Получаем данные от Дефакто'), [
                            'data' => [
                                    'url' => \yii\helpers\Url::toRoute(['get-outbound-party-items', 'id' => $model->id]),
                                    'value'=>$model->party_number,
                                    'id'=>$model->id
                            ],
                            'class' => 'btn btn-danger outbound-order-complete-bt_ get-outbound-party-items-bt',
                            'id' => 'get-outbound-party-items-bt-'.$model->id,
                        ]);
                    }

                    $hidden = 'hidden';
                    if(in_array($model->status,[\common\modules\stock\models\ConsignmentUniversal::STATUS_OUTBOUND_LOADED])) {
                        $hidden  = '';
                    }

                    $bt .= '   ' . \yii\helpers\Html::tag('span', Yii::t('outbound/buttons', 'Создаем расходные накландые'), [
                            'data' => [
                                'url' => \yii\helpers\Url::toRoute(['get-outbound-party-items-save', 'id' => $model->id]),
                                'value'=>$model->party_number,
                                'id'=>$model->id
                            ],
                            'class' => 'btn btn-info outbound-order-complete-bt_ get-outbound-party-items-save-bt '.$hidden,
                            'id' => 'get-outbound-party-items-save-bt-'.$model->id,
                        ]);

                    if(in_array($model->status,[\common\modules\stock\models\ConsignmentUniversal::STATUS_OUTBOUND_SAVED_AND_CREATE_ORDERS])) {
                       return 'в обработке на складе';
                    }

                    return $bt;
                },
            ]
        ],
    ]); ?>
<?php } ?>