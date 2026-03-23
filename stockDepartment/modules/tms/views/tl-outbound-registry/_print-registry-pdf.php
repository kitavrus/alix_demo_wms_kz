<?php
////Yii::$app->get('tcpdf');;;
$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetAuthor('nmdx.com');
$pdf->SetTitle('nmdx.com');
$pdf->SetSubject('nmdx.com');
$pdf->SetKeywords('nmdx.com');

// remove default header/footer
$pdf->setPrintHeader(false);

$pdf->setPrintFooter(false);

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
$registryBarcode = sprintf("%014d", $model->id);

$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');

// consider changing to A5
$pdf->AddPage('P', 'A4', true);
$pdf->SetFont('arial', '', 12);
$pdf->Ln(5);
$pdf->Cell(0, 0, 'Дата: '.date('Y.m.d H:i:s'), 0, 0, 'R');
$pdf->SetFont('arial', 'b', 15);
$pdf->Ln(6);
$pdf->write1DBarcode($registryBarcode, 'C128', 5, 5, '100', 25, 1.5, $style, 'L');
$pdf->Ln(8);
$pdf->SetFont('arial', '', 10);
$pdf->Cell(0, 0, 'Водитель: '.$model->driver_name.'     Авто: '.$model->car->title. '      Номер: '.$model->driver_auto_number, 0, 0, 'L');
$pdf->Ln(9);
$pdf->SetFont('arial', 'b', 13);
$pdf->Cell(0, 0, 'Лист отгрузки №'.$registryBarcode, 0, 0, 'C');
$pdf->Ln(10);

//$pdf->SetFont('dejavusans', '', 10);
$pdf->SetFont('arial', 'b', 10);

$structure_table = '<table width="100%" cellspacing="0" cellpadding="4" border="1">' .
    '   <tr align="center" valign="middle" >' .
    '      <th width="6%" align="center" valign="middle" border="1"><strong>' . Yii::t('client/forms','ТТН') . '</strong></th>' .
    '      <th width="25%" align="center" valign="middle" border="1"><strong>' . Yii::t('client/forms','Point from') . '</strong></th>' .
    '      <th width="25%" align="center" valign="middle" border="1"><strong>' . Yii::t('client/forms','Point to') . '</strong></th>' .
    '      <th width="10%" align="center" valign="middle" border="1"><strong>' . Yii::t('client/forms','Weight') . '</strong></th>' .
    '      <th width="10%" align="center" valign="middle" border="1"><strong>' . Yii::t('client/forms','Volume') . '</strong></th>' .
    '      <th width="5%" align="center" valign="middle" border="1"><strong>' . Yii::t('client/forms','Места') . '</strong></th>' .
    '      <th width="19%" align="center" valign="middle" border="1"><strong>' . Yii::t('transportLogistics/forms','Orders') . '</strong></th>' .
    '   </tr>';


if ($items = $model->registryItems) {
    $pdf->SetFont('arial', '', 10);
    foreach ($items as $item) {
        $structure_table .= '<tr align="center" valign="middle">
                <td align="left" valign="middle" border="1">'.$item->tl_delivery_proposal_id.'</td>
                <td align="center" valign="middle" border="1" >'.$item->routeFrom->getDisplayFullTitle().'</td>
                <td align="center" valign="middle" border="1" >'.$item->routeTo->getDisplayFullTitle().'</td>
                <td align="center" valign="middle" border="1" >'.$item->weight .'</td>
                <td align="center" valign="middle" border="1" >'.$item->volume.'</td>
                <td align="center" valign="middle" border="1" >'.$item->places.'</td>
                <td align="center" valign="middle" border="1" >'.str_replace(', ', '<br>', $item->getExtraFieldValueByName('orders')).'</td>
            </tr>';



    }
}

$structure_table .= '</table>';

$pdf->writeHTML($structure_table);

$pdf->Ln(5);
$pdf->Cell(0, 0, 'Общий вес (кг): '.$model->weight, 0, 0, 'L');
$pdf->Ln(6);
$pdf->Cell(0, 0, 'Общий объём (м³): '.$model->volume, 0, 0, 'L');
$pdf->Ln(7);
$pdf->Cell(0, 0, 'Всего мест: '.$model->places, 0, 0, 'L');
$pdf->Ln(15);
$pdf->Cell(0, 0, 'Сдал: _____________________', 0, 0, 'L');
$pdf->Cell(0, 0, 'Принял: _____________________', 0, 0, 'R');

$pdf->Output(date("d-m-Y-H-i-s") . '-registry.pdf', 'D');
Yii::$app->end();