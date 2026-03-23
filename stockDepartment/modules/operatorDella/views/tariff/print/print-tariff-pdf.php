<?php

////Yii::$app->get('tcpdf');;;

$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetFont('arial', '', 8); //ok
$pdf->SetMargins(10, 5, 10);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->AddPage();

$pdf->setJPEGQuality(100);
$eacImgPath = Yii::getAlias("@web/image/pdf/");
$pdf->Image($eacImgPath . 'logo.png', 0, 10, 0, 0, 'png', 'http://nomadex.com', 'N', false, 300, 'R', false, false, 0, false, false, false);

$html ='<p style="text-align:center"><h1>Тарифы на перевозку по Казахстану</h1></p>';
$html .='<table width="100%" border="1" cellpadding="2px" style="margin-top: 10px">
            <tr>
                <td width="30%" align="center" style="background-color: #eef667">Город</td>
                <td width="30%" align="center" style="background-color: #eef667">Срок доставки</td>
                <td width="20%" align="center" style="background-color: #eef667">Склад-склад (цена за 1 кг*)</td>
                <td width="20%" align="center" style="background-color: #eef667">Дверь-дверь (цена за 1 кг*)</td>
            </tr>
           <tbody>';

if($data){
    foreach ($data as $row){
        $html .=
            '<tr><td>'.$row['to'].'</td>'
            .'<td>'.$row['delivery_term'].'</td>'
            .'<td>'.$row['type_wh'].'</td>'
            .'<td>'.$row['type_dd'].'</td></tr>';
    }

}

$html .= '</tbody>
         </table>';
$pdf->SetFont('arial', 'b', 6);
$html .= '<p>*Цены указаны с НДС при расчете 1м³ = 150 кг.</p>';
$pdf->SetFont('arial', 'b', 8);
if($defaultCity){
    $html .= '<span style="text-align:center"><h1>Стоимость доставки в пределах города и дополнительные услуги: </h1></span>';
    $html .=  '<ul>';
    foreach($defaultCity as $dTariff){
        $html .= '<li>' . $dTariff . '</li>';
    }
    $html .=   '</ul>';
}




$html .= '<br>';


$html .= '<h1><u>Внимание: все цены фиксированные</u></h1>';
$html .= '<span><h1>Контакты: </h1></span>'.
    '<h2>Тел: 87027773850 Кайрат</h2>'.
    '<h2>Тел: 87017164255 Турсун</h2>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->ln(1);

$pdf->lastPage();

$pdf->Output(time() . '-price-list.pdf', 'D');
die;
