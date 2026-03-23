<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 15.01.15
 * Time: 12:07
 */
////Yii::$app->get('tcpdf');;
///======================================================================
//$pdf->SetCreator(PDF_CREATOR);
//$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetAuthor('nmdx.com');
$pdf->SetTitle('nmdx.com');
$pdf->SetSubject('nmdx.com');
$pdf->SetKeywords('nmdx.com');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(10, 10, 10, true);

//set auto page breaks
$pdf->SetAutoPageBreak(true, 5);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


// ---------------------------------------------------------

$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');

// consider changing to A5
$pdf->AddPage('P', 'A4', true);
//$pdf->SetFont('arial', 'B', 22);
//$pdf->Cell(10, 0, 'Список расхождений по накладной № ' . $order->order_number, 0, 0, 'L');

//$pdf->Ln(10);
$pdf->SetFont('arial', 'B', 15);
$pdf->Cell(0, 0, 'Неразмещенные короба '.date("Y-m-d"), 0, 0, 'R');
$pdf->Ln(10);
$pdf->SetFont('arial', 'b', 10);

//            $pdf->SetLineWidth(0.2);

$structure_table = '<table width="100%" cellspacing="0" cellpadding="4" border="1">' .
    '   <tr align="center" valign="middle" >' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Primary address') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Primary address') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Primary address') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Primary address') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Primary address') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Primary address') . '</strong></th>' .
    '   </tr>';

if (!empty($items)) {
    $data = array_chunk($items, 6);
   // \yii\helpers\VarDumper::dump($data, 10, true); die;
    foreach ($data as $items) {
            $structure_table .= '<tr align="center" valign="middle">';
            foreach($items as $item){
                $structure_table .= '<td align="center" valign="middle" border="1">' . $item['primary_address'] . '</td>';
            }
            $structure_table .= '</tr>';

    }
}

$structure_table .= '</table>';

$pdf->writeHTML($structure_table);
$pdf->Output(date("d-m-Y-H-i-s") . '-unallocated-list.pdf', 'D');
Yii::$app->end();