<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 20.10.14
 * Time: 19:17
 */
use common\modules\transportLogistics\models\TlDeliveryProposalRouteTransport;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\client\models\ClientEmployees;
use common\helpers\DateHelper;
use common\events\DpEvent;
use common\components\DeliveryProposalManager;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposalRouteCars */
//use yii;

////Yii::$app->get('tcpdf');;;

$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


$pdf->SetFont('arial', 'b', 8);
$pdf->SetMargins(10, 5, 10);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

if($items = $model->registryItems){

    $data = [
        'agent_id' => $model->agent_id,
        'car_id' => $model->car_id,
        'driver_name' => $model->driver_name,
        'driver_phone' => $model->driver_phone,
        'driver_auto_number' => $model->driver_auto_number,
        'price_invoice' => $model->price_invoice,
        'price_invoice_with_vat' => $model->price_invoice_with_vat,
    ];
    //создаем авто на основе данных из реестра
    $routeCar = DeliveryProposalManager::createRouteCar($data);

    foreach ($items as $i){
        if($dp = $i->proposal){
            $dpManager = new DeliveryProposalManager(['id'=>$dp->id]);
            $userName = '';
            $storeFrom = $dp->routeFrom;
            $managersNamesTo = 'Контакты получателей:<br />';
            if($routeTo = $dp->routeTo) {
                $clientEmployees = ClientEmployees::find()
                    ->where([
                        'deleted'=>0,
                        'client_id'=>$dp->client_id,
                        'store_id'=>$routeTo->id,
                        'manager_type'=>[
                            ClientEmployees::TYPE_BASE_ACCOUNT,
                            ClientEmployees::TYPE_DIRECTOR,
                            ClientEmployees::TYPE_DIRECTOR_INTERN,
                        ]
                    ])
                    ->all();

                foreach($clientEmployees as $item) {
                    $managersNamesTo .= $item->first_name.' '.$item->last_name.' / '.$item->phone_mobile.' '.$item->phone."<br />";
                }
            }

            //берем города Откуда и куда для авто из первой DP
            if(!$routeCar->route_city_from && !$routeCar->route_city_to){
                $routeCar->route_city_from = $dp->routeFrom->city_id;
                $routeCar->route_city_to = $dp->routeTo->city_id;
                $routeCar->save(false);
            }

            //добавляем ранее созданный автомобиль к первому маршруту в DP
            $dpManager->addCarToFirstRoute($routeCar->id);
            $dpManager->onChangeRouteCar();

            if(in_array($storeFrom->id,[4])) {
                $dp->shipped_datetime = DateHelper::getTimestamp();
                $dp->status = TlDeliveryProposal::STATUS_ON_ROUTE;
                $dp->save(false);

            }

            $event = new DpEvent();
            $event->deliveryProposalId = $dp->id;
            $dp->trigger(TlDeliveryProposal::EVENT_PRINT_TTN, $event);

            $ttnNumber = $dp->id;
            $from = Yii::$app->formatter->asDatetime($dp->shipped_datetime,'php:d.m.Y H:i:s');
            $car = '';
            if($carModel = $dp->car) {
                $car = $carModel->name.' '.$dp->driver_auto_number;
            }

            $automobileCompany = $dp->getCompanyTransporterValue();
            $driverName = $dp->driver_name;
            $typeTransportation = 'АВТО';

            $firstCopyShipper = '';


            $twoCopyConsignee = '';
            $shipper = $dp->getCompanyTransporterValue();
            $consignee = '';

//            $clientPayer = $dp->client->legal_company_name;
            $clientPayer = '';
            if($rClient =  $dp->client) {
                $clientPayer = $rClient->legal_company_name;
            }

            $loadingPoint = $dp->routeFrom->getPointTitleByPattern('{city_name} / {shop_code}, {shopping_center_name} {street} {house}');

            $unloadingPoint = $dp->routeTo->getPointTitleByPattern('{city_name} / {shop_code}, {shopping_center_name} {street} {house}');

            $twoCopyConsignee = $unloadingPoint;
            $consignee = $unloadingPoint;

            $numberPlaces = (!empty($dp->number_places) ? $dp->number_places : '0');
            $mcActual = ($dp->mc_actual > 0 ? $dp->mc_actual : '0');
            $kgActual = ($dp->kg_actual > 0 ? $dp->kg_actual.'' : '0');

            $numberPlaces = Yii::$app->formatter->asDecimal($numberPlaces);
            $mcActual = Yii::$app->formatter->asDecimal($mcActual,2);
            $kgActual = Yii::$app->formatter->asDecimal($kgActual,2);

            $passed = 'Уалиев А.Н';

            $dataArray = array(
                array(1,2,3,4,5,6,7,8,9,10,11,12,13),
                array('-','-','-','шт','-','-','-','-','Коробка',$numberPlaces,$kgActual,$numberPlaces,$mcActual),
            );

           for($i=0; $i<Yii::$app->params['TttCopiesNumber']; $i++){
               $pdf->AddPage();
               $html = '';
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
               if(!empty($dp->seal)){
                   $html.='<table width="100%" style="padding:2px" >
            <tr>
                <td width="60%">&nbsp;</td>
                <td width="10%">Пломба</td>
                <td width="30%" style="border-bottom: 0.2px solid black">'.$dp->seal.'</td>
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

               $pdf->ln(1);
//
               $html ='<table width="100%" style="padding:0px" >
            <tr>
                <td width="100%" align="center">СВЕДЕНИЯ О ГРУЗЕ [  '.Yii::t('transportLogistics/titles','Номера заказов : ').$dp->getExtraFieldValueByName('orders').' ]</td>
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
                 <td width="7%">Масса брутто, кг.</td>
                 <td width="7%">Кол мест факт</td>
                 <td width="7%">М3 факт</td>
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
               $pdf->ln(1);
               $pdf->writeHTML($managersNamesTo, true, false, true, false, '');

               $pdf->setJPEGQuality(100);
               $eacImgPath = Yii::getAlias("@web/image/pdf/");
               $pdf->Image($eacImgPath . 'logo-nomadex.jpg', 0, 182, 0, 0, 'jpg', 'http://nomadex.com', 'N', false, 300, 'R', false, false, 0, false, false, false);
           }
        }
    }

    DeliveryProposalManager::recalculateCarProposals($routeCar->id);
}

$pdf->lastPage();

$pdf->Output(time() . 'registry-ttn.pdf', 'D');
die;