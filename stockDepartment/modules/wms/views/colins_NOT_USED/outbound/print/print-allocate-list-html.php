<?php
use yii\helpers\Html;

$html = '';
$html .= Html::beginTag('div', ['class' => 'a4']);
$html .='<table width="60%" style="padding:2px">
            <tr>
                <td width="100%" colspan="2"><h1>Лист распределения Colins</h1></td>
            </tr>
             <tr>
                <td width="30%">Дата: '.date('Y.m.d').'</td>
                <td width="30%">ШК короба: '.$orderNumber.'</td>
            </tr>
        </table><br>';
$activeShops = [];
if($outputData){
    foreach($outputData as $data) {
        foreach ($data as $item) {
            $activeShops[$item['shop_id']] = isset($storeArray[$item['shop_id']]) ? $storeArray[$item['shop_id']] : '-НЕ найден-';
        }
    }
}
asort($activeShops);
$shopSortingArray = [];
asort($storeArray);


$min = min($activeShops);
$max = max($activeShops);

if($storeArray) {
    foreach ($storeArray as $storeId => $shopCode) {
        if($shopCode >= $min && $shopCode <=$max) {
            if (array_key_exists($storeId, $activeShops)) {
                $shopSortingArray[$storeId] = $shopCode;
            } else {
                $shopSortingArray[$storeId] = $shopCode;
            }
        }
    }
}
$html .='<table width="100%" style="padding:2px; font-size: 11px" border="1" align="left">
            <tr>
                <th width="11%" style="background-color: #d9dad9">'.Yii::t('stock/forms', 'ШК').'</th>
                <th width="10%" style="background-color: #d9dad9">'.Yii::t('stock/forms', 'Модель').'</th>'.
                '<th width="7%" style="background-color: #d9dad9">'.Yii::t('stock/forms', 'Остаток').'</th>';

            foreach ($shopSortingArray as $storeId => $shopCode) {
                    $html .= '<th width="3%" style="background-color: #d9dad9">' . $shopCode . '</th>';
            }

$html.='</tr>';

$v = [];
if($outputData){
    foreach($outputData as $data) {
        $html .= '<tr>';
        foreach ($shopSortingArray as $storeId => $shopCode) {
            $v[$storeId] = '<td>0</td>';
        }
        foreach($data as $k=>$item) {
            if(!$k) {
                $html .= '<td>' . $item['product_barcode'] . '</td>
                          <td>' . $item['product_model'] . '</td>'
                          .'<td>' . (isset($remnantInBox[$item['product_barcode']]) ? $remnantInBox[$item['product_barcode']] : 0) . '</td>';
            }

            if(isset($v[$item['shop_id']])) {
                $v[$item['shop_id']] = '<td style="background-color: #eef667">'.$item['expected_qty'].'</td>';
            }
        }
        $html .= implode('',$v);
        $html .='</tr>';
    }
}

$html.= '</table>';
$html.= Html::endTag('div');
echo $html;
//die;