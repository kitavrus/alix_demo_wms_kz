<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 06.10.14
 * Time: 14:58
 */

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposal */

////Yii::$app->get('tcpdf');;


$pdf->AddPage();

$ttnNumber = $model->id;// ТОВАРНО-ТРАНСПОРТНАЯ НАКЛАДНАЯ №
$from = Yii::$app->formatter->asDatetime($model->shipped_datetime,'php:d.m.Y H:i:s'); // От
$car = '';
if($carModel = $model->car) {
    $car = $carModel->name.' '.$model->driver_auto_number; // Автомобиль
}

//$automobileCompany = 'TOO NOMADEX'; // Автопредприятие
$automobileCompany = $model->getCompanyTransporterValue(); // Автопредприятие
$driverName = $model->driver_name; // Водитель
$typeTransportation = 'АВТО';// Вид перевозки

//$firstCopyShipper = 'TOO NOMADEX'; // 1-й экз. - грузоотправителю
$firstCopyShipper = '';//$model->getCompanyTransporterValue(); // 1-й экз. - грузоотправителю

//\yii\helpers\VarDumper::dump($firstCopyShipper,10,true);
//die;

$twoCopyConsignee = ''; // 2-й экз. - грузополучателю (название магазина)
//$shipper = 'TOO NOMADEX'; // Грузоотправитель
$shipper = $model->getCompanyTransporterValue(); // Грузоотправитель
$consignee = ''; // Грузополучатель (название магазина)

// Заказчик (плательщик)
//$clientPayer = $model->client->legal_company_name;

$clientPayer = '';
if($rClient =  $model->client) {
    $clientPayer = $rClient->legal_company_name;
}

// Пунк погрузки
$loadingPoint = $model->routeFrom->getPointTitleByPattern('{city_name} / {shop_code}, {shopping_center_name} {street} {house}');

// Пункт разгрузки
$unloadingPoint = $model->routeTo->getPointTitleByPattern('{city_name} / {shop_code}, {shopping_center_name} {street} {house}');

// 2-й экз. - грузополучателю (название магазина)
$twoCopyConsignee = $unloadingPoint;
// Грузополучатель (название магазина)
$consignee = $unloadingPoint;


//$unloadingPoint .= ((!empty($model->routeTo->shopping_center_name) && $model->routeTo->shopping_center_name != '-')  ? '  [ ТЦ ' . $model->routeTo->shopping_center_name . ' ] ' : '');
//$loadingPoint = $routItem->route_from; // Пунк погрузки
//$unloadingPoint = 'route_to'; // Пункт разгрузки


$numberPlaces = (!empty($model->number_places) ? $model->number_places : '0');// . ' ' . Yii::t('titles', 'Кол-во мест');// . "<br />";
$mcActual = ($model->mc_actual > 0 ? $model->mc_actual : '0');// . ' ' . Yii::t('titles', 'М3');// . "<br />";
$kgActual = ($model->kg_actual > 0 ? $model->kg_actual.'' : '0');// . ' ' . Yii::t('titles', 'Кг') . "<br />";

$numberPlaces = Yii::$app->formatter->asDecimal($numberPlaces);
$mcActual = Yii::$app->formatter->asDecimal($mcActual,2);
$kgActual = Yii::$app->formatter->asDecimal($kgActual,2);



//$passed = 'Уалиев А.Н'; // Сдал (слева 1)
// TODO Брать автоматически из залогиненого пользователя
$passed = 'Уалиев А.Н'; // Сдал (слева 1)

// Reset Begin
//$firstCopyShipper = '-';
//$twoCopyConsignee = '-';
//$numberPlaces = '-';
//$kgActual = '-';
//$mcActual = '-';
//$ttnNumber = '-';
//$from = '-';
//$car = '-';
//$automobileCompany = '-';
//$driverName = '-';
//$typeTransportation = '-';
//$clientPayer = '-';
//$shipper = '-';
//$consignee = '-';
//$loadingPoint = '-';
//$unloadingPoint = '-';
//$passed = '-';
// Reset end



$style = array(
    'border'=>false,
    'padding'=>0,
    'hpadding'=>0,
    'vpadding'=>0.5,
    'fgcolor'=>array(0, 0, 0),
    'bgcolor'=>false,
    'text'=>false,//Текст снизу
    'font'=>'dejavusans',
    'fontsize'=>15,//Размер шрифта
    'stretchtext'=>4,//Растягивание
    'stretch'=>false,
    'fitwidth'=>false,
    'cellfitalign'=>'',
);

$pdf->write1DBarcode($model->getSecureReviewCodePrefix(), 'C128',205,0, 80, 9, 1.5, $style, 'C');
//$pdf->SetXY(0,0);

$dataArray = array(
    array(1,2,3,4,5,6,7,8,9,10,11,12,13),
    array('-','-','ИТОГО','шт','-','-','-','-','Коробка',$numberPlaces,$kgActual,$numberPlaces,$mcActual),
);
$html = '';
//
//
//
$html ='<table width="100%" style="padding:2px" >
            <tr>
                <td width="20%" style="font-weight:normal;">1-й экз. - грузоотправителю</td>
                <td width="5%"  >&nbsp;</td>
                <td width="20%" style="border-bottom: 0.2px solid black">'.$firstCopyShipper.'</td>
                <td width="30%">'.Yii::t('transportLogistics/pdf','Model international form No. 1-T').'</td>
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
if(!empty($model->seal)){
    $html.='<table width="100%" style="padding:2px" >
            <tr>
                <td width="60%">&nbsp;</td>
                <td width="10%">Пломба</td>
                <td width="30%" style="border-bottom: 0.2px solid black">'.$model->seal.'</td>
            </tr>
        </table>';
}
$html.='<table width="100%" style="padding:2px" >
            <tr>
                <td width="60%">&nbsp;</td>
                <td width="10%">Автомобиль</td>
                <td width="30%" style="border-bottom: 0.2px solid black">'.$car.'</td>
            </tr>
        </table>';
//
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
//
$html.='<table width="100%" style="padding:2px" >
            <tr>
                <td width="15%">Заказчик (плательщик)</td>
                <td width="35%" style="border-bottom: 0.2px solid black">'.$clientPayer.'</td>
                <td width="10%">Водитель 2</td>
                <td width="40%" style="border-bottom: 0.2px solid black">&nbsp;</td>

            </tr>
        </table>';
//
$html.='<table width="100%" style="padding:2px" >
            <tr>
                <td width="15%">Грузоотправитель </td>
                <td width="35%" style="border-bottom: 0.2px solid black">'.$shipper.'</td>
                <td width="10%">Экспедитор</td>
                <td width="40%" style="border-bottom: 0.2px solid black">&nbsp;</td>

            </tr>
        </table>';
//
$html.='<table width="100%" style="padding:2px" >
            <tr>
                <td width="15%">Грузополучатель</td>
                <td width="85%" style="border-bottom: 0.2px solid black">'.$consignee.'</td>
            </tr>
        </table>';
//
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
//
$html.='<table width="100%" style="padding:2px" >
            <tr>
                <td width="15%">Переадресовка</td>
                <td width="35%" style="border-bottom: 0.2px solid black">&nbsp;</td>
                <td width="10%">Прицеп</td>
                <td width="40%" style="border-bottom: 0.2px solid black">&nbsp;</td>
            </tr>
        </table>';
//
$html.='<table width="100%" style="padding:2px" >
            <tr>
                <td width="15%">Переадресовка</td>
                <td width="35%" style="border-bottom: 0.2px solid black">&nbsp;</td>
                <td width="10%">Прицеп</td>
                <td width="40%" style="border-bottom: 0.2px solid black">&nbsp;</td>
            </tr>
        </table>';

$pdf->writeHTML($html, true, false, true, false, '');

//$pdf->ln(1);
//
$html ='<table width="100%" style="padding:0px" >
            <tr>
                <td width="100%" align="center">СВЕДЕНИЯ О ГРУЗЕ</td>
            </tr>
        </table>';
// [  '.Yii::t('transportLogistics/title','Номера заказов : ').$model->getExtraFieldValueByName('orders').' ]
$pdf->writeHTML($html, true, false, true, false, '');

$dataProviderProposalOrders = $model->getProposalOrders()->all();

$html_in = '';



foreach($dataArray as $dataArrayKey => $row) {
    if($dataArrayKey==1) {
        if (!empty($dataProviderProposalOrders)) {
            foreach ($dataProviderProposalOrders as $orderValue) {
                $mc = !empty($orderValue->mc_actual) ? $orderValue->mc_actual : $orderValue->mc;
                $kg = !empty($orderValue->kg_actual) ? $orderValue->kg_actual : $orderValue->kg;

                $html_in .= '<tr>
                 <td width="7%">' . '-' . '</td>
                 <td width="7%">' . '-' . '</td>
                 <td width="16%">' . $orderValue->order_number . '</td>
                 <td width="7%">' . '-' . '</td>
                 <td width="7%">' . '-' . '</td>
                 <td width="7%">' . '-' . '</td>
                 <td width="7%">' . '-' . '</td>
                 <td width="7%">' . '-' . '</td>
                 <td width="7%">' . '-' . '</td>
                 <td width="7%">' . Yii::$app->formatter->asDecimal($orderValue->number_places) . '</td>
                 <td width="7%">' . Yii::$app->formatter->asDecimal($kg,2) . '</td>
                 <td width="7%">' . Yii::$app->formatter->asDecimal($orderValue->number_places) . '</td>
                 <td width="7%">' . Yii::$app->formatter->asDecimal($mc,2) . '</td>
             </tr>';
            }
        }
    }

    $html_in.='<tr>
                 <td width="7%">' . $row[0] . '</td>
                 <td width="7%">' . $row[1] . '</td>
                 <td width="16%">' . $row[2] . '</td>
                 <td width="7%">' . $row[3] . '</td>
                 <td width="7%">' . $row[4] . '</td>
                 <td width="7%">' . $row[5] . '</td>
                 <td width="7%">' . $row[6] . '</td>
                 <td width="7%">' . $row[7] . '</td>
                 <td width="7%">' . $row[8] . '</td>
                 <td width="7%">' . $row[9] . '</td>
                 <td width="7%">' . $row[10] . '</td>
                 <td width="7%">' . $row[11] . '</td>
                 <td width="7%">' . $row[12] . '</td>
             </tr>';
}
// Reset Begin
//$html_in = '';
// Reset End
$html = '<table width="100%" style="padding:2px;" border="1">
             <tr>
                 <td width="7%">Отпустил</td>
                 <td width="7%">№ Прейск., позиция</td>
                 <td width="16%">Наименование продукции, товара(груза) или номера контейнеров</td>
                 <td width="7%">Ед. Из</td>
                 <td width="7%">Кол.</td>
                 <td width="7%">Цена</td>
                 <td width="7%">Сумма</td>
                 <td width="7%">С грузом следуют документы</td>
                 <td width="7%">Вид упаков</td>
                 <td width="7%">Кол мест</td>
                 <td width="7%">Масса брутто, кг.</td>
                 <td width="7%">Кол мест факт</td>
                 <td width="7%">М3 факт</td>
             </tr>
             ' . $html_in . '

         </table>';
$pdf->writeHTML($html, true, false, true, false, '');
/*
              <tr>
                 <td width="7%">&nbsp;</td>
                 <td width="7%">&nbsp;</td>
                 <td width="16%">ИТОГО</td>
                 <td width="7%">&nbsp;</td>
                 <td width="7%">&nbsp;</td>
                 <td width="7%">&nbsp;</td>
                 <td width="7%">&nbsp;</td>
                 <td width="7%">&nbsp;</td>
                 <td width="7%">&nbsp;</td>
                 <td width="7%">&nbsp;</td>
                 <td width="7%">&nbsp;</td>
                 <td width="7%">&nbsp;</td>
                 <td width="7%">&nbsp;</td>
             </tr>
 * */


//$pdf->ln(1);
$html = '';
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
//$html.='<table width="100%" style="padding:2px">
//            <tr>
//                <td width="30%">Пломбой тарой и упаковкой в xорошем состоянии</td>
//                <td width="10%" style="border-bottom: 0.2px solid black">&nbsp;</td>
//                <td width="5%">Мест</td>
//                <td width="5%" style="border-bottom: 0.2px solid black">'.$numberPlaces.'</td>
//                <td width="20%">Пломбой тарой и  упаковкой </td>
//                <td width="30%" style="border-bottom: 0.2px solid black">&nbsp;</td>
//            </tr>
//        </table>';

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
$pdf->writeHTML($managersNamesTo, true, false, true, false, '');


//$pdf->setJPEGQuality(100);
//$eacImgPath = Yii::getAlias("@web/image/pdf/");
//$pdf->Image($eacImgPath . 'logo-nomadex.jpg', 0, 182, 0, 0, 'jpg', 'http://nomadex.kz', 'N', false, 300, 'R', false, false, 0, false, false, false);