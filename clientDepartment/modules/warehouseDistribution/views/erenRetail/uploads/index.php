<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 08.01.15
 * Time: 7:02
 */

use yii\helpers\Html;
use yii\helpers\Url;
?>

<?php $this->title = Yii::t('titles', 'Загрузить приходную/расходную накладную'); ?>
<h1><?= $this->title ?> </h1>
<span id="buttons-menu">
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Загрузить приходную накладную'), [
        'data'=>['url'=>Url::toRoute('inbound-order')],
        'class' => 'btn btn-primary btn-lg',
        'style' => ' margin:10px;',
        'id' => 'upload-inbound-order-bt'
    ]) ?>
</span>

<span id="buttons-menu">
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Upload Outbound Order'), [
        'data'=>['url'=>Url::toRoute('outbound-order')],
        'class' => 'btn btn-primary btn-lg',
        'style' => ' margin:10px;',
        'id' => 'upload-outbound-order-bt'
    ]) ?>
</span>
<script>
    $(function(){
        $('body').on('click', '#upload-outbound-order-bt', function(){
            window.location.href = $(this).data('url');
        })
	    $('body').on('click', '#upload-inbound-order-bt', function(){
            window.location.href = $(this).data('url');
        })
    });
</script>