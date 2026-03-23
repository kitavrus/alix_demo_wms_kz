<?php

////Yii::$app->get('tcpdf');;

$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//$pdf->SetFont('dejavusans', 'b', 8);
//$pdf->SetFont('dejavusans', 'b', 8);
//$pdf->SetFont('times', 'b', 8); // -

//$pdf->SetFont('helvetica', 'b', 8); // -
$pdf->SetFont('arial', 'b', 8); //ok
$pdf->SetMargins(10, 5, 10);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);


$pdf->AddPage();
$style = array(
    'border'=>false,
    'padding'=>0,
    'hpadding'=>0,
    'vpadding'=>0.5,
    'fgcolor'=>array(0, 0, 0),
    'bgcolor'=>false,
    'text'=>true,//Текст снизу
    'font'=>'dejavusans',
    'fontsize'=>15,//Размер шрифта
    'stretchtext'=>4,//Растягивание
    'stretch'=>true,
    'fitwidth'=>true,
    'cellfitalign'=>'',
);

$html = '';
$html .='<table width="60%" style="padding:2px">
            <tr>
                <td width="100%" colspan="2"><h1>Лист распределения Colins</h1></td>
            </tr>
             <tr>
                <td width="30%">Дата: '.date('Y.m.d').'</td>
                <td width="30%">ШК короба: '.$orderNumber.'</td>
            </tr>
        </table><br>';
$pdf->writeHTML($html, true, false, true, false, 'L');
//$pdf->write1DBarcode($barcode, 'C128', 145, 5, '60', 20, 1.5, $style, 'R');
$pdf->ln(5);

$activeShops = [];
if($outputData){
    foreach($outputData as $data) {
        foreach ($data as $item) {
//            $shopIDs[$item['shop_id']] = $item['shop_id'];
            $activeShops[$item['shop_id']] = isset($storeArray[$item['shop_id']]) ? $storeArray[$item['shop_id']] : '-НЕ найден-';
        }
    }
}

//echo "<br />remnantInBox: <br />";
//\yii\helpers\VarDumper::dump($remnantInBox,10,true);
//$shopIDs = \yii\helpers\ArrayHelper::getColumn($outputData,'shop_id');
//echo "<br />shopIDs: <br />";
//\yii\helpers\VarDumper::dump($shopIDs,10,true);
//echo "<br />outputData: <br />";
//\yii\helpers\VarDumper::dump($outputData,10,true);
//echo "<br />storeArray: <br />";
//\yii\helpers\VarDumper::dump($storeArray,10,true);
//echo "<br />";
//die('---zxzxzx---');'

//if($storeArray){
//    foreach ($storeArray as $storeId => $shopCode) {
//        if(in_array($storeId,$shopIDs)) {
//            $activeShops[$storeId] = $shopCode;
//        }
//    }
//}
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

$html ='<table width="100%" style="padding:2px; font-size: 6px" border="1" align="left">
            <tr>
                <th width="10%" style="background-color: #d9dad9">'.Yii::t('stock/forms', 'ШК').'</th>
                <th width="10%" style="background-color: #d9dad9">'.Yii::t('stock/forms', 'Модель').'</th>'.
                '<th width="4%" style="background-color: #d9dad9">'.Yii::t('stock/forms', 'Остаток').'</th>';

//            foreach ($activeShops as $storeId => $shopCode) {
            foreach ($shopSortingArray as $storeId => $shopCode) {
                    $html .= '<th width="3%" style="background-color: #d9dad9">' . $shopCode . '</th>';
//                    $html .= '<th width="3%" style="background-color: #d9dad9">' . $shopCode . ' / ' . $storeId . '</th>';
            }

$html.='</tr>';

$v = [];
if($outputData){
    foreach($outputData as $data) {
        $html .= '<tr>';
//        foreach ($activeShops as $storeId => $shopCode) {
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
//echo $html;
$pdf->writeHTML($html, true, false, true, false, 'C');
$pdf->lastPage();
$pdf->Output(time() . '-colins-allocate-list.pdf', 'D');
die;