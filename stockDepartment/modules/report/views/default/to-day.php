<?php //\yii\helpers\VarDumper::dump($inProcessCrossDockBoxToDay, 10, true); ?>
<?php //\yii\helpers\VarDumper::dump($inboundOrderOnRoadToKz['dataProvider'], 10, true); ?>
<?php //\yii\helpers\VarDumper::dump($moreDeliveryTime, 10, true); ?>
<?php //\yii\helpers\VarDumper::dump($outboundToDay, 10, true); ?>
<?php //\yii\helpers\VarDumper::dump($readyForDelivery, 10, true); ?>
<h2 class="text-center"><span class="text-warning">C </span><?= $currentDateTime; ?><span class="text-warning"> ДО </span></h2>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-md-3">
            <div class="panel panel-primary">
                <div class="panel-heading">За сегодня собраны</div>
                <div class="panel-body">
                    <table class="table table-bordered">
                        <tr class="active">
                            <td>Всего собрали заказов:</td>
                            <td><?= $outboundToDay['orderSum']; ?></td>
                        </tr>
                        <tr class="active">
                            <td>Всего мест:</td>
                            <td><?= $outboundToDay['placeSum']; ?></td>
                        </tr>
                        <tr class="active">
                            <td>Всего м3:</td>
                            <td><?= $outboundToDay['mcSum']; ?></td>
                        </tr>
                        <tr class="warning">
                            <td>Всего собрали лотов:</td>
                            <td><strong><?= $outboundToDay['lotSum']; ?><strong></td>
                        </tr>
                        <tr class="success">
                            <td>Всего приняли лотов:</td>
                            <td><strong><?= $inboundToDay['lotSum']; ?></strong></td>
                        </tr>
                        <tr class="info">
                            <td>Всего приняли кросс-док (коробов):</td>
                            <td><strong> <?= $acceptedCrossDockBoxToDay['boxSum']; ?></strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-3">
            <div class="panel panel-primary">
                <div class="panel-heading">За сегодня отгрузили</div>
                <div class="panel-body">
                    <table class="table table-bordered">
                        <tr class="active">
                            <td>Всего отгрузили заказов:</td>
                            <td><?= $outboundLeftToDay['orderSum']; ?></td>
                        </tr>
                        <tr class="active">
                            <td>Всего отгрузили лотов:</td>
                            <td><?= $outboundLeftToDay['lotSum']; ?></td>
                        </tr>
                        <tr class="active">
                            <td>Всего мест:</td>
                            <td><?= $outboundLeftToDay['placeSum']; ?></td>
                        </tr>
                        <tr class="active">
                            <td>Всего м3:</td>
                            <td><?= $outboundLeftToDay['mcSum']; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-3">
            <div class="panel panel-primary">
                <div class="panel-heading">За сегодня приняли кросс-док</div>
                <div class="panel-body">
                    <table class="table table-bordered">
                        <tr>
                            <td>Всего приняли заказов:</td>
                            <td><?= $acceptedCrossDockBoxToDay['orderSum']; ?></td>
                        </tr>
                        <tr>
                            <td>Всего приняли коробов:</td>
                            <td><?= $acceptedCrossDockBoxToDay['boxSum']; ?></td>
                        </tr>
                        <tr>
                            <td>Всего м3:</td>
                            <td><?= $acceptedCrossDockBoxToDay['mcSum']; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-md-3">
            <div class="panel panel-primary">
                <div class="panel-heading">Заказы еще не собраны</div>
                <div class="panel-body">
                    <table class="table table-bordered">
                        <tr class="active">
                            <td>Заказы:</td>
                            <td><?= $sumOutboundOrderInProcess['orderSum']; ?></td>
                        </tr>
                        <tr class="active">
                            <td>Лоты:</td>
                            <td><?= $sumOutboundOrderInProcess['lotSum']; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-md-3">
            <div class="panel panel-primary">
                <div class="panel-heading">Заказы еще не собраны по направлениям</div>
                <div class="panel-body">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th>Направление</th>
                            <th>Кол-во заказов:</th>
                            <th>Кол-во лотов</th>
                        </tr>
                        <?php foreach($outboundInProcessByRouteDirections as $directionName=>$outboundInProcessByRouteDirection) { ?>
                            <tr>
                                <td><?= $directionName; ?></td>
                                <td><?= $outboundInProcessByRouteDirection['orderSum']; ?></td>
                                <td><?= $outboundInProcessByRouteDirection['lotSum']; ?></td>
                            </tr>
                        <?php } ?>
                    </table>

                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-xs-12 col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading">Принимаемые поступления</div>
                <div class="panel-body">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <td>Всего заказов:</td>
                            <td><?= $inboundOrderInProcess['orderSum']; ?></td>
                        </tr>
                        <tr>
                            <td>Осталось лотов:</td>
                            <td><?= $inboundOrderInProcess['lotSum']; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading">Готовы для отгрузки</div>
                <div class="panel-body">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <td>Всего заказов:</td>
                            <td><?= $readyForDelivery['orderSum']; ?></td>
                        </tr>
                        <tr>
                            <td>Всего лотов:</td>
                            <td><?= $readyForDelivery['lotSum']; ?></td>
                        </tr>
                        <tr>
                            <td>Всего мест:</td>
                            <td><?= $readyForDelivery['placeSum']; ?></td>
                        </tr>
                        <tr>
                            <td>Всего м3:</td>
                            <td><?= $readyForDelivery['mcSum']; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="panel panel-info">
                <div class="panel-heading">Принимаемые в данный момент поступления</div>
                <div class="panel-body">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th>Заказ:</th>
                            <th>Ожидаемое кол-во:</th>
                            <th>Принятое кол-во</th>
                            <th>Разница:</th>
                            <th>Начали принимать</th>
                        </tr>
                        <?php foreach($inboundInProcessByOrders as $inboundOrder) { ?>
                            <tr>
                                <td><?= $inboundOrder['order_number'].' / '.$inboundOrder['parent_order_number']; ?></td>
                                <td><?= $inboundOrder['expectedQtyLot']; ?></td>
                                <td><?= $inboundOrder['acceptedQtyLot']; ?></td>
                                <td><?= $inboundOrder['diffQtyLot']; ?></td>
                                <td><?= Yii::$app->formatter->asDatetime($inboundOrder['beginDatetime']); ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="panel panel-info">
                <div class="panel-heading">Принимаемые в данный момент кросс-доки</div>
                <div class="panel-body">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th>Заказ:</th>
                            <th>Принятое кол-во заказов:</th>
                            <th>Принятое кол-во m3</th>
                            <th>Принятое кол-во коробов</th>
                            <th>Осталось заказов принять</th>
                            <th>Осталось коробов принять</th>
                            <th>Дата создания</th>
                        </tr>
                        <?php foreach($inProcessCrossDockBoxToDay as $inProcessCrossDock) { ?>
                            <tr>
                                <td><?= \stockDepartment\modules\wms\managers\defacto\api\CrossDockItemService::makePartyNumber($inProcessCrossDock['internal_barcode']).' / '.$inProcessCrossDock['party_number']; ?></td>
                                <td><?= $inProcessCrossDock['acceptedOrderSum']; ?></td>
                                <td><?= Yii::$app->formatter->asDecimal($inProcessCrossDock['acceptedBoxM3Sum']); ?></td>
                                <td><?= $inProcessCrossDock['acceptedBoxSum']; ?></td>
                                <td><?= $inProcessCrossDock['expectedOrderSum']; ?></td>
                                <td><?= $inProcessCrossDock['expectedBoxSum']; ?></td>
                                <td><?= Yii::$app->formatter->asDatetime($inProcessCrossDock['created_at']); ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-xs-12 col-md-6">
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
                        </tr>
                        <?php foreach($moreDeliveryTime as $moreDeliveryTimeItem) { ?>
                            <tr>
                                <td><?= \yii\helpers\Html::a('со Склада => '.$moreDeliveryTimeItem['store-name'],\yii\helpers\Url::to(['/tms/default/view','id'=>$moreDeliveryTimeItem['id']])); ?></td>
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
<!--        <div class="col-xs-12 col-md-6">-->
<!--            <div class="panel panel-success">-->
<!--                <div class="panel-heading">Готовы для отгрузки по магазинам</div>-->
<!--                <div class="panel-body">-->
<!--                    <table class="table table-striped table-bordered">-->
<!--                        <tr>-->
<!--                            <th>Магазин:</th>-->
<!--                            <th>Всего заказов:</th>-->
<!--                            <th>Всего лотов:</th>-->
<!--                            <th>Всего мест:</th>-->
<!--                            <th>Всего м3:</th>-->
<!--                        </tr>-->
<!--                        --><?php //foreach($readyForDeliveryByStore as $store) { ?>
<!--                            <tr>-->
<!--                                <td>--><?//= isset($readyForDeliveryStores[$store['to_point_id']]) ? $readyForDeliveryStores[$store['to_point_id']] :'-'; ?><!--</td>-->
<!--                                <td>--><?//= $store['orderSum']; ?><!--</td>-->
<!--                                <td>--><?//= $store['lotSum']; ?><!--</td>-->
<!--                                <td>--><?//= $store['placeSum']; ?><!--</td>-->
<!--                                <td>--><?//= $store['mcSum']; ?><!--</td>-->
<!--                            </tr>-->
<!--                        --><?php //} ?>
<!--                    </table>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->

        <div class="col-xs-12 col-md-6">
            <div class="panel panel-success">
                <div class="panel-heading">Готовы для отгрузки по направлениям</div>
                <div class="panel-body">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th>Направление / Магазин:</th>
                            <th>Всего заказов:</th>
                            <th>Всего лотов:</th>
                            <th>Всего мест:</th>
                            <th>Всего м3:</th>
                        </tr>
                        <?php foreach($readyForDeliveryByRouteDirections as $directionName=>$readyForDeliveryByRouteDirection) { ?>
                            <tr>
                                <td colspan="5"><strong><?= $directionName; ?></strong></td>
                            </tr>
                            <?php $totalOrderSum = $totalLotSum = $totalPlaceSum = $totalMcSum = 0; ?>
                            <?php foreach($readyForDeliveryByRouteDirection as $storeID=>$store) { ?>
                                <?php if(isset($store['RPT']['orderSum'])) { ?>
                                    <tr>
                                        <td style="padding-left: 20px;">RPT: <?= isset($readyForDeliveryStores[$storeID]) ? $readyForDeliveryStores[$storeID] :'-'; ?></td>
                                        <td><?= $store['RPT']['orderSum']; ?></td>
                                        <td><?= $store['RPT']['lotSum']; ?></td>
                                        <td><?= $store['RPT']['placeSum']; ?></td>
                                        <td><?= Yii::$app->formatter->asDecimal($store['RPT']['mcSum']); ?></td>
                                    </tr>
                                    <?php $totalOrderSum += $store['RPT']['orderSum'];  $totalLotSum += $store['RPT']['lotSum']; $totalPlaceSum += $store['RPT']['placeSum']; $totalMcSum += $store['RPT']['mcSum'] ; ?>
                                <?php } ?>
                                <?php if(isset($store['CROSS-DOCK']['orderSum'])) { ?>
                                    <tr>
                                        <td style="padding-left: 20px;">CROSS-DOCK: <?= isset($readyForDeliveryStores[$storeID]) ? $readyForDeliveryStores[$storeID] :'-'; ?></td>
                                        <td><?= $store['CROSS-DOCK']['orderSum']; ?></td>
                                        <td></td>
                                        <td><?= $store['CROSS-DOCK']['placeSum']; ?></td>
                                        <td><?= Yii::$app->formatter->asDecimal($store['CROSS-DOCK']['mcSum']); ?></td>
                                    </tr>
                                    <?php $totalOrderSum += $store['CROSS-DOCK']['orderSum']; $totalPlaceSum += $store['CROSS-DOCK']['placeSum']; $totalMcSum += $store['CROSS-DOCK']['mcSum'] ; ?>
                                <?php } ?>
                            <?php } ?>
                            <tr class="warning">
                                <td style="padding-left: 20px;"> <strong>Итого: </strong></td>
                                <td><strong><?= $totalOrderSum; ?> </strong></td>
                                <td><strong><?= $totalLotSum; ?> </strong></td>
                                <td><strong><?= $totalPlaceSum; ?> </strong></td>
                                <td><strong><?= Yii::$app->formatter->asDecimal($totalMcSum); ?> </strong></td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-md-6">
            <div class="panel panel-success">
                <div class="panel-heading">Поступления которые едут к нам на склад  <span class="badge badge-default"> <?= (isset($inboundOrderOnRoadToKz['dataProvider']) ? $inboundOrderOnRoadToKz['dataProvider']->query->count() : '') ?></span></div>
                <div class="panel-body">
                    <?php if(!empty($inboundOrderOnRoadToKz['dataProvider'])) { ?>
                        <?= \yii\grid\GridView::widget([
                            'tableOptions' => ['class' => 'table table-bordered'],
                            'id' => 'grid-view-inbound-order-items',
                            'dataProvider' => $inboundOrderOnRoadToKz['dataProvider'],
                            'layout'=>'{items}',
                            'pager'=>false,
                            'sorter'=>false,
                            'columns' => [
                                'party_number',
                                'field_extra1',
                                'status_created_on_client',
                                'data_created_on_client:datetime',
                                ]
                        ]); ?>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-3">
            <div class="panel panel-primary">
                <div class="panel-heading">Заказы в пути</div>
                <div class="panel-body">
                    <table class="table table-bordered">
                        <tr class="active">
                            <td>Всего в пути заказов:</td>
                            <td><?= $outboundOnRoadToDay['orderSum']; ?></td>
                        </tr>
                        <tr class="active">
                            <td>Всего в пути лотов:</td>
                            <td><?= $outboundOnRoadToDay['lotSum']; ?></td>
                        </tr>
                        <tr class="active">
                            <td>Всего мест:</td>
                            <td><?= $outboundOnRoadToDay['placeSum']; ?></td>
                        </tr>
                        <tr class="active">
                            <td>Всего м3:</td>
                            <td><?= $outboundOnRoadToDay['mcSum']; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>