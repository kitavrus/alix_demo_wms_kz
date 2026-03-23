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
                    <th>Добавить</th>
                </tr>
                <?php foreach($moreDeliveryTime as $moreDeliveryTimeItem) { ?>
                    <tr>
                        <td><?= \yii\helpers\Html::a('со Склада => '.$moreDeliveryTimeItem['store-name'],\yii\helpers\Url::to(['/tms/default/view','id'=>$moreDeliveryTimeItem['id']])); ?></td>
                        <td><?= Yii::$app->formatter->asDatetime($moreDeliveryTimeItem['shipped_datetime']); ?></td>
                        <td><?= $moreDeliveryTimeItem['day']; ?></td>
                        <td><?= $moreDeliveryTimeItem['diff']; ?></td>
                        <td><?= $moreDeliveryTimeItem['day-term']; ?></td>
                        <td><?= $moreDeliveryTimeItem['fail-delivery-status']->statusText; ?></td>
                        <td><?= $moreDeliveryTimeItem['fail-delivery-status']->otherStatus; ?></td>
                        <td><?= \yii\helpers\Html::a('Добавить статус',\yii\helpers\Url::to(['/tms/last-day-check/add-status','id'=>$moreDeliveryTimeItem['id']])); ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>