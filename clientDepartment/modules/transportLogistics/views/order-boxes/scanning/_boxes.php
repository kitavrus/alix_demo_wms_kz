<?php if( !empty($items) ) { ?>
	<?php foreach($items as $item) { ?>
		<?= '<tr id="row-'.$item['box_barcode'].'" class="alert-success">';?>
		<?= '<td>'.$item['box_barcode'].'</td>'; ?>
		<?= '</tr>'; ?>
	<?php } ?>
<?php } ?>
