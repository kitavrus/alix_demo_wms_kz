<?php
use yii\helpers\Html;
use stockDepartment\modules\wms\assets\DeFactoAsset;
use yii\helpers\Url;

DeFactoAsset::register($this);
$this->title = Yii::t('wms/titles', 'Cross-dock DeFacto');
?>

<?= Html::label(Yii::t('inbound/forms', 'Client ID')); ?>
<?= Html::dropDownList( 'client_id',\common\modules\client\models\Client::CLIENT_DEFACTO,$clientsArray, [
        'prompt' => '',
        'id' => 'main-form-client-id',
        'class' => 'form-control input-lg',
        'data'=>['url'=>Url::to('/wms/default/route-form')],
        'readonly' => true,
        'name' => 'InboundForm[client_id]',
    ]
); ?>
<h1><?=$this->title?></h1>
<span id="buttons-menu">
    <?= Html::tag('span', Yii::t('wms/buttons', 'Print Cross Dock Picking List [{0}]', [1]), [
        'class' => 'btn btn-primary btn-lg',
        'style' => 'margin:10px;',
        'id' => 'print-cross-dock-list-bt',
        'data' => ['url' => Url::toRoute('generate-cross-dock')],
    ]) ?>

    <?= Html::tag('span', Yii::t('wms/buttons', 'Scanning cross-dock [{0}]', [2]), [
        'class' => 'btn btn-danger btn-lg',
        'style' => ' margin:10px;',
        'id' => 'outbound-cross-dock-form-bt',
        'data' => ['url' => Url::toRoute('outbound-form')],
    ]) ?>

    <?= Html::tag('span', Yii::t('wms/buttons', 'Confirm Cross Dock Picking List [{0}]', [3]), [
        'class' => 'btn btn-primary btn-lg',
        'style' => ' margin:10px;',
        'id' => 'confirm-cross-dock-list-bt',
        'data' => ['url' => Url::toRoute('confirm-cross-dock')],
    ]) ?>

    <?= Html::tag('span', Yii::t('wms/buttons', 'Print box barcode [{0}]', [4]), [
        'class' => 'btn btn-danger btn-lg',
        'style' => ' margin:10px;',
        'id' => 'print-box-barcode-bt',
        'data' => ['url' => Url::toRoute('get-cross-doc-orders')],
    ]) ?>

    <?= Html::tag('span', Yii::t('wms/buttons', 'Search by product [{0}]', [5]), [
        'class' => 'btn btn-danger btn-lg',
        'style' => ' margin:10px;',
        'id' => 'search-by-product-cross-dock-bt',
        'data' => ['url' => Url::toRoute('search-by-product-form')],
    ]) ?>

    <?= Html::tag('span', Yii::t('wms/buttons', 'Add new item [{0}]', [6]), [
        'class' => 'btn btn-warning btn-lg',
        'style' => ' margin:10px;',
        'id' => 'add-new-item-cross-dock-bt',
        'data' => ['url' => Url::toRoute('add-new-item-cross-dock')],
    ]) ?>

    <?= Html::tag('span', Yii::t('wms/buttons', 'Create new cross-dock [{0}]', [7]), [
        'class' => 'btn btn-warning btn-lg',
        'style' => ' margin:10px;',
        'id' => 'create-cross-dock-form-bt',
        'data' => ['url' => Url::toRoute('create-cross-dock-form')],
    ]) ?>
</span>

<div id="container-crossdock-de-facto-layout" style="margin-top: 50px;" class="ajax-container">
</div>
<iframe style="display: none" name="frame-print-alloc-list" src="#" width="468" height="468">
</iframe>

