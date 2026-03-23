<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:27
 */
////Yii::$app->get('tcpdf');;;


$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('nomadex.com');
$pdf->SetTitle('Nomadex 3PL labels');
$pdf->SetSubject('Namadex  3PL labels');
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

foreach ($boxesBarcode as $barcode) {
    $pdf->AddPage('L', 'NOMADEX30X60', true);

    $style = array(
        'position' => 'C',
        'align' => 'C',
        'stretch' => false,
        'fitwidth' => false,
        'cellfitalign' => '',
        'border' => false,
        'padding' => 0,
        'hpadding' => 0,
        'vpadding' => 0.5,
        'fgcolor' => array(0, 0, 0),
        'bgcolor' => false, //array(255,255,255),
        'text' => true,
        'font' => 'arial',
        'fontsize' => 14,
        'stretchtext' => 4
    );

    $pdf->write1DBarcode($barcode, 'C128', '', '', 50, 13, 0.5, $style, 'N');

    $pdf->SetFont('arial', 'B', 9);

   // $pdf->MultiCell(0, 0, 'nomadex', 0, 'C',false,1, 0,23);
}


$pdf->Output(time() . '-product-label.pdf', 'D');
Yii::$app->end();