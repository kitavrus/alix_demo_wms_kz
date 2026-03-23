<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 06.10.14
 * Time: 14:58
 */
use yii\helpers\Html;

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
$clientPayer = $model->client->legal_company_name;

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


$dataArray = array(
    array(1,2,3,4,5,6,7,8,9,10,11,12,13),
    array('-','-','-','шт','-','-','-','-','Коробка',$numberPlaces,$kgActual,$numberPlaces,$mcActual),
);
$html = Html::beginTag('div', ['class' => 'a4-l ttn']);

$html .='<table width="100%" style="padding:2px" >
            <tr>
                <td width="20%" style="font-weight:normal;">1-й экз. - грузоотправителю</td>
                <td width="5%"  >&nbsp;</td>
                <td width="20%" style="border-bottom: 0.2px solid black">'.$firstCopyShipper.'</td>
                <td width="30%" class="row-title">'.Yii::t('transportLogistics/pdf','Model international form No. 1-T').'</td>
                <td width="5%">&nbsp;</td>
                <td width="20%" style="border-bottom: 0.2px solid black">&nbsp;</td>
            </tr>
            <tr>
                <td width="20%">2-й экз. - грузополучателю</td>
                <td width="5%" class="row-title">Коды</td>
                <td width="20%" style="border-bottom: 0.2px solid black">'.$twoCopyConsignee.'</td>
                <td width="35%" class="row-title">ТОВАРНО-ТРАНСПОРТНАЯ НАКЛАДНАЯ №</td>
                <td width="5%">&nbsp;</td>
                <td width="20%" style="border-bottom: 0.2px solid black">'.$ttnNumber.'</td>
            </tr>
            <tr>
                <td width="20%">3-й экз. - перевозчику</td>
                <td width="5%" >&nbsp;</td>
                <td width="20%" style="border-bottom: 0.2px solid black">&nbsp;</td>
                <td width="30%"></td>
                <td width="5%" class="row-title">От</td>
                <td width="20%" style="border-bottom: 0.2px solid black">'.$from.'</td>
            </tr>
        </table>';
if(!empty($model->seal)){
    $html.='<table width="100%" style="padding:2px" >
            <tr>
                <td width="60%">&nbsp;</td>
                <td width="10%" class="row-title">Пломба</td>
                <td width="30%" style="border-bottom: 0.2px solid black">'.$model->seal.'</td>
            </tr>
        </table>';
}
$html.='<table width="100%" style="padding:2px" >
            <tr>
                <td width="60%">&nbsp;</td>
                <td width="10%" class="row-title">Автомобиль</td>
                <td width="30%" style="border-bottom: 0.2px solid black">'.$car.'</td>
            </tr>
        </table>';
//
$html.='<table width="100%" style="padding:2px" >
            <tr>
                <td width="15%" class="row-title">Автопредприятие</td>
                <td width="20%" style="border-bottom: 0.2px solid black">'.$automobileCompany.'</td>
                <td width="10%" class="row-title">Водитель.</td>
                <td width="25%" style="border-bottom: 0.2px solid black">'.$driverName.'</td>
                <td width="15%" class="row-title">Вид перевозки</td>
                <td width="15%" style="border-bottom: 0.2px solid black">'.$typeTransportation.'</td>
            </tr>
        </table>';
//
$html.='<table width="100%" style="padding:2px" >
            <tr>
                <td width="15%" class="row-title">Заказчик (плательщик)</td>
                <td width="35%" style="border-bottom: 0.2px solid black">'.$clientPayer.'</td>
                <td width="10%" class="row-title">Водитель 2</td>
                <td width="40%" style="border-bottom: 0.2px solid black">&nbsp;</td>

            </tr>
        </table>';
//
$html.='<table width="100%" style="padding:2px" >
            <tr>
                <td width="15%" class="row-title">Грузоотправитель </td>
                <td width="35%" style="border-bottom: 0.2px solid black">'.$shipper.'</td>
                <td width="10%" class="row-title">Экспедитор</td>
                <td width="40%" style="border-bottom: 0.2px solid black">&nbsp;</td>

            </tr>
        </table>';
//
$html.='<table width="100%" style="padding:2px" >
            <tr>
                <td width="15%" class="row-title">Грузополучатель</td>
                <td width="85%" style="border-bottom: 0.2px solid black">'.$consignee.'</td>
            </tr>
        </table>';
//
$html.='<table width="100%" style="padding:2px" >
            <tr>
                <td width="15%" class="row-title">Пункт погрузки:</td>
                <td width="20%" style="border-bottom: 0.2px solid black">'.$loadingPoint.'</td>
                <td width="10%" class="row-title">Пункт разгрузки: </td>
                <td width="25%" style="border-bottom: 0.2px solid black">'.$unloadingPoint.'</td>
                <td width="10%" class="row-title">Маршрут №</td>
                <td width="20%" style="border-bottom: 0.2px solid black">&nbsp;</td>
            </tr>
        </table>';
//
$html.='<table width="100%" style="padding:2px" >
            <tr>
                <td width="15%" class="row-title">Переадресовка</td>
                <td width="35%" style="border-bottom: 0.2px solid black">&nbsp;</td>
                <td width="10%" class="row-title">Прицеп</td>
                <td width="40%" style="border-bottom: 0.2px solid black">&nbsp;</td>
            </tr>
        </table>';
//
$html.='<table width="100%" style="padding:2px" >
            <tr>
                <td width="15%" class="row-title">Переадресовка</td>
                <td width="35%" style="border-bottom: 0.2px solid black">&nbsp;</td>
                <td width="10%" class="row-title">Прицеп</td>
                <td width="40%" style="border-bottom: 0.2px solid black">&nbsp;</td>
            </tr>
        </table>';

$html .='<table width="100%" style="padding:0; margin-top: 20px;" >
            <tr>
                <td width="100%" align="center"><b>СВЕДЕНИЯ О ГРУЗЕ</b> [  '.Yii::t('transportLogistics/title','Номера заказов : ').$model->getExtraFieldValueByName('orders').' ]</td>
            </tr>
        </table>';

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

$html .= '<table width="100%" style="padding:2px; margin-top: 4mm; margin-bottom: 4mm;" border="1">
             <tr>
                 <td width="10%" class="row-title">Отпустил</td>
                 <td width="7%" class="row-title">№ Прейск., позиция</td>
                 <td width="10%" class="row-title">Наименование продукции, товара(груза) или номера контейнеров</td>
                 <td width="7%" class="row-title">Ед. Из</td>
                 <td width="7%" class="row-title">Кол.</td>
                 <td width="7%" class="row-title">Цена</td>
                 <td width="7%" class="row-title">Сумма</td>
                 <td width="10%" class="row-title">С грузом следуют документы</td>
                 <td width="7%" class="row-title">Вид упаков</td>
                 <td width="7%" class="row-title">Кол мест</td>
                 <td width="7%" class="row-title">Масса брутто, кг.</td>
                 <td width="7%" class="row-title">Кол мест факт</td>
                 <td width="7%" class="row-title">М3 факт</td>
             </tr>
             ' . $html_in . '
             <tr>
                 <td width="10%">&nbsp;</td>
                 <td width="7%">&nbsp;</td>
                 <td width="10%" class="row-title">ИТОГО</td>
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
$html .='<table width="100%" style="padding:2px margin-top: 4mm;">
            <tr>
                <td width="25%" class="row-title">Всего отпущено на сумму</td>
                <td width="25%" style="border-bottom: 0.2px solid black">&nbsp;</td>
                <td width="20%" class="row-title">Отпуск разрешил</td>
                <td width="30%" style="border-bottom: 0.2px solid black">&nbsp;</td>
            </tr>
        </table>';
$html.='<table width="100%" style="padding:2px">
            <tr>
                <td width="15%" class="row-title">Указанный груз за испр.</td>
                <td width="15%" style="border-bottom: 0.2px solid black">&nbsp;</td>
                <td width="5%" class="row-title">Кол</td>
                <td width="15%" style="border-bottom: 0.2px solid black">&nbsp;</td>
                <td width="20%" class="row-title">Указанный груз за испр.</td>
                <td width="30%" style="border-bottom: 0.2px solid black">&nbsp;</td>

            </tr>
        </table>';
$html.='<table width="100%" style="padding:2px">
            <tr>
                <td width="30%" class="row-title">Пломбой тарой и упаковкой в хорошем состоянии</td>
                <td width="10%" style="border-bottom: 0.2px solid black">&nbsp;</td>
                <td width="5%" class="row-title">Мест</td>
                <td width="5%" style="border-bottom: 0.2px solid black">'.$numberPlaces.'</td>
                <td width="20%" class="row-title">Пломбой тарой и  упаковкой </td>
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
                <td width="15%" class="row-title">Массой брутто</td>
                <td width="35%" style="border-bottom: 0.2px solid black">&nbsp;</td>
                <td width="15%" class="row-title">Массой брутто</td>
                <td width="35%" style="border-bottom: 0.2px solid black">&nbsp;</td>
            </tr>
        </table>';
$html.='<table width="100%" style="padding:2px">
            <tr>
                <td width="15%" class="row-title">Сдал</td>
                <td width="35%" style="border-bottom: 0.2px solid black">'.$passed.'</td>
                <td width="15%" class="row-title">Принял</td>
                <td width="35%" style="border-bottom: 0.2px solid black">&nbsp;</td>
            </tr>
        </table>';
$html.='<table width="100%" style="padding:2px">
            <tr>
                <td width="15%" class="row-title">Принял</td>
                <td width="35%" style="border-bottom: 0.2px solid black">&nbsp;</td>
                <td width="15%" class="row-title">Сдал</td>
                <td width="35%" style="border-bottom: 0.2px solid black">&nbsp;</td>
            </tr>
        </table>';
$html.='<table width="100%" style="padding:2px">
            <tr>
                <td width="15%" class="row-title">Сдал</td>
                <td width="35%" style="border-bottom: 0.2px solid black">&nbsp;</td>
                <td width="15%" class="row-title">Принял</td>
                <td width="35%" style="border-bottom: 0.2px solid black">&nbsp;</td>
            </tr>
        </table>';
$html.='<table width="100%" style="padding:2px">
            <tr>
                <td width="15%" class="row-title">'.$managersNamesTo.'</td>
            </tr>
        </table>';
$eacImgPath = Yii::getAlias("@web/image/pdf/");
$html.= Html::img($eacImgPath . 'logo-nomadex.jpg', ['class' => 'ttn-logo']);
$html .= Html::endTag('div');
echo $html;