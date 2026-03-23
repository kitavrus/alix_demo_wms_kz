<?php
use yii\helpers\Html;
use yii\helpers\Url;
use stockDepartment\modules\wms\assets\DeFactoAsset;


/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $orderNumberArray common\modules\inbound\models\InboundOrder */
/* @var $clientsArray common\modules\client\models\Client */
/* @var $outboundForm stockDepartment\modules\outbound\models\OutboundPickListForm */

DeFactoAsset::register($this);
$this->title = Yii::t('wms/titles', 'DeFacto Outbound Process')
?>
<?= Html::label(Yii::t('inbound/forms', 'Client ID')); ?>
<?= Html::dropDownList( 'client_id',$client_id,$clientsArray, [
        'prompt' => '',
        'id' => 'main-form-client-id',
        'class' => 'form-control input-lg',
        'data'=>['url'=>Url::to('/wms/default/route-form')],
        'readonly' => true,
        'name' => 'InboundForm[client_id]',
    ]
); ?>
<span id="buttons-menu">
    <?= Html::tag('span', Yii::t('wms/buttons', 'Print pick list [{0}]', ['1']), [
        'class' => 'btn btn-primary btn-lg',
        'style' => ' margin:10px;',
        'id' => 'outbound-print-pick-list-bt',
        'data-url' => Url::toRoute('select-and-print-picking-list')
    ]) ?>
    <?= Html::tag('span', Yii::t('wms/buttons', 'Begin end picking process [{0}]', ['2']), [
        'class' => 'btn btn-primary btn-lg',
        'style' => ' margin:10px;',
        'id' => 'begin-end-picking-list-bt',
        'data-url' => Url::toRoute('begin-end-picking-handler')
    ]) ?>
    <?= Html::tag('span', Yii::t('wms/buttons', 'Outbound scanning process [{0}]', ['3']), [
        'class' => 'btn btn-primary btn-lg',
        'style' => ' margin:10px;',
        'id' => 'scanning-process-bt',
        'data-url' => Url::toRoute('scanning-form')
    ]) ?>

<!--    --><?php /*echo Html::tag('span', Yii::t('wms/buttons', 'Outbound Order Grid [{0}]', ['4']), [
        'class' => 'btn btn-primary btn-lg btn-href',
        'style' => ' margin:10px;',
        //'id' => 'outbound-grid-list-bt',
        'data-url' => Url::toRoute('defacto-outbound-grid')
    ]) */?>
</span>

<div id="container-outbound-layout" style="margin-top: 50px;">

</div>
<iframe style="display: none" name="frame-print-alloc-list" src="#" width="468" height="468">
</iframe>