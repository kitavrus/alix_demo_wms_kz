<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
use yii\bootstrap\Modal;
//use stockDepartment\assets\OutboundAsset;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $orderNumberArray common\modules\inbound\models\InboundOrder */
/* @var $clientsArray common\modules\client\models\Client */
/* @var $outboundForm stockDepartment\modules\outbound\models\OutboundPickListForm */
//OutboundAsset::register($this);
$this->title = Yii::t('wms/titles', 'Distribution || Koton')
?>
<?= Html::label(Yii::t('inbound/forms', 'Client ID')); ?>
<?= Html::dropDownList( 'client_id',$id,$clientsArray, [
        'prompt' => '',
        'id' => 'main-form-client-id',
        'class' => 'form-control input-lg',
        'data'=>['url'=>Url::to('/wms/default/route-form')],
        'readonly' => true,
    ]
); ?>
<div id="container-colins-process-form-layout" style="margin-top: 30px;"></div>
<div id="container-colins-layout" style="margin-top: 30px;"></div>

<span id="buttons-menu">
    <?= Html::a(Yii::t('outbound/buttons', 'Приход'), Url::toRoute(['/wms/miele/inbound/index','client_id'=>$id]),['class' => 'btn btn-warning btn-lg', 'style' => ' margin:10px;']) ?>
    <?= Html::a(Yii::t('outbound/buttons', 'Размещение Прихода'), Url::toRoute(['/wms/miele/inbound/addresses','client_id'=>$id]),['class' => 'btn btn-warning btn-lg', 'style' => ' margin:10px;']) ?>
    <?= Html::a(Yii::t('outbound/buttons', 'Расход'), Url::toRoute(['/wms/miele/outbound/index','client_id'=>$id]),['class' => 'btn btn-danger btn-lg', 'style' => ' margin:10px;']) ?>
    <?= Html::a(Yii::t('outbound/buttons', 'Перемещение'), Url::toRoute(['/wms/miele/movement/index','client_id'=>$id]),['class' => 'btn btn-success btn-lg', 'style' => ' margin:10px;']) ?>
    <?= Html::a(Yii::t('outbound/buttons', 'Закрыть накладные'), Url::toRoute(['/wms/miele/complete/index','client_id'=>$id]),['class' => 'btn btn-primary btn-lg', 'style' => ' margin:10px;']) ?>
</span>
<div id="container-colins-outbound-layout" style="margin-top: 50px;"></div>