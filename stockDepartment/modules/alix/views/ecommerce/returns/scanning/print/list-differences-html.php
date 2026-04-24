<?php

use yii\helpers\Html;

$table = Html::beginTag('div', ['class' => 'a4']);
$table .= Html::tag('h3', 'Лист расхождений возврата');
$table .= Html::tag('span', date('Y-m-d'), ['style' => 'float: right']);
$table .= '<table width="100%" cellspacing="0" cellpadding="4" border="1">'
    . '<tr align="center" valign="middle">'
    . '<th width="40%"><strong>' . Yii::t('inbound/forms', 'Product Barcode') . '</strong></th>'
    . '<th width="30%"><strong>' . Yii::t('inbound/forms', 'Expected Qty') . '</strong></th>'
    . '<th width="30%"><strong>' . Yii::t('inbound/forms', 'Accepted Qty') . '</strong></th>'
    . '</tr>';

if (!empty($items)) {
    foreach ($items as $item) {
        if ($item['expected_qty'] == $item['accepted_qty']) {
            continue;
        }
        $table .= '<tr align="center" valign="middle">'
            . '<td align="left">' . $item['product_barcode'] . '</td>'
            . '<td>' . $item['expected_qty'] . '</td>'
            . '<td>' . $item['accepted_qty'] . '</td>'
            . '</tr>';
    }
}

$table .= '</table>';
$table .= Html::endTag('div');
echo $table;
