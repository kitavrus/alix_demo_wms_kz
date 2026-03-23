<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 06.10.14
 * Time: 14:58
 */
/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposal */

$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetFont('arial', '', 8); //ok
$pdf->SetMargins(10, 5, 10);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

$pdf->AddPage();
$test = 0.0;
$test = Yii::$app->formatter->asDecimal($test,2);

$defactoTOO = "Defacto Retail Store Kz(Дефакто Ретэйл Стор Кз) Товарищество с ограниченной ответственностью";

$clientName = $orderInfo->order->customer_name;// "Игорь Потема";
$storeName = $orderInfo->order->client_StoreName;// "341 KZK ALMATY ADK MALL";
$documentNumber = $orderInfo->order->order_number; // '3763669'; // Номер документа
$orderNumber = substr($orderInfo->order->order_number,0,4); //
$customerAddress = $orderInfo->order->customer_address;
$totalExpected = $orderInfo->order->accepted_qty;
$totalPrice = $orderInfo->order->total_price;
$totalPriceTax = $orderInfo->order->total_price_tax;

$productsInOrder = $orderInfo->items;

$dateTimeCreatedDocument = Yii::$app->formatter->asDatetime(time(),'php:d.m.Y H:i:s'); // Дата составления

$html ='<table width="100%" style="padding:2px" >
<tr>
    <td  width="80%">&nbsp;</td>
    <td  width="20%">Приложение 26<br /> к приказу Министра финансов<br /> Республики Казахстан<br /> от 20 декабря 2012 №562</td>
</tr>
</table>';
$pdf->writeHTML($html, true, false, true, false, '');


$html ='<table width="100%" style="padding:2px" >
<tr>
    <td  width="30%">Организация(индивидуальный предприниматель)</td>
    <td  width="50%" style="border-bottom: 0.2px solid black; padding-top:10px; font-weight:bold;" align="center" >Defacto Retail Store Kz(Дефакто Ретэйл Стор Кз) Товарищество с<br />ограниченной ответственностью</td>
    <td  width="20%"><table width="100%" border="0"   style="padding-top:5px; padding-bottom:5px;"><tr><td width="30%" >ИИН/БИН</td><td width="70%" align="center" style="border: 0.2px solid black; font-weight:bold;">'.$documentNumber.'</td></tr></table> </td>
</tr>
</table>';
$pdf->writeHTML($html, true, false, true, false, '');
$html ='<table width="100%" style="padding:2px"><tr>
    <td  width="80%">&nbsp;</td>
    <td  width="20.4%">
        <table width="100%" border="1"  style="padding:5px">
            <tr><td width="40%" style="background-color:#c2ccd1;" align="center">Номер<br />документа</td><td  width="60%" style="background-color:#c2ccd1" align="center">Дата<br />составления</td></tr>
            <tr><td>'.$orderNumber.'</td><td>'.$dateTimeCreatedDocument.'</td></tr>
        </table>
    </td>
</tr>
</table>';
//$pdf->Ln();
$pdf->writeHTMLCell(0,0,$pdf->GetX(),$pdf->GetY()-5,$html,0,1,false,true,'R');
//$pdf->writeHTML($html, true, false, true, false, '');

$html ='<table width="100%" style="padding:2px" ><tr><td align="center"><h1>НАКЛАДНАЯ НА ОТПУСК ЗАПАСОВ НА</h1></td></tr></table>';
//$pdf->Ln();
$pdf->writeHTMLCell(0,0,$pdf->GetX(),$pdf->GetY()-1,$html,0,1,false,true,'C');
//$pdf->writeHTML($html, true, false, true, false, '');

$html ='<table width="100%" border="1"  style="padding:2px">
<tr>
    <td style="background-color:#c2ccd1;" align="center">Организация(индивидуальный<br/> предприниматель) - отправитель</td>
    <td style="background-color:#c2ccd1;" align="center">Организация(индивидуальный<br/> предприниматель) - получатель</td>
    <td style="background-color:#c2ccd1;" align="center">Ответственный за поставку<br/>(Ф.И.О)</td>
    <td style="background-color:#c2ccd1;" align="center">Транспортная накладная</td>
    <td style="background-color:#c2ccd1;" align="center">Товарно-транспортная накладная<br />(номер,дата)</td>
</tr>
<tr>
    <td>'.$defactoTOO.'</td>
    <td>'.$clientName.'</td>
    <td>'.$storeName.'</td>
    <td></td>
    <td></td>
</tr>
</table>';
//$pdf->Ln();
//$pdf->writeHTMLCell(0,0,$pdf->GetX(),$pdf->GetY(),$html,0,1,false,true,'C');
$pdf->writeHTMLCell(0,0,$pdf->GetX(),$pdf->GetY(),$html,0,1,false,true,'C');
//$pdf->writeHTML($html, true, false, true, false, '');

//$productInOrder[] = ['1'=> '1', '2'=> '2', '3'=> '3', '4'=> '4', '5'=> '5', '6'=> '6', '7'=> '7', '8'=> '8', '9'=> '9'];

$html ='<table width="100%" border="1"  style="padding:2px">
            <tr>
                <td style="background-color:#c2ccd1;" align="center" rowspan="2">Номер по<br/>подряду</td>
                <td style="background-color:#c2ccd1;" align="center" rowspan="2">Наименование, характеристика</td>
                <td style="background-color:#c2ccd1;" align="center" rowspan="2">Номенклатурный номер</td>
                <td style="background-color:#c2ccd1;" align="center" rowspan="2">Единица<br />измерения</td>
                <td style="background-color:#c2ccd1;" align="center" colspan="2">Количество</td>
                <td style="background-color:#c2ccd1;" align="center" rowspan="2">Цена за единицу,<br />в KZT</td>
                <td style="background-color:#c2ccd1;" align="center" rowspan="2">Сумма НДС, в <br /> KZT</td>
                <td style="background-color:#c2ccd1;" align="center" rowspan="2">Сумма НДС, в KZT</td>
            </tr>
            <tr>
                <td style="background-color:#c2ccd1;" align="center">подлежит отпуску</td>
                <td style="background-color:#c2ccd1;" align="center">отпущено</td>
            </tr>
       ';

$rows  = '';
$totalPrice = 0;
$totalPriceTax = 0;
$totalPriceTax2 = 0;
foreach($productsInOrder as $key=>$productRow)
{
    if($productRow->accepted_qty < 1) { continue; }

    $totalPrice += $productRow->product_price;
    $totalPriceTax += $productRow->price_tax;
    $totalPriceTax2 += ($productRow->product_price - $productRow->price_discount);

    $rows .= '
<tr>
    <td>'.($key+1).'</td>
    <td>'.$productRow->product_model.'</td>
    <td>'.$productRow->product_name.'</td>
    <td>AD</td>
    <td>'.$productRow->accepted_qty.'</td>
    <td>'.$productRow->accepted_qty.'</td>
    <td>'.$productRow->product_price.'</td>
    <td>'.($productRow->product_price - $productRow->price_discount).'</td>
    <td>'.$productRow->price_tax.'</td>
</tr>
';
}
//$rows  = '';
$tableEnd = ' </table>';
$html .= $rows.$tableEnd;
$pdf->writeHTMLCell(0,0,$pdf->GetX(),$pdf->GetY()+2,$html,0,1,false,true,'C');
//$pdf->writeHTML($html, true, false, true, false, '');

$html ='<table width="100%" border="1"  style="padding:3px">
<tr>
    <td colspan="4" align="right" style="font-weight:bold;">Итого</td>
    <td>'.$totalExpected.'</td>
    <td>'.$totalExpected.'</td>
    <td>'.$totalPrice.'</td>
    <td>'.$totalPriceTax2.'</td>
    <td>'.$totalPriceTax.'</td>
</tr>
</table>
';

$pdf->writeHTMLCell(0,0,$pdf->GetX(),$pdf->GetY()+5,$html,0,1,false,true,'C');
//$pdf->writeHTML($html, true, false, true, false, '');

$pdf->Ln();
$html ='<table width="100%" border="0">
<tr>
    <td width="20%">Всего отпущено количество запасов</td>
    <td width="26%">____________________________________________</td>
    <td width="54%">на сумму (прописью), в KZT _______________________________________________________________________ </td>
</tr>
</table>';
$pdf->writeHTML($html, true, false, true, false, '');

$html ='<table width="100%" border="0">
<tr>
<td>
<table width="100%" border="0">
        <tr>
            <td>
                <table width="100%" border="0">
                    <tr>
                        <td width="25%">Отпуск разрешил</td>
                        <td>____________________ /</td>
                        <td>____________________ /</td>
                        <td>_____________________</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td align="center" style="font-size: small;">должность</td>
                        <td align="center" style="font-size: small;" >подпись</td>
                        <td align="center" style="font-size: small;">расшифровка подписи</td>
                    </tr>
               </table>
            </td>
        </tr>
        <tr>
            <td>
                <table width="100%" border="0">
                    <tr>
                        <td width="25%">Главный бухгалтер</td>
                        <td width="25%">____________________ /</td>
                        <td width="50%">________________не предусмотрен____________</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">М.П.</td>
                        <td  align="center" style="font-size: small;">подпись</td>
                        <td  align="center" style="font-size: small;">расшифровка подписи</td>
                    </tr>
               </table>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td>
                 <table width="100%" border="0">
                        <tr>
                            <td width="25%">Отпустил</td>
                            <td width="25%">____________________ /</td>
                            <td width="50%">__________________________________________</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td  align="center" style="font-size: small;">подпись</td>
                            <td  align="center" style="font-size: small;">расшифровка подписи</td>
                        </tr>
                 </table>
            </td>
        </tr>
</table>
</td>
<td>
 <table width="100%" border="0" style="padding: 2px">
        <tr>
            <td width="20%">По доверенности</td>
            <td width="40%">№_______________________________</td>
            <td width="40%">от "__"___________________20__года</td>
        </tr>
        <tr>
            <td  width="20%">выданной</td>
            <td colspan="2">____________________________________________________________________</td>
        </tr>
        <tr>
            <td colspan="3">_____________________________________________________________________________________</td>
        </tr>
        <tr>
            <td  width="20%">Запасы получил</td>
            <td>________________________________ /</td>
            <td>_________________________________</td>
        </tr>
        <tr>
            <td></td>
            <td align="center" style="font-size: small;">подпись</td>
            <td align="center" style="font-size: small;">расшифровка подписи</td>
        </tr>
 </table>
</td>
</tr>
</table>
';

$pdf->writeHTML($html, true, false, true, false, '');

// $pdf->writeHTMLCell();

//$pdf->setJPEGQuality(100);
//$eacImgPath = Yii::getAlias("@web/image/pdf/");
//$pdf->Image($eacImgPath . 'logo-nomadex.jpg', 0, 182, 0, 0, 'jpg', 'http://nomadex.kz', 'N', false, 300, 'R', false, false, 0, false, false, false);
$pdf->lastPage();
$pdf->Output(time() . '-ttn.pdf', 'D');
die;