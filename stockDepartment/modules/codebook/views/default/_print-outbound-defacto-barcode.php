<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:27
 */

/* @var array $LCItems  */
/* @var integer $quantity  */

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
$pdf->SetMargins(0, 0, 0, true);

//set auto page breaks
$pdf->SetAutoPageBreak(false, 0);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');

$pref = $model->cod_prefix;
if($model->cod_prefix == 'b') {
    $model = \common\modules\codebook\models\Codebook::findOne(1);
//    $pref = $model->cod_prefix;
}

$from = $model->barcode;
$to = $from + $quantity;
//$pref = $model->cod_prefix;
$model->barcode = $to;
$model->save(false);
$from += 1;

$index = 0;
for ($i = $from; $i <= $to; $i++) {

    $pdf->AddPage('L', 'NOMADEX30X60', true);

    $barcode = $pref . sprintf("%010d", $i);
    $barcodeLC = $LCItems[$index++];

   $inboundUnitAddressRepository = new \common\modules\placementUnit\repository\InboundUnitAddressRepository();

    $dto = new \stdClass();
    $dto->codeBookID = $model->id;
    $dto->ourBarcode = $barcode;

    $inboundUnitAddressRepository->create($dto);

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

    $pdf->write1DBarcode($barcode, 'C128', '', '', 50, 12, 0.5, $style, 'N');

    $pdf->Ln(6);
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
//        'text' => true,
        'text' => false,
        'font' => 'arial',
//        'font' => 'helvetica',
        'fontsize' => 14,
//        'stretchtext' => 8
        'stretchtext' => 4
    );
    $pdf->Cell(0, 0, $barcodeLC, 0, 1,'C');
    $pdf->write1DBarcode($barcodeLC, 'C128', '', '', 55, 7, 0.5, $style, 'N');

//    $pdf->SetFont('arial', 'B', 9);

    \common\modules\outbound\service\OutboundBoxService::create($barcode,$barcodeLC);

//    $pdf->MultiCell(0, 0, $barcode, 0, 'C',false,1, 0,15);
//    $pdf->MultiCell(0, 0, 'nomadex', 0, 'C',false,1, 0,23);
//    $pdf->MultiCell(0, 0, 'nomadex', 0, 'C',false,1, 0,23);
//
}


$pdf->Output(time() . '-box-label.pdf', 'D');
Yii::$app->end();