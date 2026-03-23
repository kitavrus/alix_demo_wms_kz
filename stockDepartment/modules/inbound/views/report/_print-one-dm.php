<?php
/* @var $productData array */

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


    $pdf->AddPage('L','NOMADEX40X60', true);
	$productStyle = $productData['product_model'];
	$pdf->SetFont('dejavusans', 'B', 7);
	$pdf->MultiCell(0, 0,$productStyle , 0, 'C',false,1, 0,1);
	$style = array(
		'border' => false,
		'padding' => 0,
		'fgcolor' => array(0,0,0),
		'bgcolor' => false
	);
	$productBarcode = $productData['product_barcode'];
	$dmCode = $productData['data_matrix_code'];;
	$pdf->write2DBarcode($dmCode, 'DATAMATRIX', 34, 5, 40, 40, $style, "N");

	$pdf->SetFont('dejavusans', 'N', 7);
	$pdf->Text(0, 5, $productBarcode);

	$dmCodeSplit = str_split($dmCode, 17);

	$pdf->Text(0, 22, $dmCodeSplit[0]);
	$pdf->Text(0, 24, $dmCodeSplit[1]);

$pdf->Output('receipt_box_label_'.$productBarcode . '-'."data-matrix" . '.pdf', 'D');
Yii::$app->end();