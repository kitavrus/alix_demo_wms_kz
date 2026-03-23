<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 20.09.2017
 * Time: 18:51
 */
?>
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