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
use yii\helpers\Html;

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
    $html = '';
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

            $clientPayer = $dp->client->legal_company_name;

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

               $html .= Html::beginTag('div', ['class' => 'a4-l ttn']);

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
               if(!empty($dp->seal)){
                   $html.='<table width="100%" style="padding:2px" >
            <tr>
                <td width="60%">&nbsp;</td>
                <td width="10%" class="row-title">Пломба</td>
                <td width="30%" style="border-bottom: 0.2px solid black">'.$dp->seal.'</td>
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
                <td width="100%" align="center"><b>СВЕДЕНИЯ О ГРУЗЕ</b> [  '.Yii::t('transportLogistics/title','Номера заказов : ').$dp->getExtraFieldValueByName('orders').' ]</td>
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
           }
        }
    }

    DeliveryProposalManager::recalculateCarProposals($routeCar->id);

    echo $html;
}

