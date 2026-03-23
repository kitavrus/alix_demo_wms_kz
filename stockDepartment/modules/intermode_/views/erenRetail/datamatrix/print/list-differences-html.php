<?php
use yii\helpers\Html;
$structure_table = Html::beginTag('div', ['class' => 'a4']);
$structure_table .= Html::tag('h3','Лист расхождений');
$structure_table .= Html::tag('span',date("Y-m-d"),['style'=>'float: right']);
$structure_table .= '<table width="100%" cellspacing="0" cellpadding="4" border="1">' .
    '   <tr align="center" valign="middle" >' .
    '      <th width="30%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Product Barcode') . '</strong></th>' .
    '      <th width="20%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Product Model') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Primary address') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Secondary address') . '</strong></th>' .
    '      <th width="10%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Expected Qty') . '</strong></th>' .
    '      <th width="10%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Accepted Qty') . '</strong></th>' .
    '   </tr>';

if (!empty($items)) {
    foreach ($items as $item) {
        if($item['expected_qty'] != $item['accepted_qty']) {
            $structure_table .= '<tr align="center" valign="middle" style="background-color:' . ($item['expected_qty'] == $item['accepted_qty'] ? '#FFFFF1' : 'lightgray') . '">
                <td align="left" valign="middle" border="1">' . $item['product_barcode'] . '</td>
                <td align="center" valign="middle" border="1">' . $item['product_model'] . '</td>
                <td align="center" valign="middle" border="1">' . '-' . '</td>
                <td align="center" valign="middle" border="1">' . '-' . '</td>
                <td align="center" valign="middle" border="1">' . $item['expected_qty'] . '</td>
                <td align="center" valign="middle" border="1">' . $item['accepted_qty'] . '</td>
            </tr>';

            //S: TODO Потом сделать это по-человечески
            $itemsProcess = \common\modules\stock\models\Stock::find()
                ->select('id, product_barcode, primary_address, secondary_address, product_model, count(*) as items ')
                ->where([
                    'inbound_order_id' => $item['inbound_order_id'],
                    'product_barcode' => $item['product_barcode'],
                    'status' => [
                        \common\modules\stock\models\Stock::STATUS_INBOUND_SCANNED,
                        \common\modules\stock\models\Stock::STATUS_INBOUND_OVER_SCANNED,
                    ]
                ])
                ->groupBy('product_barcode, primary_address')
                ->orderBy([
                    'secondary_address' => SORT_DESC,
                    'primary_address' => SORT_DESC,
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
        //E: TODO Потом сделать это по-человечески
    }
}

$structure_table .= '</table>';
$structure_table .= Html::endTag('div');
echo $structure_table;