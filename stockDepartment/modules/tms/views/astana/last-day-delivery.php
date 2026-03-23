<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 22.10.2018
 * Time: 12:48
 */
?>

<div class="row">
    <div class="col-xs-12">
        <div class="panel panel-danger">
            <div class="panel-heading">Последний день доставки</div>
            <div class="panel-body">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>ID:</th>
                        <th>Из Магазин:</th>
                        <th>В Магазин:</th>
                        <th>Дата отгрузки:</th>
                        <th>Прошло дней:</th>
                        <th>Разница:</th>
                        <th>Срок по тарифу</th>
                    </tr>
                    <?php foreach ($moreDeliveryTime as $moreDeliveryTimeItem) { ?>
                        <tr>
                            <td><?= $moreDeliveryTimeItem['delivery-id']; ?></td>
                            <td><?= $moreDeliveryTimeItem['store-from-name']; ?></td>
                            <td><?= $moreDeliveryTimeItem['store-to-name']; ?></td>
                            <td><?= Yii::$app->formatter->asDatetime($moreDeliveryTimeItem['shipped_datetime']); ?></td>
                            <td><?= $moreDeliveryTimeItem['day']; ?></td>
                            <td><?= $moreDeliveryTimeItem['diff']; ?></td>
                            <td><?= $moreDeliveryTimeItem['day-term']; ?></td>

                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</div>