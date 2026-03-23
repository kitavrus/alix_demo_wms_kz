<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use stockDepartment\modules\wms\assets\DeFactoAsset;
DeFactoAsset::register($this);
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
$this->title = Yii::t('wms/titles', 'Distribution || DeFacto')
?>

<?= Html::label(Yii::t('inbound/forms', 'Client ID')); ?>
<?= Html::dropDownList( 'client_id',$id,$clientsArray, [
        'prompt' => '',
        'id' => 'main-form-client-id',
        'class' => 'form-control input-lg',
        'data'=>['url'=>Url::to('/wms/default/route-form')],
        'readonly' => true,
        'name' => 'InboundForm[client_id]',
    ]
); ?>

<div id="container-defacto-process-form-layout" style="margin-top: 30px;"></div>
<div id="container-defacto-layout" style="margin-top: 30px;"></div>

<span id="buttons-menu">
<!--    --><?php //echo  Html::tag('span', Yii::t('wms/buttons', 'Get orders by API DeFacto [{0}]', ['1']),
//        [
//        'data'=>[
//            'url'=>Url::toRoute('/wms/defacto/api-de-facto/index')
//        ],
//        'class' => 'btn btn-primary btn-lg btn-href',
//        'style' => ' margin:10px;',
//    ]) ?>
<!--    --><?php //echo Html::tag('span', Yii::t('wms/buttons', 'Inbound [{0}]', ['2']),
//        [
//            'data'=>[
//                'url'=>Url::toRoute('/wms/defacto/inbound/index')
//            ],
//            'class' => 'btn btn-primary btn-lg',
//            'style' => ' margin:10px;',
//            'id' => 'inbound-process-bt'
//        ]) ?>
<!--    --><?php //echo  Html::tag('span', Yii::t('wms/buttons', 'Outbound [{0}]', ['3']),
//        [
//            'data'=>[
//                'url'=>Url::toRoute('/wms/defacto/outbound/index')
//            ],
//            'class' => 'btn btn-primary btn-lg btn-href',
//            'style' => ' margin:10px;',
//            //'id' => 'inbound-process-bt'
//        ]) ?>
<!---->
<!--    --><?php //echo Html::tag('span', Yii::t('wms/buttons', 'Cross Dock [{0}]', ['4']),
//        [
//            'data'=>[
//                'url'=>Url::toRoute('/wms/defacto/cross-dock/index')
//            ],
//            'class' => 'btn btn-primary btn-lg btn-href',
//            'style' => ' margin:10px;',
//            //'id' => 'inbound-process-bt'
//        ]) ?>
<!---->
<!--    --><?php //echo Html::tag('span', Yii::t('wms/buttons', 'Return Orders [{0}]', ['5']),
//        [
//            'data'=>[
//                'url'=>Url::toRoute('/wms/defacto/return-order/index')
//            ],
//            'class' => 'btn btn-primary btn-lg btn-ajax',
//            'style' => ' margin:10px;',
//            //'id' => 'inbound-process-bt'
//        ]) ?>
<!--</span>-->
<hr />
<h4>API v2</h4>
<hr />
<div class="row">
    <?= Html::tag('span', Yii::t('wms/buttons', 'Get orders by API DeFacto [{0}]', ['1']),
        [
            'data'=>[
                'url'=>Url::toRoute('/wms/defacto/api-de-facto-v2/index')
            ],
            'class' => 'btn btn-primary btn-lg btn-href',
            'style' => ' margin:10px;',
        ]) ?>

    <?= Html::tag('span', Yii::t('wms/buttons', 'Inbound [{0}]', ['2']),
        [
            'data'=>[
                'url'=>Url::toRoute('/wms/defacto/inbound-v2/index')
            ],
            'class' => 'btn btn-primary btn-lg',
            'style' => ' margin:10px;',
            'id' => 'inbound-process-bt'
        ]) ?>

        <?= Html::tag('span', Yii::t('wms/buttons', 'Outbound [{0}]', ['3']),
        [
            'data'=>[
                'url'=>Url::toRoute('/wms/defacto/outbound-v2/index')
            ],
            'class' => 'btn btn-primary btn-lg btn-href',
            'style' => ' margin:10px;',
            //'id' => 'inbound-process-bt'
        ]) ?>

        <?= Html::tag('span', Yii::t('wms/buttons', 'Cross Dock [{0}]', ['4']),
        [
            'data'=>[
                'url'=>Url::toRoute('/wms/defacto/cross-dock-v2/index')
            ],
            'class' => 'btn btn-primary btn-lg btn-href',
            'style' => ' margin:10px;',
            //'id' => 'inbound-process-bt'
        ]) ?>

        <?= Html::tag('span', Yii::t('wms/buttons', 'Returns [{0}]', ['5']),
        [
            'data'=>[
//                'url'=>Url::toRoute('/wms/defacto/return-order-v2/index')
                'url'=>Url::toRoute('/returnOrder/tmp-order/index')
            ],
            'class' => 'btn btn-primary btn-lg btn-href',
            'style' => ' margin:10px;',
            //'id' => 'inbound-process-bt'
        ]) ?>
        <?= Html::tag('span', Yii::t('wms/buttons', 'Размещение возвратов [{0}]', ['6']),
        [
            'data'=>[
                'url'=>Url::toRoute('/returnOrder/tmp-order/move')
            ],
            'class' => 'btn btn-primary btn-lg btn-href',
            'style' => ' margin:10px;',
            //'id' => 'inbound-process-bt'
        ]) ?>
</div>
<div id="container-defacto-outbound-layout" style="margin-top: 50px;" class="ajax-container"></div>
<iframe style="display: none" name="frame-print-alloc-list" src="#" width="468" height="468">
</iframe>
