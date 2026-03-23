<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 12.11.2019
 * Time: 11:31
 */
?>
<h1>Упакованы но не отсканировани в лист отгрузки.</h1>
<table class="table table-bordered">
    <thead>
    <td>Курьерская компания</td>
    <td>Кол-во</td>
    </thead>
    <?php $totalQty = count($orderNotInList->orderByCourier); ?>
    <?php foreach($orderNotInList->orderByCourier as $key=>$orderRow)  { ?>
        <tr class="alert-warning">
            <td width="20%" style="font-weight: bold"><?= $orderRow['client_CargoCompany']; ?></td>
            <td><?= $orderRow['orderQty']; ?></td>
        </tr>
    <?php } ?>
</table>

<table class="table table-bordered">
    <thead>
        <td>#</td>
        <td>Шк места</td>
        <td>Курьерская компания</td>
        <td>Номер заказ</td>
        <td>Статус</td>
        <td>Defacto TTN</td>
        <td>Дата упаковки</td>
    </thead>
<?php $asDatetimeFormat = 'php:d.m.Y H:i:s'; ?>
<?php $totalQty = count($orderNotInList->orderList); ?>
<?php foreach($orderNotInList->orderList as $key=>$orderRow)  { ?>
    <tr class="alert-danger">
        <td><?= $totalQty-$key; ?></td>
        <td><?= \yii\helpers\ArrayHelper::getValue($orderNotInList->outboundBoxList,$orderRow['id']); ?></td>
        <td style="font-weight: bold"><?= $orderRow['client_CargoCompany']; ?></td>
        <td><?= $orderRow['order_number']; ?></td>
        <td><?= \common\ecommerce\constants\OutboundStatus::getValue($orderRow['status']); ?></td>
        <td><?= $orderRow['client_ReferenceNumber']; ?></td>
        <td><?= Yii::$app->formatter->asDatetime($orderRow['packing_date'],$asDatetimeFormat) ; ?></td>
    </tr>
<?php } ?>
</table>
