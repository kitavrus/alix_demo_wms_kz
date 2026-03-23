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
// set cell padding
$pdf->setCellPaddings(1, 1, 1, 1);

// set cell margins
$pdf->setCellMargins(1, 1, 1, 1);


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
$ttn = sprintf("%014d",$order->id);
// ---------------------------------------------------------

$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');

// consider changing to A5
$pdf->AddPage('P', 'A4', true);
//$pdf->SetFont('dejavusans', 'B', 15);
$pdf->SetFont('arial', 'b', 10);
$eacImgPath = Yii::getAlias("@web/image/pdf/");
$pdf->Image($eacImgPath . 'logo.png', 0, 10, 0, 0, 'png', 'http://nomadex.com', 'L', false, 300, 'L', false, false, 0, false, false, false);
$pdf->write1DBarcode($ttn, 'C128', 100, '', '100', 25, 1.5, $style, 'R');
$pdf->Ln(10);
$pdf->Cell(0, 0, 'Накладная № '.$order->id, 0, 0, 'C');
$pdf->Ln(15);
//$pdf->SetFont('dejavusans', '', 10);
//$pdf->SetFont('arial', 'b', 10);
$addCustomerPhone = $order->getExtraFieldValueByName('customer_phone_2');
$addRecipientPhone = $order->getExtraFieldValueByName('recipient_phone_2');
//\yii\helpers\VarDumper::dump($addRecipientPhone, 10,true); die;
$structure_table =
    '<table width="100%" cellspacing="15" cellpadding="4" border="0" align="left" cols="1">' .
    '<tr align="left" valign="middle" >' .
    '<td width="50%" align="left" valign="middle" border="1"><b>Отправитель:</b> '
    .$order->routeFrom->contact_full_name.'<br><b>Адрес:</b> '
    .$order->routeFrom->name.'<br><b>Телефон: </b>'
    .$order->routeFrom->phone_mobile;
if($addCustomerPhone){
    $structure_table .='<br><b>Доп.Телефон: </b>'.$addCustomerPhone;
}
$structure_table.='</td><td width="50%" align="left" valign="middle" border="1"><b>Получатель:</b> '
    .$order->routeTo->contact_full_name.'<br><b>Адрес:</b> '
    .$order->routeTo->name.'<br><b>Телефон: </b>'
    .$order->routeTo->phone_mobile;
if($addRecipientPhone){
    $structure_table .='<br><b>Доп.Телефон: </b>'.$addRecipientPhone;
}
$structure_table.='</td>'
    .'</tr>'
    .'<tr>'
    .'<td width="50%" align="left" valign="middle" border="1"><b>Информация о грузе:</b><br>Заявленная стоимость: '
    .Yii::$app->formatter->asCurrency($order->declared_value).'<br>Описание груза: '
    .$order->shipment_description.'<br>Вес (кг): '
    .$order->kg_actual. '<br>Обьем (м³): '
    .$order->mc_actual.'<br>Количество мест: '
    .$order->number_places_actual
    .'<br></td>'
    .'<td width="50%" align="left" valign="middle" border="1"><b>Тип доставки: </b>'
    .$order->getDeliveryMethod().'<br><b>Стоимость доставки: </b> '
    .Yii::$app->formatter->asCurrency($order->price_invoice_with_vat)
    .'</td>'
    .'</tr> </table>';
$pdf->writeHTML($structure_table);
//$pdf->writeHTMLCell(0, 0, '', '', '<b>Отправитель:</b> '.$order->routeFrom->contact_full_name.'<br><b>Адрес:</b> '.$order->routeFrom->name.'<br><b>Телефон: </b>'.$order->routeFrom->phone_mobile, 'LRTB', 1, 0, true, 'L', true);
//$pdf->writeHTMLCell(0, 0, '', '', '<b>Получатель:</b> '.$order->routeTo->contact_full_name. '<br><b>Адрес:</b> '.$order->routeTo->name.'<br><b>Телефон: </b> '.$order->routeTo->phone_mobile, 'LRTB', 1, 0, true, 'L', true);
//$pdf->writeHTMLCell(0, 0, '', '', '<b>Информация о грузе:</b><br>Вес (кг): '.$order->kg_actual. '<br>Обьем (м³): '.$order->mc_actual.'<br>Количество мест: '.$order->number_places_actual, 'LRTB', 1, 0, true, 'L', true);
//$pdf->writeHTMLCell(0, 0, '', '', '<b>Тип доставки:</b>'.$order->getDeliveryMethod().'<br><b>Стоимость:</b> '.Yii::$app->formatter->asCurrency($order->price_invoice_with_vat), 'LRTB', 1, 0, true, 'L', true);
//$pdf->Ln(5);
$pdf->Cell(0, 0, 'Подпись оператора:_________________', 0, 0, 'L');
$pdf->Cell(0, 0, 'Подпись клиента:_________________', 0, 0, 'R');
$pdf->Ln(14);
$pdf->Cell(0, 0, '--------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
$pdf->Ln(14);
$eacImgPath = Yii::getAlias("@web/image/pdf/");
$pdf->Image($eacImgPath . 'logo.png', 0, 155, 0, 0, 'png', 'http://nomadex.com', 'L', false, 300, 'L', false, false, 0, false, false, false);
$pdf->write1DBarcode($ttn, 'C128', 100, '', '100', 25, 1.5, $style, 'R');
$pdf->Ln(10);
$pdf->Cell(0, 0, 'Накладная № '.$order->id, 0, 0, 'C');
$pdf->Ln(15);
//$pdf->SetFont('dejavusans', '', 10);
//$pdf->SetFont('arial', 'b', 10);
$addCustomerPhone = $order->getExtraFieldValueByName('customer_phone_2');
$addRecipientPhone = $order->getExtraFieldValueByName('recipient_phone_2');
//\yii\helpers\VarDumper::dump($addRecipientPhone, 10,true); die;
$structure_table =
    '<table width="100%" cellspacing="15" cellpadding="4" border="0" align="left" cols="1">' .
    '<tr align="left" valign="middle" >' .
    '<td width="50%" align="left" valign="middle" border="1"><b>Отправитель:</b> '
    .$order->routeFrom->contact_full_name.'<br><b>Адрес:</b> '
    .$order->routeFrom->name.'<br><b>Телефон: </b>'
    .$order->routeFrom->phone_mobile;
if($addCustomerPhone){
    $structure_table .='<br><b>Доп.Телефон: </b>'.$addCustomerPhone;
}
$structure_table.='</td><td width="50%" align="left" valign="middle" border="1"><b>Получатель:</b> '
    .$order->routeTo->contact_full_name.'<br><b>Адрес:</b> '
    .$order->routeTo->name.'<br><b>Телефон: </b>'
    .$order->routeTo->phone_mobile;
if($addRecipientPhone){
    $structure_table .='<br><b>Доп.Телефон: </b>'.$addRecipientPhone;
}
$structure_table.='</td>'
    .'</tr>'
    .'<tr>'
    .'<td width="50%" align="left" valign="middle" border="1"><b>Информация о грузе:</b><br>Заявленная стоимость: '
    .Yii::$app->formatter->asCurrency($order->declared_value).'<br>Описание груза: '
    .$order->shipment_description.'<br>Вес (кг): '
    .$order->kg_actual. '<br>Обьем (м³): '
    .$order->mc_actual.'<br>Количество мест: '
    .$order->number_places_actual
    .'<br></td>'
    .'<td width="50%" align="left" valign="middle" border="1"><b>Тип доставки: </b>'
    .$order->getDeliveryMethod().'<br><b>Стоимость доставки: </b> '
    .Yii::$app->formatter->asCurrency($order->price_invoice_with_vat)
    .'</td>'
    .'</tr> </table>';
$pdf->writeHTML($structure_table);
//$pdf->writeHTMLCell(0, 0, '', '', '<b>Отправитель:</b> '.$order->routeFrom->contact_full_name.'<br><b>Адрес:</b> '.$order->routeFrom->name.'<br><b>Телефон: </b>'.$order->routeFrom->phone_mobile, 'LRTB', 1, 0, true, 'L', true);
//$pdf->writeHTMLCell(0, 0, '', '', '<b>Получатель:</b> '.$order->routeTo->contact_full_name. '<br><b>Адрес:</b> '.$order->routeTo->name.'<br><b>Телефон: </b> '.$order->routeTo->phone_mobile, 'LRTB', 1, 0, true, 'L', true);
//$pdf->writeHTMLCell(0, 0, '', '', '<b>Информация о грузе:</b><br>Вес (кг): '.$order->kg_actual. '<br>Обьем (м³): '.$order->mc_actual.'<br>Количество мест: '.$order->number_places_actual, 'LRTB', 1, 0, true, 'L', true);
//$pdf->writeHTMLCell(0, 0, '', '', '<b>Тип доставки:</b>'.$order->getDeliveryMethod().'<br><b>Стоимость:</b> '.Yii::$app->formatter->asCurrency($order->price_invoice_with_vat), 'LRTB', 1, 0, true, 'L', true);
//$pdf->Ln(5);
$pdf->Cell(0, 0, 'Подпись оператора:_________________', 0, 0, 'L');
$pdf->Cell(0, 0, 'Подпись клиента:_________________', 0, 0, 'R');

$pdf->Output(date("d-m-Y-H-i-s") . '-invoice.pdf', 'D');
Yii::$app->end();