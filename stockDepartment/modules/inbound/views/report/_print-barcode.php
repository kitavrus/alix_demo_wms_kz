<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 07.12.2020
 * Time: 16:51
 */

/* @var $newBoxBarcodeList array */

$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('wms 8d.com');
$pdf->SetTitle('wms 8d 3PL labels');
$pdf->SetSubject('wms 8d 3PL labels');
$pdf->SetKeywords('wms 8d.com, receipt, box, label');

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
//
$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');


foreach ($newBoxBarcodeList as $boxBarcode) {

    $pdf->AddPage('L','NOMADEX40X60', true);
//    $pdf->AddPage('L','CUSTOM_SIZE40x60', true);
// set font
//	$pdf->SetFont('helvetica', '', 11);

// add a page
//	$pdf->AddPage();
//    $style = array(
//        'position' => 'C',
//        'align' => 'C',
//        'stretch' => false,
//        'fitwidth' => false,
//        'cellfitalign' => '',
//        'border' => false,
//        'padding' => 0,
//        'hpadding' => 0,
//        'vpadding' => 0.5,
//        'fgcolor' => array(0, 0, 0),
//        'bgcolor' => false, //array(255,255,255),
//        'text' => true,
//        'font' => 'dejavusans',
//        'fontsize' => 14,
//        'stretchtext' => 4
//    );

//    $pdf->write1DBarcode($boxBarcode, 'C128', '', '', 50, 13, 0.5, $style, 'N');
//    $pdf->write2DBarcode($boxBarcode, 'DATAMATRIX', '', '', 50, 13, 0.5, $style, 'N');

//	$style = array(
//		'border' => 2,
//		'vpadding' => 'auto',
//		'hpadding' => 'auto',
//		'fgcolor' => array(0,0,0),
//		'bgcolor' => false, //array(255,255,255)
//		'module_width' => 1, // width of a single module in points
//		'module_height' => 1 // height of a single module in points
//	);

// new style
//	$productStyle = "744SFA0112312T4";
	$productStyle = $boxBarcode['product_model'];
	$pdf->SetFont('dejavusans', 'B', 7);
	$pdf->MultiCell(0, 0,$productStyle , 0, 'C',false,1, 0,1);
	$style = array(
		'border' => false,
		'padding' => 0,
		'fgcolor' => array(0,0,0),
		'bgcolor' => false
	);
//	$productBarcode = "5059862044849";
	$productBarcode = $boxBarcode['product_barcode'];
//	$dmCode = "01050598620448492130M?k5VY*4K/H91KZF092ZascAvCayryBENByxKHoAY/70mA=ZascAvCayryBENByxKHoAY/70mA=ZascAvCayryBENByxKHoAY/70mA=Zasc";
	$dmCode = $boxBarcode['data_matrix_code'];;
//	$dmCodePrefix = "01050598620448492130M?k5VY*4K";
	$pdf->write2DBarcode($dmCode, 'DATAMATRIX', 34, 5, 40, 40, $style, "N");

	$pdf->SetFont('dejavusans', 'N', 7);
	$pdf->Text(0, 5, $productBarcode);

	$dmCodeSplit = str_split($dmCode, 17);

	$pdf->Text(0, 22, $dmCodeSplit[0]);
	$pdf->Text(0, 24, $dmCodeSplit[1]);
//    $dateTime = \common\modules\common\helpers\DateTimeHelper::getCurrentMysqlDateTime();
//    $pdf->MultiCell(0, 0,$dateTime , 0, 'C',false,1, 0,23);
}

$pdf->Output('receipt_box_label_'.count($newBoxBarcodeList) . '-'."data-matrix" . '.pdf', 'D');
Yii::$app->end();