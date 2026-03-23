<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 06.10.14
 * Time: 14:58
 */

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposal */

$pdf->AddPage();

$headerRightTop ='<table width="100%" style="padding:2px"  border="0">
            <tr>
                <td width="100%" style="font-weight:normal;">Приложение 9</td>
            </tr>
             <tr>
                <td width="100%" style="font-weight:normal;"> к Правилам перевозок грузов</td>
            </tr>
            <tr>
                <td width="100%" style="font-weight:normal;">автомобильным транспортом</td>
            </tr>
        </table>';

$pdf->writeHTMLCell(0,0,230,5,$headerRightTop, 0, 0, false, true, 'R');

$y = $pdf->GetY();

$headerLeft ='<table width="100%" style="padding:2px"  border="0">
            <tr>
                <td width="20%" style="font-weight:normal;">1-й экз. - грузоотправителю</td>
                 <td width="5%" style="font-weight:normal;">&nbsp;</td>
            </tr>
             <tr>
                <td width="20%" style="font-weight:normal;">2-й экз. - грузополучателю</td>
                <td width="5%" style="font-weight:normal;">Коды</td>
            </tr>
            <tr>
                <td width="20%" style="font-weight:normal;">3-й и 4-й экз. - автопредприятию</td>
                 <td width="5%" style="font-weight:normal;">&nbsp;</td>
            </tr>
        </table>';

$pdf->writeHTMLCell(0,0,0,$y+20,$headerLeft);


$headerCenter ='<table width="100%" style="padding:2px"  border="1">
            <tr>
                <td width="10%" height="28px" style="font-weight:normal;">&nbsp;</td>
                <td width="5%" style="font-weight:normal;">&nbsp;</td>
                <td width="5%" style="font-weight:normal;">&nbsp;</td>
                <td width="5%" style="font-weight:normal;">&nbsp;</td>
                <td width="5%" style="font-weight:normal;">&nbsp;</td>
                <td width="5%" style="font-weight:normal;">&nbsp;</td>
                <td width="5%" style="font-weight:normal;">&nbsp;</td>
            </tr>
             <tr>
                <td width="10%" style="font-weight:normal;">&nbsp;</td>
                <td width="5%" style="font-weight:normal;">&nbsp;</td>
                <td width="5%" style="font-weight:normal;">&nbsp;</td>
                <td width="5%" style="font-weight:normal;">&nbsp;</td>
                <td width="5%" style="font-weight:normal;">&nbsp;</td>
                <td width="5%" style="font-weight:normal;">&nbsp;</td>
                <td width="5%" style="font-weight:normal;">&nbsp;</td>
            </tr>
        </table>';

$pdf->writeHTMLCell(0,0,80,$y+20,$headerCenter);


$headerRight='<table width="100%" style="padding:2px"  border="0">
            <tr>
                <td width="55%" style="font-weight:bold;">ТОВАРНО-ТРАНСПОРТНАЯ НАКЛАДНАЯ №</td>
            </tr>
             <tr>
                <td width="5%" style="font-weight:normal;">от</td>
            </tr>
            <tr>
                <td width="50%" style="font-weight:bold;">Автомобиль</td>
            </tr>
            <tr>
                <td width="50%" style="font-weight:normal;">марка, гос.номер</td>
            </tr>
        </table>';

$pdf->writeHTMLCell(0,0,165,$y+20,$headerRight);


$headerRight2Box = '<table style="padding:2px"  border="1"><tr><td width="10%" style="font-weight:normal;">'.$dateTime['day'].'</td> <td width="30%" style="font-weight:normal;">'.$dateTime['monthYear'].'</td></tr></table>';

$pdf->writeHTMLCell(0,0,175,$y+25,$headerRight2Box);


$headerRight1Box = '<table width="25%" style="padding:2px"  border="1"><tr><td style="font-weight:normal;">'.$ttnNumber.'</td></tr></table>';

$pdf->writeHTMLCell(0,0,240,$y+20,$headerRight1Box);


$header7Rows = '<table width="100%" style="padding:2px"  border="1"><tr><td width="59px" height="20px" style="font-weight:normal;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr><tr><td width="59px" height="20px" style="font-weight:normal;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr><tr><td width="59px" height="20px" style="font-weight:normal;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr><tr><td width="59px" height="20px" style="font-weight:normal;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr><tr><td width="59px" height="20px" style="font-weight:normal;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr><tr><td width="59px" height="20px" style="font-weight:normal;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr><tr><td width="59px" height="20px" style="font-weight:normal;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr><tr><td width="59px" height="20px" style="font-weight:normal;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>';

$pdf->writeHTMLCell(0,0,275,$y+40,$header7Rows);


$headerContactData = '
<table width="100%" style="padding:2px"  border="0">
            <tr>
                <td width="12%" style="font-weight:normal;">Автопредприятие </td>
                <td width="42%" style="font-weight:normal;">ТОО "'.$clientName.'"</td>
                <td width="9%" style="font-weight:normal;">Водитель</td>
                <td width="12%" style="font-weight:normal;">&nbsp;</td>
                <td width="20%" style="font-weight:normal;">Вид перевозки__________________  код</td>
            </tr>
            <tr>
                <td width="12%" style="font-weight:normal;">&nbsp;</td>
                <td width="42%" style="font-weight:normal;" >наименование</td>
                <td width="9%" style="font-weight:normal;"></td>
                <td width="12%" style="font-weight:normal;"></td>
                <td width="20%" style="font-weight:normal;"></td>
            </tr>
             <tr>
                <td width="12%" style="font-weight:normal;">Заказчик (плательщик) </td>
                <td width="42%" style="font-weight:normal;">ТОО "'.$clientName.'"  код</td>
                <td width="9%" style="font-weight:normal;"></td>
                <td width="12%" style="font-weight:normal;"></td>
                <td width="20%" style="font-weight:normal;"></td>
            </tr>
            <tr>
                <td width="12%" style="font-weight:normal;">&nbsp;</td>
                <td width="42%" style="font-weight:normal;">наименование</td>
                <td width="9%" style="font-weight:normal;"></td>
                <td width="12%" style="font-weight:normal;"></td>
                <td width="20%" style="font-weight:normal;"></td>
            </tr>
            <tr>
                <td width="12%" style="font-weight:normal;">Грузоотправитель</td>
                 <td width="42%" style="font-weight:normal;">ТОО "Nomadex" код</td>
                 <td width="9%" style="font-weight:normal;"></td>
                <td width="12%" style="font-weight:normal;"></td>
                <td width="20%" style="font-weight:normal;"></td>
            </tr>
            <tr>
                <td width="12%" style="font-weight:normal;">&nbsp;</td>
                <td width="42%" style="font-weight:normal;">наименование</td>
                <td width="9%" style="font-weight:normal;"></td>
                <td width="12%" style="font-weight:normal;"></td>
                <td width="20%" style="font-weight:normal;"></td>
            </tr>
            <tr>
                <td width="12%" style="font-weight:normal;">Грузополучатель</td>
                 <td width="42%" style="font-weight:normal;">'.$endPointCompanyName.'</td>
                 <td width="9%" style="font-weight:normal;"></td>
                <td width="12%" style="font-weight:normal;"></td>
                <td width="20%" style="font-weight:normal;"></td>
            </tr>
            <tr>
                <td width="12%" style="font-weight:normal;">&nbsp;</td>
                <td width="42%" style="font-weight:normal;">наименование</td>
                <td width="9%" style="font-weight:normal;"></td>
                <td width="12%" style="font-weight:normal;"></td>
                <td width="20%" style="font-weight:normal;"></td>
            </tr>
            <tr>
                <td width="12%" style="font-weight:normal;">Пункт погрузки</td>
                 <td width="42%" style="font-weight:normal;">050030,  Казахстан, обл Алматинская, Город Алматы, пр-кт Суюнбая, дом 258, корп. В</td>
                 <td width="9%" style="font-weight:normal;">Пункт разгрузки</td>
                <td width="20%" style="font-weight:normal;">'.$endPointAddress.'</td>
                <td width="12%" style="font-weight:normal;">Маршрут №</td>
            </tr>
            <tr>
                <td width="12%" style="font-weight:normal;"></td>
                 <td width="42%" style="font-weight:normal;">адрес</td>
                 <td width="9%" style="font-weight:normal;"></td>
                <td width="12%" style="font-weight:normal;">адрес</td>
                <td width="20%" style="font-weight:normal;"></td>
            </tr>
        </table>
';
$pdf->writeHTMLCell(0,0,0,$y+40,$headerContactData);


$headerReaddressing = '
<table width="100%" style="padding:2px"  border="0">
            <tr>
                <td width="57%" style="font-weight:normal;">Переадресовка ______________________________________________________________________ 1.</td>
                <td width="37%" style="font-weight:normal;">Прицеп_____________________________ Гар.№</td>
            </tr>
            <tr>
                <td width="57%" style="font-weight:normal;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;наименование и адрес нового грузополучателя</td>
                <td width="37%" style="font-weight:normal;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;гос.№</td>
            </tr>
            <tr>
                <td width="57%" style="font-weight:normal;">___________________________________________________________________________________ 2.</td>
                <td width="37%" style="font-weight:normal;">Прицеп_____________________________ Гар.№</td>
            </tr>
            <tr>
                <td width="57%" style="font-weight:normal;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;наименование и адрес нового грузополучателя</td>
                <td width="37%" style="font-weight:normal;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;гос.№</td>
            </tr>
        </table>
';
$pdf->writeHTMLCell(0,0,0,$y+94,$headerReaddressing);


$rowWidth3 = '12%';
$rowWidth4 = '3%';
$productInOrderHeader = '
<table width="103%" style="padding:2px"  border="1">
            <tr >
                <td width="101%" height="20px" style="font-weight:bold; text-align: center;" colspan="14">Сведения о грузе*)</td>
            </tr>
            <tr>
                <td style="font-weight:bold; text-align: center;">Номенкла турный №, код</td>
                <td style="font-weight:bold; text-align: center;">№ прейск. позиция</td>
                <td style="font-weight:bold; text-align: center;" width="'.$rowWidth3.'">Наименование продукции товара (груза) или номера контейнеров</td>
                <td style="font-weight:bold; text-align: center;" width="'.$rowWidth4.'" >Ед. изм.</td>
                <td style="font-weight:bold; text-align: center;">Количество</td>
                <td style="font-weight:bold; text-align: center;">Цена</td>
                <td style="font-weight:bold; text-align: center;">Сумма</td>
                <td style="font-weight:bold; text-align: center;">С грузом следуют</td>
                <td style="font-weight:bold; text-align: center;">Вид упаковки</td>
                <td style="font-weight:bold; text-align: center;">К-во мест</td>
                <td style="font-weight:bold; text-align: center;">Способ определения массы</td>
                <td style="font-weight:bold; text-align: center;">Код груза</td>
                <td style="font-weight:bold; text-align: center;">Класс груза</td>
                <td style="font-weight:bold; text-align: center;">Масса брутто,т</td>
            </tr>
            <tr>
                <td  style="font-weight:bold; text-align: center;">1</td>
                <td  style="font-weight:bold; text-align: center;">2</td>
                <td  style="font-weight:bold; text-align: center;" width="'.$rowWidth3.'" >3</td>
                <td  style="font-weight:bold; text-align: center;" width="'.$rowWidth4.'">4</td>
                <td  style="font-weight:bold; text-align: center;">5</td>
                <td  style="font-weight:bold; text-align: center;">6</td>
                <td  style="font-weight:bold; text-align: center;">7</td>
                <td  style="font-weight:bold; text-align: center;">8</td>
                <td  style="font-weight:bold; text-align: center;">9</td>
                <td  style="font-weight:bold; text-align: center;">10</td>
                <td  style="font-weight:bold; text-align: center;">11</td>
                <td  style="font-weight:bold; text-align: center;">12</td>
                <td  style="font-weight:bold; text-align: center;">13</td>
                <td  style="font-weight:bold; text-align: center;">14</td>
            </tr>
';

$productInOrderBody = '';

foreach($outboundOrderItems['products'] as $orderItem) {
    $productInOrderBody .= '
<tr>
    <td style="font-weight:normal; text-align: center;">'.$orderItem['productBarcode'].'</td>
    <td style="font-weight:normal; text-align: center;">'.$orderItem['orderNumber'].'</td>
    <td style="font-weight:normal; text-align: center;" width="'.$rowWidth3.'">'.mb_strtolower($orderItem['productName'],'UTF-8').'</td>
    <td style="font-weight:normal; text-align: center;" width="'.$rowWidth4.'">шт</td>
    <td style="font-weight:normal; text-align: center;">'.$orderItem['acceptedQty'].'</td>
    <td style="font-weight:normal; text-align: center;"></td>
    <td style="font-weight:normal; text-align: center;">0</td>
    <td style="font-weight:normal; text-align: center;"></td>
    <td style="font-weight:normal; text-align: center;">'.$orderItem['boxBarcode'].'</td>
    <td style="font-weight:normal; text-align: center;"></td>
    <td style="font-weight:normal; text-align: center;"></td>
    <td style="font-weight:normal; text-align: center;"></td>
    <td style="font-weight:normal; text-align: center;"></td>
    <td style="font-weight:normal; text-align: center;"></td>
</tr>
';
}

$productInOrderFooter = '<tr>
                <td style="font-weight:normal; text-align: center;"></td>
                <td style="font-weight:normal; text-align: center;"></td>
                <td style="font-weight:normal; text-align: center;" width="'.$rowWidth3.'">Итого</td>
                <td style="font-weight:normal; text-align: center;" width="'.$rowWidth4.'" ></td>
                <td style="font-weight:normal; text-align: center;">'.$outboundOrderItems['totalProductQty'].'</td>
                <td style="font-weight:normal; text-align: center;"></td>
                <td style="font-weight:normal; text-align: center;"></td>
                <td style="font-weight:normal; text-align: center;"></td>
                <td style="font-weight:normal; text-align: center;"></td>
                <td style="font-weight:normal; text-align: center;">'.$outboundOrderItems['totalBoxQty'].' </td>
                <td style="font-weight:normal; text-align: center;">кол.ездок,заездов</td>
                <td style="font-weight:normal; text-align: center;"></td>
                <td style="font-weight:normal; text-align: center;"></td>
                <td style="font-weight:normal; text-align: center;">0</td>
            </tr>
        </table>
';

$productInOrder = $productInOrderHeader.$productInOrderBody.$productInOrderFooter;
$pdf->SetFont('arial', '', 5); //ok
$pdf->writeHTMLCell(0,0,0,$y+120,$productInOrder,0,1,false,true,'',false);
$pdf->SetFont('arial', 'b', 8); //ok

$sumLine = '
<table width="100%" style="padding:2px"  border="0">
            <tr>
                <td width="50%" style="font-weight:normal;">Всего отпущено на сумму __________________________________________________</td>
                <td width="50%" style="font-weight:normal;">Отпуск разрешил '.$passedUserName.' '.$positionUser.'</td>
            </tr>
            <tr>
                <td width="50%" style="font-weight:normal; text-align: center; ">прописью</td>
                <td width="50%" style="font-weight:normal; text-align: center; ">подпись, должность</td>
            </tr>
        </table>
';
//$pdf->AddPage();
$y = $pdf->GetY();
$pdf->writeHTMLCell(0,0,0,$y+5,$sumLine,0,1);
//$pdf->writeHTMLCell(0,0,0,$y-3,$sumLine,0,1);

$table3Cell = '
<table width="100%" style="padding:2px"  border="1">
            <tr>
                <td width="33.3%" style="font-weight:normal;">
                <table width="100%" style="padding:2px"  border="0">
                        <tr>
                            <td width="80%" style="font-weight:normal;">Указанный груз за испр.</td>
                            <td width="20%" style="font-weight:normal;">Кол. мест</td>
                        </tr>
                        <tr>
                            <td width="80%" style="font-weight:normal;">пломбой, тарой и упаковкой______________</td>
                            <td width="20%" style="font-weight:normal;">'.$outboundOrderItems['totalBoxQtyText'].'</td>
                        </tr>
                        <tr>
                            <td width="80%" style="font-weight:normal; text-align: right;">оттиск&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="20%" style="font-weight:normal;">прописью</td>
                        </tr>
                        <tr>
                            <td width="100%" style="font-weight:normal;">Массой брутто, т._______________________________________к перевозке</td>
                        </tr>
                        <tr>
                            <td width="100%" style="font-weight:normal; text-align: center" >прописью</td>
                        </tr>
                        <tr>
                            <td width="100%" style="font-weight:normal; text-align: left" >Сдал '.$positionUser.' '.$passedUserName.'</td>
                        </tr>
                        <tr>
                            <td width="100%" style="font-weight:normal; text-align: center" >должность,ФИО, подпись, штамп</td>
                        </tr>
                        <tr>
                            <td width="100%" style="font-weight:normal; text-align: left" >Принят водит.экспедитор </td>
                        </tr>
                        <tr>
                            <td width="100%" style="font-weight:normal; text-align: center" >ФИО водителя, подпись</td>
                        </tr>
                        <tr>
                            <td width="100%" style="font-weight:normal; text-align: left" >Перевозчик принимает груз  по коробам и не несет ответственности за содержимые ТМЦ в коробе, в случае если в момент доставки груза, короба не повреждены и не вскрыты, за исключением Хрупкого груза. Хрупкий груз досмотрен, повреждения отсутствуют.</td>
                        </tr>
                    </table>
                </td>
                <td width="33.3%" style="font-weight:normal;">
                    <table width="100%" style="padding:2px"  border="0">
                        <tr>
                            <td width="80%" style="font-weight:normal;">Указанный груз за испр.</td>
                            <td width="20%" style="font-weight:normal;">Кол. мест</td>
                        </tr>
                        <tr>
                            <td width="80%" style="font-weight:normal;">пломбой, тарой и упаковкой______________</td>
                            <td width="20%" style="font-weight:normal;">'.$outboundOrderItems['totalBoxQtyText'].'</td>
                        </tr>
                        <tr>
                            <td width="80%" style="font-weight:normal; text-align: right;">оттиск&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="20%" style="font-weight:normal;">прописью</td>
                        </tr>
                        <tr>
                            <td width="100%" style="font-weight:normal;">Массой брутто, т._____________________________________</td>
                        </tr>
                        <tr>
                            <td width="100%" style="font-weight:normal; text-align: center" >прописью</td>
                        </tr>
                        <tr>
                            <td width="100%" style="font-weight:normal; text-align: left" >Сдал водит.экспедитор_____________________________________</td>
                        </tr>
                        <tr>
                            <td width="100%" style="font-weight:normal; text-align: center" >должность,ФИО, подпись, штамп</td>
                        </tr>
                        <tr>
                            <td width="100%" style="font-weight:normal; text-align: left" >Принял _____________________________________________</td>
                        </tr>
                        <tr>
                            <td width="100%" style="font-weight:normal; text-align: center">должность, ФИО, подпись, штамп</td>
                        </tr>
                    </table>
                </td>
                <td width="33.3%" style="font-weight:normal;">
                     <table width="100%" style="padding:2px"  border="0">
                        <tr>
                            <td width="100%" style="font-weight:normal;">По доверенности № _____от "___"______20__г..</td>
                        </tr>
                        <tr>
                            <td width="100%" style="font-weight:normal;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="100%" style="font-weight:normal; text-align: right;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="100%" style="font-weight:normal;">выданной __________________________________</td>
                        </tr>
                        <tr>
                            <td width="100%" style="font-weight:normal; text-align: center"></td>
                        </tr>
                        <tr>
                            <td width="100%" style="font-weight:normal; text-align: left">Груз получил _______________________________</td>
                        </tr>
                        <tr>
                            <td width="100%" style="font-weight:normal; text-align: center">должность, ФИО</td>
                        </tr>
                        <tr>
                            <td width="100%" style="font-weight:normal; text-align: left">_____________________________________________</td>
                        </tr>
                        <tr>
                            <td width="100%" style="font-weight:normal; text-align: center">подпись грузополучателя</td>
                        </tr>
                    </table>
                    </td>
            </tr>
        </table>
';

$pdf->writeHTMLCell(0,0,0,$y+18,$table3Cell,0,1);

$y = $pdf->GetY();
$lastCell = 3;
$productInOrder = '
<table width="100%" style="padding:2px"  border="1">
            <tr>
                <td height="20px" style="font-weight:bold; text-align: center;" colspan="'.(10+$lastCell).'">ПОГРУЗОЧНО - РАЗГРУЗОЧНЫЕ ОПЕРАЦИИ</td>
            </tr>
            <tr>
                <td rowspan="2" style="font-weight:bold; text-align: center;">Операция</td>
                <td rowspan="2" style="font-weight:bold; text-align: center;">Исполн. (первозч., отправит., получат.)</td>
                <td colspan="2" style="font-weight:bold; text-align: center;">Способ</td>
                <td colspan="2" style="font-weight:bold; text-align: center;">Время, час., мин.</td>
                <td style="font-weight:bold; text-align: center;"></td>
                <td colspan="2" style="font-weight:bold; text-align: center;">Дополнительные операции</td>
                <td rowspan="2" style="font-weight:bold; text-align: center;">Подпись ответ.лица</td>
            </tr>
            <tr>
                <td style="font-weight:bold; text-align: center;">руч.нал.мех. грузопод.емк.ковша</td>
                <td style="font-weight:bold; text-align: center;">код</td>
                <td style="font-weight:bold; text-align: center;">Прибытия</td>
                <td style="font-weight:bold; text-align: center;">Убытия</td>
                <td style="font-weight:bold; text-align: center;">простоя</td>
                <td style="font-weight:bold; text-align: center;">время, мин.</td>
                <td style="font-weight:bold; text-align: center;">наименование, кол-во</td>

                <td colspan="'.($lastCell).'" style="font-weight:bold; text-align: left;"><br /><br /><br />Транспортные услуги</td>
            </tr>
            <tr>
                <td height="5px"  style="font-weight:bold; text-align: center;"></td>
                <td height="5px"  style="font-weight:bold; text-align: center;">15</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">16</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">17</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">18</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">19</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">20</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">21</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">22</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">23</td>

                <td colspan="'.($lastCell).'" height="5px" style="font-weight:bold; text-align: left;"></td>
            </tr>
            <tr>
                <td style="font-weight:bold; text-align: left;" colspan="10">погр.</td>

                <td colspan="'.($lastCell).'" style="font-weight:bold; text-align: center;"></td>
            </tr>
            <tr>
                <td style="font-weight:bold; text-align: left;" colspan="10">разгр.</td>

                <td colspan="'.($lastCell).'" style="font-weight:bold; text-align: center;"></td>
            </tr>
        </table>
';

$pdf->writeHTMLCell(0,0,0,$y+2,$productInOrder,0,1);

$y = $pdf->GetY();
$lastCell = 4;
$productInOrder = '
<table width="100%" style="padding:2px"  border="1">
            <tr>
                <td height="20px" style="font-weight:bold; text-align: center;" colspan="'.(13+$lastCell).'">ПРОЧИЕ СВЕДЕНИЯ ( заполняется автопредприятием)</td>

            </tr>
            <tr>
                <td colspan="5" style="font-weight:bold; text-align: center;">расстояние перевозки по группам  дорог, км</td>
                <td rowspan="2" style="font-weight:bold; text-align: center;">код экспл.</td>
                <td colspan="2" style="font-weight:bold; text-align: center;">за транспорт. услуги</td>
                <td colspan="2" style="font-weight:bold; text-align: center;">поправочн. коэф.</td>
                <td rowspan="2" style="font-weight:bold; text-align: center; vertical-align: bottom;">Штраф</td>
                <td rowspan="2" style="font-weight:bold; text-align: center;"></td>
                <td rowspan="2" style="font-weight:bold; text-align: center;"></td>

                <td colspan="'.($lastCell).'" style="font-weight:bold; text-align: left;">Отметки о составленных актах:</td>
            </tr>
            <tr>
                <td style="font-weight:bold; text-align: center;">всего</td>
                <td style="font-weight:bold; text-align: center;">в гор</td>
                <td style="font-weight:bold; text-align: center;">1 гр.</td>
                <td style="font-weight:bold; text-align: center;">II гр.</td>
                <td style="font-weight:bold; text-align: center;">III гр.</td>
                <td style="font-weight:bold; text-align: center;">с клиента</td>
                <td style="font-weight:bold; text-align: center;">водителю</td>
                <td style="font-weight:bold; text-align: center;">расцен.вод.</td>
                <td style="font-weight:bold; text-align: center;">осн.тариф</td>

                <td colspan="'.($lastCell).'" style="font-weight:bold; text-align: left;"></td>
            </tr>
            <tr>
                <td height="5px"  style="font-weight:bold; text-align: center;">24</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">25</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">26</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">27</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">28</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">29</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">30</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">31</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">32</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">33</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">34</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">35</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">36</td>

                <td colspan="'.($lastCell).'" style="font-weight:bold; text-align: left;"></td>
            </tr>
            <tr>
                <td height="5px"  style="font-weight:bold; text-align: center;">&nbsp;</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">&nbsp;</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">&nbsp;</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">&nbsp;</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">&nbsp;</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">&nbsp;</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">&nbsp;</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">&nbsp;</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">&nbsp;</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">&nbsp;</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">&nbsp;</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">&nbsp;</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">&nbsp;</td>

                <td colspan="'.($lastCell).'" style="font-weight:bold; text-align: left;"></td>
            </tr>
        </table>
';

$pdf->writeHTMLCell(0,0,0,$y+2,$productInOrder,0,1);

$y = $pdf->GetY();
$lastCell = 2;
$productInOrder = '
<table width="100%" style="padding:2px"  border="1">
            <tr>
                <td rowspan="2" style="font-weight:bold; text-align: center;">Расчет стоимости</td>
                <td rowspan="2" style="font-weight:bold; text-align: center;">За тонны</td>
                <td rowspan="2" style="font-weight:bold; text-align: center;">Недогруз автом. и прицепа</td>
                <td rowspan="2" style="font-weight:bold; text-align: center;">За спец. трансп.</td>
                <td rowspan="2" style="font-weight:bold; text-align: center; ">Транспорт. услуги</td>
                <td rowspan="2" style="font-weight:bold; text-align: center; ">Погр.разгр.раб.(тонн)</td>
                <td colspan="2" style="font-weight:bold; text-align: center;">Сверхномат. простой</td>
                <td rowspan="2" style="font-weight:bold; text-align: center;">Прочие доплаты</td>
                <td rowspan="2" style="font-weight:bold; text-align: center;"></td>
                <td rowspan="2" style="font-weight:bold; text-align: center;">Скидки за сокр. простоя</td>
                <td rowspan="2" style="font-weight:bold; text-align: center;">Всего</td>

                <td colspan="'.($lastCell).'" style="font-weight:bold; text-align: left;">Таксировка</td>
            </tr>
            <tr>
                <td style="font-weight:bold; text-align: center;">погруз.</td>
                <td style="font-weight:bold; text-align: center;">разгруз.</td>
            </tr>
            <tr>
                <td height="5px"  style="font-weight:bold; text-align: center;"></td>
                <td height="5px"  style="font-weight:bold; text-align: center;">37</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">38</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">39</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">40</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">41</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">42</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">43</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">44</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">45</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">46</td>
                <td height="5px"  style="font-weight:bold; text-align: center;">47</td>

                <td colspan="'.($lastCell).'" style="font-weight:bold; text-align: center;"></td>

            </tr>
            <tr>
                <td style="font-weight:bold; text-align: left;" colspan="12">Выполнено</td>

                <td colspan="'.($lastCell).'" style="font-weight:bold; text-align: center;"></td>
            </tr>
            <tr>
                <td style="font-weight:bold; text-align: left;" colspan="12">Расценка</td>

                <td colspan="'.($lastCell).'"  style="font-weight:bold; text-align: center;"></td>

            </tr>
            <tr>
                <td style="font-weight:bold; text-align: left;" colspan="12">К оплате</td>

                <td colspan="'.($lastCell).'" style="font-weight:bold; text-align: center;"></td>
            </tr>
        </table>
';
$pdf->writeHTMLCell(0,0,0,$y+2,$productInOrder,0,1);
$y = $pdf->GetY();
$pdf->writeHTMLCell(0,0,0,$y+2,$managersNamesTo,0,1);
//$pdf->writeHTML($managersNamesTo, true, false, true, false, '');


//$html.='<table width="100%" style="padding:2px">
//            <tr>
//                <td width="15%">Сдал</td>
//                <td width="35%" style="border-bottom: 0.2px solid black">&nbsp;</td>
//                <td width="15%">Принял</td>
//                <td width="35%" style="border-bottom: 0.2px solid black">&nbsp;</td>
//            </tr>
//        </table>';

//$pdf->writeHTML($html, true, false, true, false, '');
//$pdf->writeHTML($managersNamesTo, true, false, true, false, '');
//$pdf->setJPEGQuality(100);
//$eacImgPath = Yii::getAlias("@web/image/pdf/");
//$pdf->Image($eacImgPath . 'logo-nomadex.jpg', 0, 182, 0, 0, 'jpg', 'http://nomadex.com', 'N', false, 300, 'R', false, false, 0, false, false, false);