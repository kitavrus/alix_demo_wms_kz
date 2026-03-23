<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 12.01.15
 * Time: 11:36
 */

use yii\bootstrap\ActiveForm;
use \yii\helpers\Html;
use yii\bootstrap\Modal;

?>

<?php $this->title = Yii::t('inbound/titles', 'Colins Cross-dock'); ?>

    <h1><?= $this->title ?></h1>
<?php if ($step==1) { ?>
    <?php //форма загрузки первого файла ?>
    <div class="panel panel-info">
            <div class="panel-heading">
                <?= Yii::t('inbound/titles', 'Step one'); ?>
            </div>
            <div class="panel-body">
                <?= Yii::t('inbound/titles', 'Download first file'); ?>
            </div>
    </div>
    <div class="colins-process-form-step-1">
        <?php $form = ActiveForm::begin([
                'options' => ['enctype' => 'multipart/form-data'],
                'id' => 'colins-process-form',
                'validateOnChange' => false,
            ]
        ); ?>

        <?= $form->field($model, 'file_1')->fileInput() ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('buttons', 'Upload'), ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
<?php } ?>
<?php if ($step==2) { ?>
    <?php //форма загрузки второго файла ?>
    <div class="panel panel-info">
        <div class="panel-heading">
            <?= Yii::t('inbound/titles', 'Step two'); ?>
        </div>
        <div class="panel-body">
            <?= Yii::t('inbound/titles', 'Download second file'); ?>
        </div>
    </div>
    <div class="colins-process-form-step-2">
        <?php $form = ActiveForm::begin([
                'options' => ['enctype' => 'multipart/form-data'],
                'id' => 'colins-process-form',
                'validateOnChange' => false,
            ]
        ); ?>

        <?= $form->field($model, 'file_2')->fileInput() ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('buttons', 'Upload'), ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('buttons', 'Сбросить'), '/warehouseDistribution/colins/inbound', ['class' => 'btn btn-warning']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
<?php } ?>

<?php if ($step==3) { ?>
    <div class="panel panel-warning">
        <div class="panel-heading">
            <?= Yii::t('inbound/titles', 'If all files looks like correct you may confirm uploading by press "Upload" button'); ?>
        </div>
        <div class="panel-body">
            <?= Html::button(Yii::t('buttons', 'Upload file(s) in system'), ['class' => 'btn btn-danger', 'id' => 'confirm-upload-bt', 'data-url' => '/warehouseDistribution/colins/inbound/process-confirm']) ?>
        </div>

    </div>
<?php } ?>

<?php //Просмотр первого документа ?>
<?php if ($fileData1) { ?>
    <div class="form-group">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th><?= Yii::t('stock/forms', 'Box barcode')?></th>
                <th><?= Yii::t('stock/forms', 'Product barcode')?></th>
                <th><?= Yii::t('stock/forms', 'Product model')?></th>
                <th><?= Yii::t('stock/forms', 'SKU')?></th>
                <th><?= Yii::t('stock/forms', 'Color')?></th>
                <th><?= Yii::t('stock/forms', 'Size')?></th>
                <th><?= Yii::t('stock/forms', 'Season')?></th>
                <th><?= Yii::t('stock/forms', 'Made in')?></th>
                <th><?= Yii::t('stock/forms', 'Composition')?></th>
                <th><?= Yii::t('stock/forms', 'Category')?></th>
                <th><?= Yii::t('stock/forms', 'Gender')?></th>
                <th><?= Yii::t('stock/forms', 'Product qty')?></th>
                <th><?= Yii::t('stock/forms', 'Price')?></th>
            </tr>
            </thead>
            <tbody>
                <?php
                    foreach ($fileData1 as $data) {  ?>
                        <tr>
                            <td><?=$data['box_barcode']?></td>
                            <td><?=$data['product_barcode']?></td>
                            <td><?=$data['product_model']?></td>
                            <td><?=$data['product_sku']?></td>
                            <td><?=$data['product_color']?></td>
                            <td><?=$data['product_size']?></td>
                            <td><?=$data['product_season']?></td>
                            <td><?=$data['product_made_in']?></td>
                            <td><?=$data['product_composition']?></td>
                            <td><?=$data['product_category']?></td>
                            <td><?=$data['product_gender']?></td>
                            <td><?=$data['product_qty']?></td>
                            <td><?=$data['product_price']?></td>
                        </tr>
                   <?php } ?>
            </tbody>
        </table>
    </div>

<?php } ?>

<?php //Просмотр второго документа ?>
<?php if ($fileData2) { ?>
<?php $table_head = array_shift($fileData2) ?>
    <div class="form-group">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
    <?php foreach ($table_head as $head) { ?>
                    <th><?php
//                        $shop_code =  iconv('CP866','utf-8',$head);
                        $shop_code =  iconv('windows-1251','utf-8',$head);
                        $shopCode = trim($shop_code);
                        $shopCode = str_replace(' ','=',$shopCode);
                        $shopCode = explode('=',$shopCode);

                        $shop = '-not find-';
                        if (isset($shopCode[0])) {
                            $shop = $shopCode[0];
                        }
                        if( !\common\modules\store\models\Store::findClientStoreByShopCode(\common\modules\client\models\Client::CLIENT_COLINS, $shop) && (trim($shop_code) != 'Баркод' || trim($shop_code) != 'Штрихкод')) {
                            echo '<span style="color: red;">'.$shop_code . '<BR />НЕ НАЙДЕН!</span>';
                        } else {
                            echo $shop_code;
                        }

                        ?></th>
    <?php } ?>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($fileData2 as $row) { ?>
                <tr>
                <?php foreach ($row as $cell) { ?>

                    <td><?= $cell ?></td>

                <?php } ?>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>

<?php Modal::begin([
    //'header' => '<h4 id="delivery-proposal-index-header"></h4>',
    'id' => 'loading-modal',
    'closeButton' => false,
    'options' => [
        'data-backdrop' => 'static',
        'data-keyboard' => 'false',
    ],
]); ?>
<?= "<div id='loading-modal-content'>Идет обработка данных, пожалуйста подождите...</div>"; ?>
<?php Modal::end(); ?>

<script type="text/javascript">




    $(function () {

        var b = $('body');

        b.on('click', '#confirm-upload-bt', function(){
            if(confirm('Вы точно хотите загрузить данные в систему')){
                $('#loading-modal').modal('show');
                $.post($(this).data('url'), {}, function (result) {

                }, 'html')
            }
        });
    });
</script>