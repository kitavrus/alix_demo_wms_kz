<?php
use yii\helpers\Html;

$structure_table = Html::beginTag('div', ['class' => 'a4']);
$structure_table .= Html::tag('h3', 'Лист расхождений');
$structure_table .= Html::tag('span', date("Y-m-d"), ['style' => 'float: right']);
$structure_table .= '<table width="100%" cellspacing="0" cellpadding="4" border="1">' .
    '   <tr align="center" valign="middle" >' .
    '      <th width="30%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms', 'Product Barcode') . '</strong></th>' .
    '      <th width="20%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms', 'Product Model') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms', 'Primary address') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms', 'Secondary address') . '</strong></th>' .
    '      <th width="10%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms', 'Expected Qty') . '</strong></th>' .
    '      <th width="10%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms', 'Accepted Qty') . '</strong></th>' .
    '   </tr>';

if (!empty($items)) {
    foreach ($items as $item) {
        if ($item['product_expected_qty'] != $item['product_accepted_qty']) {
            $structure_table .= '<tr align="center" valign="middle" style="background-color:' . ($item['product_expected_qty'] == $item['product_accepted_qty'] ? '#FFFFF1' : 'lightgray') . '">
                <td align="left" valign="middle" border="1">' . $item['product_barcode'] . '</td>
                <td align="center" valign="middle" border="1">' . $item['product_model'] . '</td>
                <td align="center" valign="middle" border="1">' . '-' . '</td>
                <td align="center" valign="middle" border="1">' . '-' . '</td>
                <td align="center" valign="middle" border="1">' . $item['product_expected_qty'] . '</td>
                <td align="center" valign="middle" border="1">' . $item['product_accepted_qty'] . '</td>
            </tr>';

            $itemsProcess = \common\ecommerce\entities\EcommerceStock::find()
                ->select('id, product_barcode, box_address_barcode AS primary_address, place_address_barcode AS secondary_address, product_model, count(*) as items ')
                ->where([
                    'inbound_id' => $item['inbound_id'],
                    'product_barcode' => $item['product_barcode'],
                    'status' => [
                        \common\ecommerce\entities\EcommerceStock::STATUS_INBOUND_SCANNED,
                        \common\ecommerce\entities\EcommerceStock::STATUS_INBOUND_OVER_SCANNED,
                    ],
                ])
                ->groupBy('product_barcode, box_address_barcode')
                ->orderBy([
                    'place_address_barcode' => SORT_DESC,
                    'box_address_barcode' => SORT_DESC,
                ])
                ->asArray()
                ->all();

            if ($itemsProcess) {
                foreach ($itemsProcess as $value) {
                    $structure_table .= '<tr align="center" valign="middle">
                    <td align="left" valign="middle" border="1">' . $value['product_barcode'] . '</td>
                    <td align="center" valign="middle" border="1">' . $value['product_model'] . '</td>
                    <td align="center" valign="middle" border="1">' . $value['primary_address'] . '</td>
                    <td align="center" valign="middle" border="1">' . $value['secondary_address'] . '</td>
                    <td align="center" valign="middle" border="1">' . '-' . '</td>
                    <td align="center" valign="middle" border="1">' . $value['items'] . '</td>
                </tr>';
                }
            }
        }
    }
}

$structure_table .= '</table>';
$structure_table .= Html::endTag('div');
echo $structure_table;
