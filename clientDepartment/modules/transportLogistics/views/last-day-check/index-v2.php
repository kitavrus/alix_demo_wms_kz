<div class="col-xs-12 col-md-12">
    <div class="panel panel-danger">
        <div class="panel-heading">Последний день доставки</div>
        <div class="panel-body">
            <table class="table table-striped table-bordered">
                <tr>
                    <th>Магазин:</th>
                    <th>Дата отгрузки:</th>
                    <th>Прошло дней:</th>
                    <th>Разница:</th>
                    <th>Срок по тарифу</th>
                    <th>Статус доставки</th>
                    <th>Статус доставки комментарий</th>
                </tr>
                <?php foreach($moreDeliveryTime as $moreDeliveryTimeItem) { ?>
                    <tr>
                        <td><?= 'со Склада => '.$moreDeliveryTimeItem['store-name']; ?></td>
                        <td><?= Yii::$app->formatter->asDatetime($moreDeliveryTimeItem['shipped_datetime']); ?></td>
                        <td><?= $moreDeliveryTimeItem['day']; ?></td>
                        <td><?= $moreDeliveryTimeItem['diff']; ?></td>
                        <td><?= $moreDeliveryTimeItem['day-term']; ?></td>
                        <td><?= $moreDeliveryTimeItem['fail-delivery-status']->statusText; ?></td>
                        <td><?= $moreDeliveryTimeItem['fail-delivery-status']->otherStatus; ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>