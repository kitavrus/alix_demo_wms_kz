<?php

use yii\bootstrap\Html;
use yii\helpers\VarDumper;

VarDumper::dump($resp,10,true);
?>

<?= Html::a('view-outbound	', ['/ecommerce/defacto/report/outbound-view', 'id' => $id], ['class' => 'btn btn-primary']) ?>
<!--        --><?//= Html::a('Delete', ['delete', 'id' => $model->id], [
//            'class' => 'btn btn-danger',
//            'data' => [
//                'confirm' => 'Are you sure you want to delete this item?',
//                'method' => 'post',
//            ],
//        ]) ?>
</p>
