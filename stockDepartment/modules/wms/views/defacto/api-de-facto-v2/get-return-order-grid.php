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
            'accepted_qty',
//            'expected_number_places_qty',
            [
                'attribute'=>'actions',
                'label' => Yii::t('outbound/forms','Actions'),
                'format' => 'raw',
                'value' => function($model) {
                    return  \yii\helpers\Html::tag('span',
                                Yii::t('outbound/buttons', 'Загружаем возвратную накладную к нам в систему').' '.'<span class="loading"></span>',
                                [
                                    'data'=>['url'=>\yii\helpers\Url::toRoute(['save-return-party-items','id'=>$model->id])],
                                    'class' => 'btn btn-warning get-return-party-items-bt',
                                ]);
                },
            ]
        ],
    ]); ?>
<?php } ?>