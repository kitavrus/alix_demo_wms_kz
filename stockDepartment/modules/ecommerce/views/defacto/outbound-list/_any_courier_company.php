<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 12.11.2019
 * Time: 11:31
 */
?>
<h1>Все курьерки отсканированные в листы отгрузки</h1>
<table class="table table-bordered">
    <thead>
    <td>Номер листа</td>
    <td>Курьерская компания</td>
    <td>Кол-во</td>
    <td></td>
    <td>#</td>
    </thead>
    <?php $totalQty = count($anyCourierCompany->orderList); ?>
    <?php foreach($anyCourierCompany->orderList as $key=>$productRow)  { ?>
        <tr class="alert-success">
            <td><?= $productRow['list_title']; ?></td>
            <td><?= $productRow['courier_company']; ?></td>
            <td><?= $productRow['orderQty']; ?></td>
            <td><span class="btn btn-danger delete-list-bt" data-url="<?= \yii\helpers\Url::toRoute(['delete-list','listTitle'=>$productRow['list_title'],'courierCompany'=>$productRow['courier_company']]); ?>">Удалить весь лист</span></td>
            <td><?= $totalQty-$key; ?></td>
        </tr>
    <?php } ?>
</table>