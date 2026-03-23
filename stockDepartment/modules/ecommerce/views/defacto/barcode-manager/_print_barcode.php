<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:27
 */

$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('nomadex.com');
$pdf->SetTitle('Nomadex 3PL labels');
$pdf->SetSubject('Namadex 3PL labels');
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

$pref = $model->barcode_prefix;

$from = $model->counter;
$to = $from + $quantity;
$model->counter = $to;
$model->save(false);
$from += 1;


for ($i = $from; $i <= $to; $i++) {

    $pdf->AddPage('L', 'NOMADEX30X60', true);

    $barcode = $pref . sprintf("%010d", $i);
    if ($model->id == 1) {
        \common\ecommerce\defacto\barcodeManager\service\BarcodeService::createInbound($barcode);
    } elseif($model->id == 2) {
        \common\ecommerce\defacto\barcodeManager\service\BarcodeService::createOutbound($barcode);
    }

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
        'fontsize' => 14,
//        'stretchtext' => 8
        'stretchtext' => 4
    );

    $pdf->write1DBarcode($barcode, 'C128', '', '', 50, 13, 0.5, $style, 'N');
//    $pdf->write1DBarcode($barcode, 'C128', '', '', '', 13, 0.5, $style, 'N');

    $pdf->SetFont('arial', 'B', 9);

//    $pdf->MultiCell(0, 0, $barcode, 0, 'C',false,1, 0,15);
//    $pdf->MultiCell(0, 0, 'nomadex', 0, 'C',false,1, 0,23);
    $pdf->MultiCell(0, 0, 'nomadex', 0, 'C',false,1, 0,23);
//
}


$pdf->Output(time() . '-product-label.pdf', 'D');
Yii::$app->end();