<?php
use stockDepartment\modules\alix\controllers\outboundSeparator\constants\Status;
?>

<?php if( !empty($outBoxInfo["total_in_box"]) ) { ?>
	<?= '<h2> Убрать из короба: '.count($outBoxInfo["items"]["out_box"]).'</h2>'; ?>
	<table class="table table-bordered">
	    <?php foreach($outBoxInfo["items"]["out_box"] as $item) { ?>
	        <?= '<tr  class="'.($item['status'] == Status::SCANNED  ? 'alert-success'  : 'alert-danger') . '">';?>
	            <?= '<td>'.$item['order_number'].'</td>'; ?>
	            <?= '<td>'.$item['out_box_barcode'].'</td>'; ?>
	            <?= '<td>'.$item['product_barcode'].'</td>'; ?>
	            <?= '<td>'.$item['status_to_out'].'</td>'; ?>
	            <?= '<td>'.$item['status'].'</td>'; ?>
	        <?= '</tr>'; ?>
	    <?php } ?>
	</table>
	<?= '<h2> Останется в коробе: '.count($outBoxInfo["items"]["in_box"]).' </h2>'; ?>
	<table class="table table-bordered">
	    <?php foreach($outBoxInfo["items"]["in_box"] as $item) { ?>
	        <?= '<tr>';?>
	            <?= '<td>'.$item['order_number'].'</td>'; ?>
	            <?= '<td>'.$item['out_box_barcode'].'</td>'; ?>
	            <?= '<td>'.$item['product_barcode'].'</td>'; ?>
	            <?= '<td>'.$item['status_to_out'].'</td>'; ?>
	            <?= '<td>'.$item['status'].'</td>'; ?>
	        <?= '</tr>'; ?>
	    <?php } ?>
	</table>
<?php } ?>

