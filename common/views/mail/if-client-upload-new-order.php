<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 20.09.2017
 * Time: 18:51
 */
?>
<table class="table table-bordered">
    <tr>
        <td>Новая накладная:</td>
    </tr>
    <tr>
        <td><?= $order->clientName; ?></td>
    </tr>
    <tr>
        <td><?= $order->orderNumber; ?></td>
    </tr>
    <tr>
        <td><?= $order->comment; ?></td>
    </tr>
    <tr>
        <td><?= \yii\helpers\Html::a("Подробнее...",Yii::$app->params['stockDepartmentUrl'].'/'.$order->isOrderType.'/report/view?id='.$order->id); ?></td>
    </tr>
</table>