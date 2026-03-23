<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 09.04.15
 * Time: 11:53
 */
use yii\helpers\Html;
use yii\helpers\Url;
?>
<?php $expectedQty = 0;?>
<?php $count = count($items);?>
<?php if(!empty($items) && is_array($items)) { ?>
    <?php foreach($items as $item) { ?>
        <?= '<tr id="row-'.$item['id'].'-'.$item['order_number'].'" class="">';?>
        <?= '<td>'.$item['order_number'].'</td>'; ?>
        <?= '<td>'.$item['expected_qty'].'</td>'; ?>
        <?=
        '<td>'. Html::tag('span', Yii::t('return/buttons', 'Delete'),
            [
                'data-url' => Url::toRoute('delete-inbound-order-item'),
                'class' => 'btn btn-danger',
                'id' => 'return-delete-inbound-item-bt',
                'data-item_id'=>$item['id'],
                'style' => 'margin-right:10px;'])
        .'</td>';
        ?>
        <?= '</tr>'; ?>
        <?php $expectedQty += intval($item['expected_qty']); } ?>

        <?= '<tr id="row-'.$item['id'].'-'.$item['order_number'].'" class="">';?>
        <?= '<td><strong>Итого: '.$count.' к.</strong></td>'; ?>
        <?= '<td><strong>'.$expectedQty.'</strong></td>'; ?>
        <?= '</tr>'; ?>
<?php }?>