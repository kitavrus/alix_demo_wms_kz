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

    $pdf->AddPage('L', 'NOMADEX30X60', true);

//   $barcode = '2300000078916';

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

    $pdf->write1DBarcode($address, 'C128', '', '', 50, 13, 0.5, $style, 'N');

	$codeBoxPart1 = substr($address, 0, 8);
	$codeBoxPart4 = substr($address, 8, 4);
	$boxFormatText = "&nbsp;&nbsp;&nbsp;&nbsp;".$codeBoxPart1.' <b style="font-size: 8mm; font-weight: bold; ">'.$codeBoxPart4.'</b>';
	$pdf->writeHTML($boxFormatText);
	$pdf->SetFont('arial', 'B', 9);
	$pdf->MultiCell(0, 0, 'EFF', 0, '',false,1, 47,24);


$pdf->Output($address . '-customer-label.pdf', 'D');
Yii::$app->end();