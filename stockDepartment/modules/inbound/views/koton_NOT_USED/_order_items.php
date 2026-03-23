<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\VarDumper;
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 14.01.15
 * Time: 11:53
 */
//VarDumper::dump($items, 10, true); die;
?>

<?php foreach($items as $item) { ?>
        <?= '<tr id="row-'.$item['id'].'-'.$item['product_barcode'].'" class="">';?>
        <?= '<td>'.$item['parent_title'].'</td>'; ?>
        <?= '<td>'.$item['order_number'].'</td>'; ?>
        <?= '<td>'.$item['product_barcode'].'</td>'; ?>
        <?= '<td>'.$item['expected_qty'].'</td>'; ?>
        <?//= '<td id="accepted-qty-'.$item['id'].'-'.$item['product_barcode'].'">'.Html::input('text', 'accepted_qty', $item['accepted_qty'], ['class'=>'acc-qty form-control input-sm', 'id' => 'accepted-qty']).'</td>'; ?>
<!--        --><?//= '<td>'
//                .Html::tag('span', Yii::t('inbound/buttons', 'Принять'),
//        [
//        'class' => 'btn btn-danger pull-right inbound-return-accept-bt',
//        'data-url' => Url::toRoute('accept-product-qty'),
//        'data-product-barcode' => $item['product_barcode'],
//        'data-inbound-order-id' => $item['id'],
//    ])
//          .'</td>'; ?>

    <?= '<td>'.$item['accepted_qty'].'</td>'; ?>
    <?= '<td>'.$item['difference_qty'].'</td>'; ?>
    <?= '</tr>'; ?>
<?php } ?>