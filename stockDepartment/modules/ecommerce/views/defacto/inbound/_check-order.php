<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 30.09.2017
 * Time: 15:08
 */
?>
<h1>Эти товары нужно пересканировать</h1>
<div id="inbound-items" class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
            <th><?= Yii::t('inbound/forms', 'Короб клиента'); ?></th>
            <th><?= Yii::t('inbound/forms', 'ШК товара'); ?></th>
            <th><?= Yii::t('inbound/forms', 'Шк нашего короба'); ?></th>
            <th><?= Yii::t('inbound/forms', 'Адрес места'); ?></th>
        </thead>
        <tbody id="inbound-item-body">
        <?php foreach($problemInfo->problemProductList as $i=>$item) { ?>
            <tr>
                <th><?= $item->clientBoxBarcode; ?></th>
                <th><?= $item->productBarcode; ?></th>
                <th><?= $item->boxAddressBarcode; ?></th>
                <th><?= $item->placeAddressBarcode; ?></th>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<h1>У этих товаров неверный шк товара(пересканировать)</h1>
<div id="inbound-items" class="table-responsive">
	<table class="table table-striped table-bordered">
		<thead>
		<th><?= Yii::t('inbound/forms', 'Короб клиента'); ?></th>
		<th><?= Yii::t('inbound/forms', 'ШК товара'); ?></th>
		<th><?= Yii::t('inbound/forms', 'Шк нашего короба'); ?></th>
		<th><?= Yii::t('inbound/forms', 'Адрес места'); ?></th>
		</thead>
		<tbody id="inbound-item-body">
		<?php foreach($problemInfo->incorrectProductBarcode as $i=>$item) { ?>
			<tr>
				<th><?= $item->clientBoxBarcode; ?></th>
				<th><?= $item->productBarcode; ?></th>
				<th><?= $item->boxAddressBarcode; ?></th>
				<th><?= $item->placeAddressBarcode; ?></th>
			</tr>
		<?php } ?>
		</tbody>
	</table>
</div>