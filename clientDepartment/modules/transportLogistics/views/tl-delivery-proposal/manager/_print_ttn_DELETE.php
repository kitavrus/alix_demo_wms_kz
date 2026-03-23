<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 06.10.14
 * Time: 14:58
 */

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\transportLogistics\models\TlDeliveryProposalRouteCars */
/* @var $modelDpRouteCar common\modules\transportLogistics\models\TlDeliveryProposalRouteTransport */
/* @var $routItem common\modules\transportLogistics\models\TlDeliveryRoutes */

////Yii::$app->get('tcpdf');;

//$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//$pdf->SetFont('dejavusans', '', 8);
//$pdf->SetMargins(10, 5, 10);
//$pdf->setPrintHeader(false);
//$pdf->setPrintFooter(false);

$pdf->AddPage();




$ttnNumber = $model->id;// ТОВАРНО-ТРАНСПОРТНАЯ НАКЛАДНАЯ №
$from = Yii::$app->formatter->asDatetime($model->shipped_datetime,'php:d.m.Y H:i:s'); // От
$car = $model->car->name.' '.$model->driver_auto_number; // Автомобиль
$automobileCompany = 'TOO NOMADEX'; // Автопредприятие
$driverName = $model->driver_name; // Водитель
$typeTransportation = 'АВТО';// Вид перевозки

$firstCopyShipper = 'TOO NOMADEX'; // 1-й экз. - грузоотправителю
$twoCopyConsignee = 'MEGA 3 [ ТЦ MEGA 3 ] '; // 2-й экз. - грузополучателю (название магазина)
$shipper = 'TOO NOMADEX'; // Грузоотправитель
$consignee = 'MEGA 3 [ ТЦ MEGA 3 ] '; // Грузополучатель (название магазина)

// Заказчик (плательщик)
$clientPayer = '';
if($rClient =  $routItem->client) {
    $clientPayer = $rClient->legal_company_name;
}
//$clientPayer = $routItem->client->legal_company_name;

// Пунк погрузки
$loadingPoint = $routItem->routeFrom->city->name . ' / ' . $routItem->routeFrom->name;
$loadingPoint .= ((!empty($routItem->routeFrom->shopping_center_name) && $routItem->routeFrom->shopping_center_name != '-')  ? '  [ ТЦ ' . $routItem->routeFrom->shopping_center_name . ' ] ' : '');
// Пункт разгрузки
$unloadingPoint = $routItem->routeTo->city->name . ' / ' . $routItem->routeTo->name;

// 2-й экз. - грузополучателю (название магазина)
$twoCopyConsignee = $unloadingPoint;
// Грузополучатель (название магазина)
$consignee = $unloadingPoint;


$unloadingPoint .= ((!empty($routItem->routeTo->shopping_center_name) && $routItem->routeTo->shopping_center_name != '-')  ? '  [ ТЦ ' . $routItem->routeTo->shopping_center_name . ' ] ' : '');
//$loadingPoint = $routItem->route_from; // Пунк погрузки
//$unloadingPoint = 'route_to'; // Пункт разгрузки



$numberPlaces = (!empty($modelDpRouteCar->number_places) ? $modelDpRouteCar->number_places : '0');// . ' ' . Yii::t('titles', 'Кол-во мест');// . "<br />";
$mcActual = ($modelDpRouteCar->mc_actual > 0 ? $modelDpRouteCar->mc_actual : '');// . ' ' . Yii::t('titles', 'М3');// . "<br />";
$kgActual = ($modelDpRouteCar->kg_actual > 0 ? $modelDpRouteCar->kg_actual.'' : '');// . ' ' . Yii::t('titles', 'Кг') . "<br />";

//$passed = 'Уалиев А.Н'; // Сдал (слева 1)
$passed = ''; // Сдал (слева 1)


$dataArray = array(
    array(1,2,3,4,5,6,7,8,9,10,11,12,13),
    array('-','-','-','шт','-','-','-','-','Коробка',$numberPlaces,$kgActual,$numberPlaces,$kgActual),
//    array(1,2,3,4,5,6,7,8,9,10,11,12,13),
//    array(1,2,3,4,5,6,7,8,9,10,11,12,13),
//    array(1,2,3,4,5,6,7,8,9,10,11,12,13),
//    array(1,2,3,4,5,6,7,8,9,10,11,12,13),
);



$html ='<table width="100%" style="padding:2px" >
            <tr>
                <td width="20%">1-й экз. - грузоотправителю</td>
                <td width="5%"  >&nbsp;</td>
                <td width="20%" style="border-bottom: 0.2px solid black">'.$firstCopyShipper.'</td>
                <td width="30%">Типовая междуведомственная форма №1-т</td>
                <td width="5%">&nbsp;</td>
                <td width="20%" style="border-bottom: 0.2px solid black">&nbsp;</td>
            </tr>
            <tr>
                <td width="20%">2-й экз. - грузополучателю</td>
                <td width="5%"  >Коды</td>
                <td width="20%" style="border-bottom: 0.2px solid black">'.$twoCopyConsignee.'</td>
                <td width="30%">ТОВАРНО-ТРАНСПОРТНАЯ НАКЛАДНАЯ №</td>
                <td width="5%">&nbsp;</td>
                <td width="20%" style="border-bottom: 0.2px solid black">'.$ttnNumber.'</td>
            </tr>
            <tr>
                <td width="20%">3-й экз. - перевозчику</td>
                <td width="5%" >&nbsp;</td>
                <td width="20%" style="border-bottom: 0.2px solid black">&nbsp;</td>
                <td width="30%"></td>
                <td width="5%">От</td>
                <td width="20%" style="border-bottom: 0.2px solid black">'.$from.'</td>
            </tr>
        </table>';

$html.='<table width="100%" style="padding:2px" >
            <tr>
                <td width="60%">&nbsp;</td>
                <td width="10%">Автомобиль</td>
                <td width="30%" style="border-bottom: 0.2px solid black">'.$car.'</td>
            </tr>
        </table>';

$html.='<table width="100%" style="padding:2px" >
            <tr>
                <td width="15%">Автопредприятие</td>
                <td width="20%" style="border-bottom: 0.2px solid black">'.$automobileCompany.'</td>
                <td width="10%">Водитель.</td>
                <td width="25%" style="border-bottom: 0.2px solid black">'.$driverName.'</td>
                <td width="15%">Вид перевозки</td>
                <td width="15%" style="border-bottom: 0.2px solid black">'.$typeTransportation.'</td>
            </tr>
        </table>';

$html.='<table width="100%" style="padding:2px" >
            <tr>
                <td width="15%">Заказчик (плательщик)</td>
                <td width="35%" style="border-bottom: 0.2px solid black">'.$clientPayer.'</td>
                <td width="10%">Водитель 2</td>
                <td width="40%" style="border-bottom: 0.2px solid black">&nbsp;</td>

            </tr>
        </table>';

$html.='<table width="100%" style="padding:2px" >
            <tr>
                <td width="15%">Грузоотправитель </td>
                <td width="35%" style="border-bottom: 0.2px solid black">'.$shipper.'</td>
                <td width="10%">Экспедитор</td>
                <td width="40%" style="border-bottom: 0.2px solid black">&nbsp;</td>

            </tr>
        </table>';

$html.='<table width="100%" style="padding:2px" >
            <tr>
                <td width="15%">Грузополучатель</td>
                <td width="85%" style="border-bottom: 0.2px solid black">'.$consignee.'</td>
            </tr>
        </table>';

$html.='<table width="100%" style="padding:2px" >
            <tr>
                <td width="15%">Пункт погрузки:</td>
                <td width="20%" style="border-bottom: 0.2px solid black">'.$loadingPoint.'</td>
                <td width="10%">Пункт разгрузки: </td>
                <td width="25%" style="border-bottom: 0.2px solid black">'.$unloadingPoint.'</td>
                <td width="10%">Маршрут №</td>
                <td width="20%" style="border-bottom: 0.2px solid black">&nbsp;</td>
            </tr>
        </table>';

$html.='<table width="100%" style="padding:2px" >
            <tr>
                <td width="15%">Переадресовка</td>
                <td width="35%" style="border-bottom: 0.2px solid black">&nbsp;</td>
                <td width="10%">Прицеп</td>
                <td width="40%" style="border-bottom: 0.2px solid black">&nbsp;</td>
            </tr>
        </table>';

$html.='<table width="100%" style="padding:2px" >
            <tr>
                <td width="15%">Переадресовка</td>
                <td width="35%" style="border-bottom: 0.2px solid black">&nbsp;</td>
                <td width="10%">Прицеп</td>
                <td width="40%" style="border-bottom: 0.2px solid black">&nbsp;</td>
            </tr>
        </table>';

$pdf->writeHTML($html, true, false, true, false, '');

$pdf->ln(1);

$html ='<table width="100%" style="padding:0px" >
            <tr>
                <td width="100%" align="center">СВЕДЕНИЯ О ГРУЗЕ</td>
            </tr>
        </table>';
$pdf->writeHTML($html, true, false, true, false, '');

$html_in = '';
foreach($dataArray as $row){
    $html_in.='<tr>
                 <td width="10%">' . $row[0] . '</td>
                 <td width="7%">' . $row[1] . '</td>
                 <td width="10%">' . $row[2] . '</td>
                 <td width="7%">' . $row[3] . '</td>
                 <td width="7%">' . $row[4] . '</td>
                 <td width="7%">' . $row[5] . '</td>
                 <td width="7%">' . $row[6] . '</td>
                 <td width="10%">' . $row[7] . '</td>
                 <td width="7%">' . $row[8] . '</td>
                 <td width="7%">' . $row[9] . '</td>
                 <td width="7%">' . $row[10] . '</td>
                 <td width="7%">' . $row[11] . '</td>
                 <td width="7%">' . $row[12] . '</td>
             </tr>';
}

$html = '<table width="100%" style="padding:2px;" border="1">
             <tr>
                 <td width="10%">Отпустил</td>
                 <td width="7%">№ Прейск., позиция</td>
                 <td width="10%">Наименование продукции, товара(груза) или номера контейнеров</td>
                 <td width="7%">Ед. Из</td>
                 <td width="7%">Кол.</td>
                 <td width="7%">Цена</td>
                 <td width="7%">Сумма</td>
                 <td width="10%">С грузом следуют документы</td>
                 <td width="7%">Вид упаков</td>
                 <td width="7%">Кол мест</td>
                 <td width="7%">Масса брутто, г.</td>
                 <td width="7%">Кол мест факт</td>
                 <td width="7%">Масса брутто факт, г.</td>
             </tr>
             ' . $html_in . '
             <tr>
                 <td width="10%">&nbsp;</td>
                 <td width="7%">&nbsp;</td>
                 <td width="10%">ИТОГО</td>
                 <td width="7%">&nbsp;</td>
                 <td width="7%">&nbsp;</td>
                 <td width="7%">&nbsp;</td>
                 <td width="7%">&nbsp;</td>
                 <td width="10%">&nbsp;</td>
                 <td width="7%">&nbsp;</td>
                 <td width="7%">&nbsp;</td>
                 <td width="7%">&nbsp;</td>
                 <td width="7%">&nbsp;</td>
                 <td width="7%">&nbsp;</td>
             </tr>
         </table>';
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->ln(1);
$html ='<table width="100%" style="padding:2px">
            <tr>
                <td width="25%">Всего отпущено на сумму</td>
                <td width="25%" style="border-bottom: 0.2px solid black">&nbsp;</td>
                <td width="20%">Отпуск разрешил</td>
                <td width="30%" style="border-bottom: 0.2px solid black">&nbsp;</td>
            </tr>
        </table>';
$html.='<table width="100%" style="padding:2px">
            <tr>
                <td width="15%">Указанный груз за испр.</td>
                <td width="15%" style="border-bottom: 0.2px solid black">&nbsp;</td>
                <td width="5%">Кол</td>
                <td width="15%" style="border-bottom: 0.2px solid black">&nbsp;</td>
                <td width="20%">Указанный груз за испр.</td>
                <td width="30%" style="border-bottom: 0.2px solid black">&nbsp;</td>

            </tr>
        </table>';
$html.='<table width="100%" style="padding:2px">
            <tr>
                <td width="30%">Пломбой тарой и упаковкой в хорошем состоянии</td>
                <td width="10%" style="border-bottom: 0.2px solid black">&nbsp;</td>
                <td width="5%">Мест</td>
                <td width="5%" style="border-bottom: 0.2px solid black">'.$numberPlaces.'</td>
                <td width="20%">Пломбой тарой и  упаковкой </td>
                <td width="30%" style="border-bottom: 0.2px solid black">&nbsp;</td>

            </tr>
        </table>';
$html.='<table width="100%" style="padding:2px">
            <tr>
                <td width="15%">Массой брутто</td>
                <td width="35%" style="border-bottom: 0.2px solid black">&nbsp;</td>
                <td width="15%">Массой брутто</td>
                <td width="35%" style="border-bottom: 0.2px solid black">&nbsp;</td>
            </tr>
        </table>';
$html.='<table width="100%" style="padding:2px">
            <tr>
                <td width="15%">Сдал</td>
                <td width="35%" style="border-bottom: 0.2px solid black">'.$passed.'</td>
                <td width="15%">Принял</td>
                <td width="35%" style="border-bottom: 0.2px solid black">&nbsp;</td>
            </tr>
        </table>';
$html.='<table width="100%" style="padding:2px">
            <tr>
                <td width="15%">Принял</td>
                <td width="35%" style="border-bottom: 0.2px solid black">&nbsp;</td>
                <td width="15%">Сдал</td>
                <td width="35%" style="border-bottom: 0.2px solid black">&nbsp;</td>
            </tr>
        </table>';
$html.='<table width="100%" style="padding:2px">
            <tr>
                <td width="15%">Сдал</td>
                <td width="35%" style="border-bottom: 0.2px solid black">&nbsp;</td>
                <td width="15%">Принял</td>
                <td width="35%" style="border-bottom: 0.2px solid black">&nbsp;</td>
            </tr>
        </table>';
$pdf->writeHTML($html, true, false, true, false, '');
//$pdf->ln(1);

$pdf->setJPEGQuality(100);
$eacImgPath = Yii::getAlias("@web/image/pdf/");
$pdf->Image($eacImgPath . 'logo-nomadex.jpg', 0, 182, 0, 0, 'jpg', 'http://nomadex.com', 'N', false, 300, 'R', false, false, 0, false, false, false);

//$pdf->lastPage();

//$pdf->Output(time().'-ttn.pdf', 'D');
//Yii::$app->end();
