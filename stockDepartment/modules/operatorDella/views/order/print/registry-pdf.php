<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 15.01.15
 * Time: 12:07
 */
////Yii::$app->get('tcpdf');;;
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
$ttn = sprintf("%014d",$registry_id);
// ---------------------------------------------------------

$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');

// consider changing to A5
$pdf->AddPage('P', 'A4', true);
//$pdf->SetFont('dejavusans', 'B', 22);
$pdf->SetFont('arial', 'b', 22);

$pdf->Ln(10);
//$pdf->SetFont('dejavusans', 'B', 15);
$pdf->SetFont('arial', 'b', 15);
$pdf->write1DBarcode($ttn, 'C128', '', '', '100', 25, 1.5, $style, 'L');
$pdf->Ln(11);
$pdf->Cell(0, 0, 'Реестр заказов №'.$registry_id, 0, 0, 'C');
$pdf->Ln(10);
//$pdf->SetFont('dejavusans', '', 10);
$pdf->SetFont('arial', 'b', 10);

//            $pdf->SetLineWidth(0.2);

$structure_table = '<table width="100%" cellspacing="0" cellpadding="4" border="1">' .
    '   <tr align="center" valign="middle" >' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('client/forms','TTN number') . '</strong></th>' .
    '      <th width="25%" align="center" valign="middle" border="1"><strong>' . Yii::t('client/forms','Point from') . '</strong></th>' .
    '      <th width="25%" align="center" valign="middle" border="1"><strong>' . Yii::t('client/forms','Point to') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('client/forms','Weight') . '</strong></th>' .
    '      <th width="10%" align="center" valign="middle" border="1"><strong>' . Yii::t('client/forms','Volume') . '</strong></th>' .
    '      <th width="10%" align="center" valign="middle" border="1"><strong>' . Yii::t('client/forms','Places') . '</strong></th>' .
    '   </tr>';
$weightTotal = 0;
$volumeTotal = 0;
$placesTotal = 0;
if (!empty($items)) {

    foreach ($items as $item) {
        $weightTotal += (!empty($item->kg_actual) ? $item->kg_actual : $item->kg);
        $volumeTotal += (!empty($item->mc_actual) ? $item->mc_actual : $item->kg);
        $placesTotal += (!empty($item->number_places_actual) ? $item->number_places_actual : $item->number_places);
        $structure_table .= '<tr align="center" valign="middle">
                <td align="left" valign="middle" border="1">'.$item->id.'</td>
                <td align="center" valign="middle" border="1">'.$item->routeFrom->title.'</td>
                <td align="center" valign="middle" border="1">'.$item->routeTo->title.'</td>
                <td align="center" valign="middle" border="1">'.(!empty($item->kg_actual) ? $item->kg_actual : $item->kg) .'</td>
                <td align="center" valign="middle" border="1">'.(!empty($item->mc_actual) ? $item->mc_actual : $item->mc).'</td>
                <td align="center" valign="middle" border="1">'.(!empty($item->number_places_actual) ? $item->number_places_actual : $item->number_places).'</td>
            </tr>';

    }
}

$structure_table .= '</table>';

$pdf->writeHTML($structure_table);

$pdf->Ln(5);
$pdf->Cell(0, 0, 'Общий вес (кг): '.$weightTotal, 0, 0, 'L');
$pdf->Ln(6);
$pdf->Cell(0, 0, 'Общий объём (м³): '.$volumeTotal, 0, 0, 'L');
$pdf->Ln(7);
$pdf->Cell(0, 0, 'Всего мест: '.$placesTotal, 0, 0, 'L');

$pdf->Output(date("d-m-Y-H-i-s") . '-registry.pdf', 'D');
Yii::$app->end();