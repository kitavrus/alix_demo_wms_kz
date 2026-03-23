<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 13.04.2016
 * Time: 17:14
 */

////Yii::$app->get('tcpdf');;;


$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('nomadex.com');
$pdf->SetTitle('Nomadex labels');
$pdf->SetSubject('Namadex labels');
$pdf->SetKeywords('nomadex.com, box, product, etc , label');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
//set margins
$pdf->SetMargins(2, 2, 2, true);

//set auto page breaks
$pdf->SetAutoPageBreak(false, 0);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');

$pdf->AddPage("P");

$style = array(
    'position' => 'C',
    'align' => 'C',
    'stretch' => false,
//        'stretch' => true,
    'fitwidth' => false,
//        'fitwidth' => true,
    'cellfitalign' => '',
    'border' => false,
    'padding' => 0,
    'hpadding' => 0,
    'vpadding' => 0.5,
    'fgcolor' => array(0, 0, 0),
    'bgcolor' => false, //array(255,255,255),
    'text' => true,
//        'text' => false,
    'font' => 'arial',
//        'font' => 'helvetica',
    'fontsize' => 35,
//        'stretchtext' => 8
    'stretchtext' => 8
);

//$pdf->write1DBarcode($palletBarcode, 'C128', '100', '', 250, 25, 0.5, $style, 'N');
$codePart1 = substr($palletBarcode, 0, 2);
$codePart2 = substr($palletBarcode, 2, 4);
$codePart3 = substr($palletBarcode, 6, 4);
$codePart4 = substr($palletBarcode, 10, 4);
$formattedPalletBarcode = '<span style="font-size: 15mm; font-weight: normal;">'.$codePart1.$codePart2.'</span><span style="font-size: 45mm; font-weight: bold;">'.$codePart3.$codePart4.'</span>';

$pdf->SetFont('arial', 'b', 8);

$htmlBox = '<table border="0" cellspacing="0" cellpadding="0"  style="text-align: center; line-height: 2; width: 100%">'.
    '<tr>'
    .'<td style="text-align: center; width: 100%">'
    .$formattedPalletBarcode
    .'</td>'

    .'</tr>';
$htmlBox .= '</table>';
//$pdf->SetFont('arial', 'b', 85);
$pdf->writeHTMLCell(0, 0, 0, 95, $formattedPalletBarcode,0,0,false,true,'C');
//$pdf->writeHTMLCell(0, 0, 0, 95, $formattedPalletBarcode,0,0,false);
//$pdf->MultiCell(0, 0, $palletBarcode, 0, 'C',false,1, 0,95);
//$pdf->SetFont('arial', 'B', 12);
//$pdf->MultiCell(0, 0, 'nomadex', 0, 'C',false,1, 0,150);

$pdf->lastPage();
$dirPath = 'uploads/pallet-barcode-label/'.date('Ymd').'/'.date('His');
$fileName = $palletBarcode.'-pallet-barcode-label-'.Yii::$app->getSecurity()->generateRandomString(12).'.pdf';
\yii\helpers\BaseFileHelper::createDirectory($dirPath);
$fullPath = $dirPath.'/'.$fileName;
$pdf->Output($fullPath, 'F');

//if(file_exists($fullPath)){
//    $boxLabel = new \common\modules\outbound\models\OutboundBoxLabels();
//    $boxLabel->client_id = $outboundOrderModel->client_id;
//    $boxLabel->outbound_order_id = $outboundOrderModel->id;
//    $boxLabel->outbound_order_number = $outboundOrderModel->order_number;
//    $boxLabel->box_label_url = $fullPath;
//    $boxLabel->filename = $fileName;
//    $boxLabel->save(false);
//}
return Yii::$app->response->sendFile($fullPath,$fileName);