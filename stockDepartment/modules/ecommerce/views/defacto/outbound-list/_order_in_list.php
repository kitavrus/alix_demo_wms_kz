<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 12.11.2019
 * Time: 11:31
 */
use yii\helpers\Html;
?>
<h1>Отсканировали в лист отгрузки</h1>
<table class="table table-bordered">
    <thead>
        <td>#</td>
        <td>Шк места</td>
        <td>Курьерская компания</td>
        <td>номер заказ</td>
        <td>Defacto TTN</td>
        <td>Номер листа</td>
		<td>Удалить</td>
    </thead>
<?php $totalQty = count($orderInList); ?>
<?php foreach($orderInList as $key=>$productRow)  { ?>
    <tr class="alert-success">
        <td><?= $totalQty-$key; ?></td>
        <td><?= $productRow['package_barcode']; ?></td>
        <td><?= $productRow['courier_company']; ?></td>
        <td><?= $productRow['client_order_number']; ?></td>
        <td><?= $productRow['ttn_delivery_company']; ?></td>
        <td><?= $productRow['list_title']; ?></td>
		        <td><?php if($productRow['status'] == \common\ecommerce\constants\OutboundListStatus::NO) {
        	    echo Html::a(
        	    		Yii::t('outbound/buttons', 'Удалить'),
			            ['/ecommerce/defacto/outbound-list/delete','id'=>$productRow['id']],
						[
							'class' => 'btn btn-danger',
							'data' => [
								'confirm' => 'Вы действительно хотите удалить '.$productRow['package_barcode'].' ?',
								'method' => 'post',
							],
						]);
	        }   ?></td>
    </tr>
<?php } ?>
</table>
