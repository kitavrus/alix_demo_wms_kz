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
                <td width="100%"><h1>Лист распределения Colins</h1></td>
            </tr>
             <tr>
                <td width="30%">Дата: '.date('Y.m.d').'</td>
                <td width="30%">ШК короба: '.$orderNumber.'</td>
            </tr>
        </table><br>';
$pdf->writeHTML($html, true, false, true, false, 'L');
//$pdf->write1DBarcode($barcode, 'C128', 145, 5, '60', 20, 1.5, $style, 'R');
$pdf->ln(5);
$html ='<table width="100%" style="padding:2px; font-size: 6px" border="1" align="left">
            <tr>
                <th width="5%" style="background-color: #d9dad9">'.Yii::t('stock/forms', 'ШК').'</th>
                <th width="3%" style="background-color: #d9dad9">'.Yii::t('stock/forms', 'Модель').'</th>';

            if($storeArray){
                foreach ($storeArray as $storeId => $shopCode){
                    $html.= '<th style="background-color: #d9dad9">'.$shopCode.'</th>';
                }
            }

$html.='</tr>';


if($outputData){
    foreach($outputData as $data) {
           $html .=
               '<tr>
                    <td>'.$data['product_barcode'].'</td>
                    <td>'.$data['product_model'].'</td>';
        if($storeArray){
            foreach ($storeArray as $id => $store){
                if($data['shop_id']==$id){
                    $html.= '<td style="background-color: #eef667">'.$data['expected_qty'].'</td>';
                } else {
                    $html.= '<td>0</td>';
                }

            }
        }
        $html .='</tr>';
    }
}

$html.= '</table>';
//$html.= '<p>&nbsp;</p>';
//
//$html .='<table width="80%" style="padding:2px;" border="1" align="left">';
//$html.= '<tr>
//            <th width="40%" style="font-weight-:normal;">Количество CROSS-DOCK</th>
//            <th colspan="1" width="20%" style="font-weight-:normal;">'.$CrossDockQty.'</th>
//         </tr>';
//
//$html.= '<tr>
//            <th width="40%" style="font-weight-:normal;">Количество RPT</th>
//            <th colspan="1" width="20%" style="font-weight-:normal;">'.$rptQty.'</th>
//         </tr>';
//
//$html.= '<tr>
//            <th width="40%" style="font-weight-:normal;">Итого</th>
//            <th colspan="1" width="20%" style="font-weight-:normal;">'.($rptQty+$CrossDockQty).'</th>
//         </tr>';
//
//$html.= '</table>';

$pdf->writeHTML($html, true, false, true, false, 'C');

$pdf->lastPage();

$pdf->Output(time() . '-colins-allocate-list.pdf', 'D');
die;