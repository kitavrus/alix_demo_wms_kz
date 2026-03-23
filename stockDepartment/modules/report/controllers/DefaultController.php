<?php

namespace stockDepartment\modules\report\controllers;

use common\modules\billing\models\TlDeliveryProposalBilling;
use common\modules\city\models\City;
use common\modules\city\models\Country;
use stockDepartment\modules\report\models\TlDeliveryProposalFormSearch;
use stockDepartment\modules\report\models\TlDeliveryProposalSearch;
use stockDepartment\modules\report\models\TlDeliveryProposalSearchReportExport;
use common\modules\store\models\Store;
use stockDepartment\modules\report\service\reportToDay\MailService;
use stockDepartment\modules\report\service\reportToDay\Service as ReportToDay;
use Yii;
use stockDepartment\components\Controller;
use common\modules\client\models\Client;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use \DateTime;
use common\modules\transportLogistics\components\TLHelper;
use common\modules\store\repository\Repository as StoreRepository;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        $searchModel = new TlDeliveryProposalSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $clientArray = Client::getActiveItems();
        $storeArray = TLHelper::getStockPointArray();

        return $this->render('index',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'clientArray' => $clientArray,
            'storeArray' => $storeArray,
        ]);
    }

    /*
     *
     * Export data to EXEL
     *
     * */
    public function actionExportToExcel()
    {
        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->getProperties()
            ->setCreator("Report Reportov")
            ->setLastModifiedBy("Report Reportov")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report");

        $activeSheet = $objPHPExcel
            ->setActiveSheetIndex(0)
            ->setTitle('report-'.date('d.m.Y'));


        $i = 1;
        $activeSheet->setCellValue('A'.$i, 'Клиент'); // +
        $activeSheet->setCellValue('B'.$i, 'Из'); // +
        $activeSheet->setCellValue('C'.$i, 'В'); // +
        $activeSheet->setCellValue('D'.$i, 'Дата отгрузки'); // +
        $activeSheet->setCellValue('E'.$i, 'Дата получения'); // +
        $activeSheet->setCellValue('F'.$i, 'Кол-во мест'); // +
        $activeSheet->setCellValue('G'.$i, 'Кол-во кг'); // +
        $activeSheet->setCellValue('H'.$i, 'Кол-во М3'); // +
        $activeSheet->setCellValue('I'.$i, 'Перевозчик'); // +
        $activeSheet->setCellValue('J'.$i, 'Расходы (дополниетельные)'); // +
        $activeSheet->setCellValue('K'.$i, 'Расходы (кто платит)'); // +
        $activeSheet->setCellValue('L'.$i, 'Потратили (наши затраты)'); // +
        $activeSheet->setCellValue('M'.$i, 'Получили (от клиента)');
        $activeSheet->setCellValue('N'.$i, 'Заработали'); // +
        $activeSheet->setCellValue('O'.$i, 'ID'); // +

        $searchModel = new TlDeliveryProposalSearchReportExport();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dps = $dataProvider->getModels();

        $priceInvoiceWithVatTotal = 0;
		
		 $storeArray = TLHelper::getStockPointArray();

        foreach($dps as $model) {

            $i++;
            $modelRow = $i;
//            $clientTitle = $model->client->title;
            $clientTitle = '';
            if($rClient =  $model->client) {
                $clientTitle = $rClient->title;
            }
			$b = isset($storeArray[$model->route_from]) ? $storeArray[$model->route_from] : "-";
			$c = isset($storeArray[$model->route_to]) ? $storeArray[$model->route_to] : "-";
            $activeSheet->setCellValue('A' . $i, $clientTitle); // 'Клиент'
            $activeSheet->setCellValue('B' . $i, $b ); // Store::getPointTitle($model->route_from)); // Из
            $activeSheet->setCellValue('C' . $i, $c); // Store::getPointTitle($model->route_to)); // В

            $shippedDatetime = '';
            if(!empty($model->shipped_datetime)) {
                $shippedDatetime = Yii::$app->formatter->asDate($model->shipped_datetime,'php:d/m/Y');
            }

            $activeSheet->setCellValue('D' . $i, $shippedDatetime); // Дата отгрузки

            $deliveryDatetime = '';
            if($model->delivery_date) {
                $deliveryDatetime = Yii::$app->formatter->asDate($model->delivery_date,'php:d/m/Y');
            }

            $activeSheet->setCellValue('E' . $i, $deliveryDatetime); // 'Дата получения'

            $numberPlacesActual = $model->number_places_actual;
            $activeSheet->setCellValue('F' . $i, $numberPlacesActual); // 'Кол-во мест'

            $kgActual = $model->kg_actual;
            $activeSheet->setCellValue('G' . $i, $kgActual); // 'Кол-во кг'

            $mcActual = $model->mc_actual;
            $activeSheet->setCellValue('H' . $i, $mcActual); // 'Кол-во М3'

            $priceInvoiceWithVat = $model->price_invoice_with_vat;
            $activeSheet->setCellValue('M' . $i, $priceInvoiceWithVat); // 'Получили (От клиента)'

            $priceInvoiceWithVatTotal += $priceInvoiceWithVat;

            $activeSheet->setCellValue('O' . $i, $model->id); // 'ID'
//

            if($routes = $model->getProposalRoutes()->all()) {

                foreach($routes as $route) {

                    if($route->deleted == 1) {
                        continue;
                    }

                    $i++;
                    $routeRow = $i;
                    $routeSumWithOutUnforeseenExpenses = $route->price_invoice;

                    $activeSheet->setCellValue('A' . $i, $clientTitle); // 'Клиент'
                    $activeSheet->setCellValue('B' . $i, Store::getPointTitle($route->route_from)); // Из
                    $activeSheet->setCellValue('C' . $i, Store::getPointTitle($route->route_to)); // в

                    $shippedDatetime = '';
                    if(!empty($route->shipped_datetime)) {
                        $shippedDatetime = Yii::$app->formatter->asDate($route->shipped_datetime,'php:d/m/Y');
                    }

                    $activeSheet->setCellValue('D' . $i, $shippedDatetime); // // Дата отгрузки

                    $deliveryDatetime = '';
                    if($route->delivery_date) {
                        $deliveryDatetime = Yii::$app->formatter->asDate($route->delivery_date,'php:d/m/Y');
                    }

                    $activeSheet->setCellValue('E' . $i, $deliveryDatetime); // 'Дата получения'
                    $activeSheet->setCellValue('F' . $i, ''); // 'Кол-во мест'
                    $activeSheet->setCellValue('G' . $i, ''); // 'Кол-во кг'
                    $activeSheet->setCellValue('H' . $i, ''); // 'Кол-во М3'

                    $priceRoute = $route->price_invoice;
                    $activeSheet->setCellValue('L' . $i, $priceRoute); // 'Потратили (Наши затраты)'

                    $activeSheet->setCellValue('O' . $i, $route->id); // 'ID'

                    if($carItems = $route->getCarItems()->all()) {
                        foreach ($carItems as $item) {
                            if($item->deleted == 1 ) {
                                continue;
                            }

                            $i++;
                            $activeSheet->setCellValue('A' . $i, $clientTitle); // 'Клиент'
                            $activeSheet->setCellValue('I' . $i, ArrayHelper::getValue($item->agent,'name')); //'Перевозчик'
                        }
                    }

                    if ($unforeseenExpenses = $route->getTlDeliveryProposalRouteUnforeseenExpenses()->all()) {
                        foreach ($unforeseenExpenses as $ue) {
                            if($ue->deleted == 1 ) {
                                continue;
                            }

                            $i++;
//                            $activeSheet->setCellValue('A' . $i, $clientTitle); // 'Клиент'
                            $activeSheet->setCellValue('J' . $i, $ue->name); // 'Расходы Название'
                            $priceUe = $ue->price_cache;
                            $activeSheet->setCellValue('L' . $i, $priceUe); // 'Потратили (Наши затраты)'
                            $activeSheet->setCellValue('K' . $i, $ue->getWhoPayValue()); // 'Расходы (кто платит)'

                            $routeSumWithOutUnforeseenExpenses -= $priceUe;
                        }
                    }

                    $activeSheet->setCellValue('L' . $routeRow, $routeSumWithOutUnforeseenExpenses);
                }

                $i++;
                $activeSheet->setCellValue('L' . $modelRow, $model->price_expenses_total);// 'Потратили (Наши затраты)'
                $activeSheet->setCellValue('M' . $modelRow, $model->price_invoice_with_vat);
                $activeSheet->setCellValue('N' . $modelRow, $model->price_our_profit); // 'Заработали'

            }
            $i++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="full-report-' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

    /*
     *
     * Export data to EXEL
     *
     * */
    public function actionExportToExcelKpiDelivered()
    {
        // export-to-excel-kpi-delivered

        $filter = Yii::$app->request->get('filter');

        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->getProperties()
            ->setCreator("Report Reportov")
            ->setLastModifiedBy("Report Reportov")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report");

        $activeSheet = $objPHPExcel
            ->setActiveSheetIndex(0)
            ->setTitle('report-'.date('d.m.Y'));


        $i = 1;
        $activeSheet->setCellValue('A'.$i, 'Клиент'); // +
        $activeSheet->setCellValue('B'.$i, 'Из'); // +
        $activeSheet->setCellValue('C'.$i, 'В'); // +
        $activeSheet->setCellValue('D'.$i, 'Дата отгрузки'); // +
        $activeSheet->setCellValue('E'.$i, 'Дата получения'); // +
        $activeSheet->setCellValue('F'.$i, 'В пути'); // +
        $activeSheet->setCellValue('G'.$i, 'Разница'); // +
        $activeSheet->setCellValue('H'.$i, 'По договору'); // +
        $activeSheet->setCellValue('I'.$i, 'ID Заявка на доставку'); // +
        $activeSheet->setCellValue('J'.$i, 'ID Тариф'); // +
        $activeSheet->setCellValue('K'.$i, 'Тип доставки'); // +
        $activeSheet->setCellValue('L'.$i, 'Из города');
        $activeSheet->setCellValue('M'.$i, 'В город');
        $activeSheet->setCellValue('N'.$i, 'Из страны');
        $activeSheet->setCellValue('O'.$i, 'В страну');

//        $activeSheet->setCellValue('F'.$i, 'Кол-во мест'); // +
//        $activeSheet->setCellValue('G'.$i, 'Кол-во кг'); // +
//        $activeSheet->setCellValue('H'.$i, 'Кол-во М3'); // +
//        $activeSheet->setCellValue('I'.$i, 'Перевозчик'); // +
//        $activeSheet->setCellValue('J'.$i, 'Расходы (дополниетельные)'); // +
//        $activeSheet->setCellValue('K'.$i, 'Расходы (кто платит)'); // +
//        $activeSheet->setCellValue('L'.$i, 'Потратили (наши затраты)'); // +
//        $activeSheet->setCellValue('M'.$i, 'Получили (от клиента)');
//        $activeSheet->setCellValue('N'.$i, 'Заработали'); // +
//        $activeSheet->setCellValue('O'.$i, 'ID'); // +

        $searchModel = new TlDeliveryProposalSearchReportExport();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dps = $dataProvider->getModels();
        $statusKey = [];


        $statusKey['tariff_not_found'] = [
            'qty'=>0,
            'title'=>'Тариф не найден'
        ];
        $statusKey['no_delivery_time'] = [
            'qty'=>0,
            'title'=>'Нет сроков доставки'
        ];
        $statusKey['no_delivery_date'] = [
            'qty'=>0,
            'title'=>'Нет даты доставки'
        ];
        $statusKey['in_time'] = [
            'qty'=>0,
            'title'=>'В срок'
        ];
        $statusKey['more_delivery_time'] = [
            'qty'=>0,
            'title'=>'Больше сроков доставки'
        ];
        $statusKey['less_delivery_time'] = [
            'qty'=>0,
            'title'=>'Меньше сроков доставки'
        ];
        $statusKey['all'] = [
            'qty'=>0,
            'title'=>'Всего'
        ];

        foreach($dps as $model) {

            if($country = $model->routeTo->country) {
                if($searchModel->country_id != $country->id) {
                    continue;
                }
            }

           $billing = TlDeliveryProposalBilling::find()
                        ->select('delivery_term, delivery_term_from, delivery_term_to, id')
                        ->andWhere(
                            [
                              'client_id' => $model->client_id,
                              'tariff_type' => TlDeliveryProposalBilling::TARIFF_TYPE_COMPANY_INDIVIDUAL,
                              'route_from'=>$model->route_from,
                              'route_to'=>$model->route_to,
                            ]
                        )
                        ->one();

//            $i++;
//            $clientTitle = $model->client->title;
            $clientTitle = '';
            if($rClient =  $model->client) {
                $clientTitle = $rClient->title;
            }

//            $activeSheet->setCellValue('A' . $i, $clientTitle); // 'Клиент'
//            $activeSheet->setCellValue('B' . $i, Store::getPointTitle($model->route_from)); // Из
//            $activeSheet->setCellValue('C' . $i, Store::getPointTitle($model->route_to)); // В
//
//            $shippedDatetime = '';
//            if(!empty($model->shipped_datetime)) {
//                $shippedDatetime = Yii::$app->formatter->asDatetime($model->shipped_datetime);
//            }
//
//            $activeSheet->setCellValue('D' . $i, $shippedDatetime); // Дата отгрузки
//
//            $deliveryDatetime = '';
//            if($model->delivery_date) {
//                $deliveryDatetime = Yii::$app->formatter->asDatetime($model->delivery_date);
//            }
//
//            $activeSheet->setCellValue('E' . $i, $deliveryDatetime); // 'Дата получения'
//            $daysOnWay = $model->calculateDiffTR();
//            $activeSheet->setCellValue('F' . $i, $daysOnWay); // 'Дней в пути'

//            $diffDaysOnWay = '';
            // 1 + должны быть заполнены дата отгрузки и доставки  // не полная дата
            // 2 + проверяем указаны  сроки доставки // нет сроков достаки
            // 3 + если доставили не всрок то от максимальный допустимой даты отнимаем срок доставки
            // 4 + если доставили раньше срока?
            // 5 +  если в срок вписались //  в срок
            // 6 +  если тариф не найден//  тариф не найден
            $diffDaysOnWay = '';
            $diffDaysOnWayKey = '';
            $billingID = '';
            $deliveryTerm = '';
            $daysOnWay = $model->calculateDiffTR();
            if($billing) {
                $billingID = $billing->id;
                if(!empty($model->delivery_date) && !empty($model->shipped_datetime)) {
                    if (empty($billing->delivery_term_from) || empty($billing->delivery_term_to)) {
                        $diffDaysOnWay = 'Нет сроков доставки';
                        $diffDaysOnWayKey = 'no_delivery_time';
                        $statusKey['no_delivery_time']['qty'] += 1;
                    } elseif ($daysOnWay >= (int)$billing->delivery_term_from && $daysOnWay <= (int)$billing->delivery_term_to) {
                        $diffDaysOnWay = 'В срок';
                        $diffDaysOnWayKey = 'in_time';
                        $statusKey['in_time']['qty'] += 1;
                    } elseif($daysOnWay < $billing->delivery_term_from) {
                        $diffDaysOnWay = '';
                        $diffDaysOnWayKey = 'less_delivery_time';
                        $diffDaysOnWay .= $daysOnWay - $billing->delivery_term_from;
                        $statusKey['less_delivery_time']['qty'] += 1;

                    } else {
                        $diffDaysOnWay = '';
                        $diffDaysOnWayKey = 'more_delivery_time';
                        $diffDaysOnWay .= $daysOnWay - $billing->delivery_term_to;
                        $statusKey['more_delivery_time']['qty'] += 1;
                    }
                } else {
                    $diffDaysOnWay = 'Нет даты доставки';
                    $diffDaysOnWayKey = 'no_delivery_date';
                    $statusKey['no_delivery_date']['qty'] += 1;
                }

                 $deliveryTerm = $billing->delivery_term; // 'По договору'
//                $activeSheet->setCellValue('H' . $i, $billing->delivery_term); // 'По договору'
            } else {
                $diffDaysOnWay = 'Тариф не найден';
                $diffDaysOnWayKey = 'tariff_not_found';
                $statusKey['tariff_not_found']['qty'] += 1;
            }
            $statusKey['all']['qty'] += 1;
//            VarDumper::dump($diffDaysOnWay,10,true);
//            VarDumper::dump($filter,10,true);
//            die();

            if( (!empty($filter) && $filter == $diffDaysOnWayKey) || empty($filter) ) {
                $i++;
                $activeSheet->setCellValue('A' . $i, $clientTitle); // 'Клиент'
                $activeSheet->setCellValue('B' . $i, Store::getPointTitle($model->route_from)); // Из
                $activeSheet->setCellValue('C' . $i, Store::getPointTitle($model->route_to)); // В

                $shippedDatetime = '';
                if(!empty($model->shipped_datetime)) {
                    $shippedDatetime = Yii::$app->formatter->asDatetime($model->shipped_datetime);
                }
                if($model->client_id == 77) {
                    $shippedDatetime = Yii::$app->formatter->asDatetime($model->created_at);
                }

                $activeSheet->setCellValue('D' . $i, $shippedDatetime); // Дата отгрузки

                $deliveryDatetime = '';
                if($model->delivery_date) {
                    $deliveryDatetime = Yii::$app->formatter->asDatetime($model->delivery_date);
                }
                $activeSheet->setCellValue('E' . $i, $deliveryDatetime); // 'Дата получения'

                $daysOnWay = $model->calculateDiffTR();
                $activeSheet->setCellValue('F' . $i, $daysOnWay); // 'Дней в пути'

                $activeSheet->setCellValue('H' . $i, $deliveryTerm); // 'По договору'
                $activeSheet->setCellValue('G' . $i, $diffDaysOnWay); // 'Разница'
                $activeSheet->setCellValue('I' . $i, $model->id); // 'ID Заявка на доставку'
                $activeSheet->setCellValue('J' . $i, $billingID); // 'ID Тариф'
                $activeSheet->setCellValue('K' . $i, $model->getDeliveryTypeValue()); // 'Тип доставки'
                $activeSheet->setCellValue('L' . $i, isset($model->routeFrom->city) ? $model->routeFrom->city->name : '-');
                $activeSheet->setCellValue('M' . $i, isset($model->routeTo->city) ? $model->routeTo->city->name : '-');

                $activeSheet->setCellValue('N' . $i, isset($model->routeFrom->country) ? $model->routeFrom->country->name : '-');
                $activeSheet->setCellValue('O' . $i, isset($model->routeTo->country) ? $model->routeTo->country->name : '-');
            }
        }
        $i += 3;
        $activeSheet->setCellValue('A'.$i, 'статус');
        $activeSheet->setCellValue('B'.$i, 'количество');
        $activeSheet->setCellValue('C'.$i, 'в процентах');
        $i++;
        $OnePercent = $statusKey['all']['qty'] / 100;

        foreach($statusKey as $k=>$item) {
            $activeSheet->setCellValue('A'.$i, $item['title']);
            $activeSheet->setCellValue('B'.$i, $item['qty']);
            $activeSheet->setCellValue('C'.$i, number_format($item['qty'] / $OnePercent,2));
            ($k == 'no_delivery_date' || $k == 'less_delivery_time'  ? $i += 2 : $i++);
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="full-report-' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

    /*
    *
    *
    * */
    public function actionChartDelivery()
    {
        // chart-delivery
        $searchModel = new TlDeliveryProposalFormSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $clientArray = Client::getActiveItems();
        $storeArray = TLHelper::getStockPointArray();

        $client_id = 2;
        $routeFromDP = 4;
        if (!empty($searchModel->client_id)) {
            $client_id = $searchModel->client_id;
        } else {
            $searchModel->client_id = $client_id;
        }

        if($client_id == 77) { // Это для топерваре
            $routeFromDP = 388; // Это склад топерваре
        }

        $cityOrShop = 'city';
        if (!empty($searchModel->city_or_shop)) {
            $cityOrShop = $searchModel->city_or_shop;
        } else {
            $searchModel->city_or_shop = $cityOrShop;
        }

        $country_id = '1'; //Казахстан
        if (!empty($searchModel->country_id)) {
            $country_id = $searchModel->country_id;
        } else {
            $searchModel->country_id = $country_id;
        }

        // SHIPPED DATETIME
        $dateFrom = '';
        $dateTo = '';

        if (!empty($searchModel->shipped_datetime)) {
            $date = explode('/', $searchModel->shipped_datetime);

            $dateFrom = trim($date[0]) . ' 00:00:00';
            $dateTo = trim($date[1]) . ' 23:59:59';

            $dateFrom = strtotime($dateFrom);
            $dateTo = strtotime($dateTo);
        } else {
            $tStart = time() - 30 * 24 * 3600;
            $tEnd = time();
            $dateFrom = date('Y-m-d', $tStart) . ' 00:00:00';
            $dateTo = date('Y-m-d', $tEnd) . ' 23:59:59';

            $searchModel->shipped_datetime = date('Y-m-d', $tStart) . ' / ' . date('Y-m-d', $tEnd);

            $dateFrom = strtotime($dateFrom);
            $dateTo = strtotime($dateTo);
        }

        if ($cityOrShop == 'shop') {
            $cityStoreAll = Store::find()
                ->select('id as ids, name')
                ->andWhere(['client_id' => $client_id, 'type_use' => Store::TYPE_USE_STORE])
                ->andFilterWhere(['country_id' => $country_id])
                ->orderBy('city_id')
                ->asArray()
                ->all();

        } elseif($cityOrShop == 'city') {
            $cityStoreAll = Store::find()
                ->select('city_id, GROUP_CONCAT(id) as ids')
                ->andWhere(['client_id' => $client_id, 'type_use' => Store::TYPE_USE_STORE])
                ->andFilterWhere(['country_id' => $country_id])
                ->groupBy('city_id')
                ->orderBy('city_id')
                ->asArray()
                ->all();

        } elseif($cityOrShop == 'country') { // TODO Не ипользуется
            $cityStoreAll = Store::find()
                ->select('country_id, GROUP_CONCAT(id) as ids')
                ->andWhere(['client_id' => $client_id, 'type_use' => Store::TYPE_USE_STORE])
                ->groupBy('country_id')
                ->orderBy('country_id')
                ->asArray()
                ->all();
        }

        // VarDumper::dump($cityStoreAll,10,true);
        // die;

        foreach ($cityStoreAll as $store) {

            $queryDP = TlDeliveryProposal::find();

            if(!empty($dateFrom) && !empty($dateTo)) {
                if($client_id == 77) {
                    $queryDP->andWhere(['between', 'created_at', $dateFrom, $dateTo]);
                } else {
                    $queryDP->andWhere(['between', 'shipped_datetime', $dateFrom, $dateTo]);
                }
            }

            $dps = $queryDP->andWhere(['client_id' => $client_id, 'route_from' => $routeFromDP, 'route_to' => explode(',', $store['ids'])])->all();

            if(isset($store['city_id'])) {
                $keyID = $store['city_id'];
            } elseif(isset($store['name'])) {
                $keyID = $store['ids'];
            } elseif(isset($store['country_id'])) {
                $keyID = $store['country_id'];
            }

            $i = 1;
            $statusKey[$keyID]['tariff_not_found'] = [
                'qty' => 0,
                'title' => 'Тариф не найден'
            ];
            $statusKey[$keyID]['no_delivery_time'] = [
                'qty' => 0,
                'title' => 'Нет сроков доставки'
            ];
            $statusKey[$keyID]['no_delivery_date'] = [
                'qty' => 0,
                'title' => 'Нет даты доставки'
            ];
            $statusKey[$keyID]['in_time'] = [
                'qty' => 0,
                'title' => 'В срок'
            ];
            $statusKey[$keyID]['more_delivery_time'] = [
                'qty' => 0,
                'title' => 'Больше сроков доставки'
            ];
//            $statusKey[$store['city_id']]['less_delivery_time'] = [
//                'qty'=>0,
//                'title'=>'Меньше сроков доставки'
//            ];
//            $statusKey[$store['city_id']]['all'] = [
//                'qty'=>0,
//                'title'=>'Всего'
//            ];

            foreach ($dps as $model) {

                $billing = TlDeliveryProposalBilling::find()
                    ->select('delivery_term, delivery_term_from, delivery_term_to, id')
                    ->andWhere(
                        [
                            'client_id' => $model->client_id,
                            'tariff_type' => TlDeliveryProposalBilling::TARIFF_TYPE_COMPANY_INDIVIDUAL,
                            'route_from' => $model->route_from,
                            'route_to' => $model->route_to,
                        ]
                    )
                    ->one();

                $i++;
                $daysOnWay = $model->calculateDiffTR();

                if ($billing) {
                    if (!empty($model->delivery_date) && !empty($model->shipped_datetime)) {
                        if (empty($billing->delivery_term_from) || empty($billing->delivery_term_to)) {
                            $statusKey[$keyID]['no_delivery_time']['qty'] += 1;
                        } elseif ($daysOnWay >= (int)$billing->delivery_term_from && $daysOnWay <= (int)$billing->delivery_term_to) {
                            $statusKey[$keyID]['in_time']['qty'] += 1;
                        } elseif ($daysOnWay < $billing->delivery_term_from) {
                            $statusKey[$keyID]['in_time']['qty'] += 1;

                        } else {
                            $statusKey[$keyID]['more_delivery_time']['qty'] += 1;
                        }
                    } else {
                        $statusKey[$keyID]['no_delivery_date']['qty'] += 1;
                    }
                } else {
                    // TODO Что писать если не заданы сроки доставки
                    $statusKey[$keyID]['tariff_not_found']['qty'] += 1;
                }
//                $statusKey[$store['city_id']]['all']['qty'] += 1;
            }
        }

        $statusKeyColumn = [];
        $statusKeyColumn['tariff_not_found'] = [
            'title' => 'Тариф не найден'
        ];
        $statusKeyColumn['no_delivery_time'] = [
            'title' => 'Нет сроков доставки'
        ];
        $statusKeyColumn['no_delivery_date'] = [
            'title' => 'Нет даты доставки'
        ];
        $statusKeyColumn['in_time'] = [
            'title' => 'В срок'
        ];
        $statusKeyColumn['more_delivery_time'] = [
            'title' => 'Больше сроков доставки'
        ];
//        $statusKeyColumn['less_delivery_time'] = [
//            'title'=>'Меньше сроков доставки'
//        ];
//        $statusKeyColumn['all'] = [
//            'title'=>'Всего'
//        ];
        $cityAll = [];
        $columns = [];
        $columns[0][] = 'x';
        foreach ($statusKey as $key => $value) {

            if($cityOrShop == 'shop') {
                $cityName = Store::findOne($key)->getPointTitleByPattern('default');
            } elseif($cityOrShop == 'city') {
                $cityName = City::findOne($key)->name;
            } else {
                $cityName = Country::findOne($key)->name;
            }

            $columns[0][] = $cityName;
            $cityAll[$key] = $cityName;
        }
//        $columns[0][] = 'Всего'; // all

        $groups = [];
        $names = [];
        $k = 0;
        foreach ($statusKeyColumn as $keyColumn => $valueColumn) {
//
            $title = $valueColumn['title'];
            $groups[0][] = $keyColumn;
            $k++;
            $columns[$k][] = $keyColumn;
            $qty = 0;

            $names[$keyColumn] = $title;

            foreach ($statusKey as $key => $value) {
                $columns[$k][] = $value[$keyColumn]['qty'];
                $qty += $value[$keyColumn]['qty'];
            }
//            $columns[$k][] = $qty;// all
        }

        $columnsByCity = $columns;
        $groupsByCity = $groups;
        $namesByCity = $names;

//        VarDumper::dump($columnsByCity,10,true);
//        die;

        $storeM3 = [];
        foreach ($cityStoreAll as $store) {

            if(isset($store['city_id'])) {
                $keyID = $store['city_id'];
            } elseif(isset($store['name'])) {
                $keyID = $store['ids'];
            } elseif(isset($store['country_id'])) {
                $keyID = $store['country_id'];
            }

            $queryDP = TlDeliveryProposal::find();
            if(!empty($dateFrom) && !empty($dateTo)) {
//                $queryDP->andWhere(['between', 'shipped_datetime', $dateFrom,$dateTo]);
                if($client_id == 77) {
                    $queryDP->andWhere(['between', 'created_at', $dateFrom, $dateTo]);
                } else {
                    $queryDP->andWhere(['between', 'shipped_datetime', $dateFrom, $dateTo]);
                }
            }
            $dps = $queryDP->andWhere(['client_id' => $client_id, 'route_from' => $routeFromDP, 'route_to' => explode(',', $store['ids'])])->all();

            foreach ($dps as $model) {
                if(isset($storeM3[$keyID])) {
                    $storeM3[$keyID] += $model->mc_actual;
                } else {
                    $storeM3[$keyID] = $model->mc_actual;
                }
            }
        }

        $columns = [];
        $columns[0][] = 'x';
        $columns[1][] = 'по М3';
        $qty = 0;
        foreach ($storeM3 as $cityID => $qtyInCity) {

            if($cityOrShop == 'shop') {
                $cityName = Store::findOne($cityID)->getPointTitleByPattern('default');
            } elseif($cityOrShop == 'city') {
                $cityName = City::findOne($cityID)->name;
            } else {
                $cityName = Country::findOne($cityID)->name;
            }

            $columns[0][] = $cityName;
            $columns[1][] = $qtyInCity;
            $qty += $qtyInCity;
        }

        $columnsM3 = $columns;


        $storeM3 = [];
        foreach ($cityStoreAll as $store) {

            if(isset($store['city_id'])) {
                $keyID = $store['city_id'];
            } elseif(isset($store['name'])) {
                $keyID = $store['ids'];
            } elseif(isset($store['country_id'])) {
                $keyID = $store['country_id'];
            }

            $queryDP = TlDeliveryProposal::find();
            if(!empty($dateFrom) && !empty($dateTo)) {
//                $queryDP->andWhere(['between', 'shipped_datetime', $dateFrom,$dateTo]);
                if($client_id == 77) {
                    $queryDP->andWhere(['between', 'created_at', $dateFrom, $dateTo]);
                } else {
                    $queryDP->andWhere(['between', 'shipped_datetime', $dateFrom, $dateTo]);
                }
            }
            $dps = $queryDP->andWhere(['client_id' => $client_id, 'route_from' => $routeFromDP, 'route_to' => explode(',', $store['ids'])])->all();

            foreach ($dps as $model) {
                if(isset($storeM3[$keyID])) {
                    $storeM3[$keyID] += $model->number_places_actual;
                } else {
                    $storeM3[$keyID] = $model->number_places_actual;
                }
            }
        }

        $columns = [];
        $columns[0][] = 'x';
        $columns[1][] = 'по местам';
        $qty = 0;
        foreach ($storeM3 as $cityID => $qtyInCity) {

            if($cityOrShop == 'shop') {
                $cityName = Store::findOne($cityID)->getPointTitleByPattern('default');
            } elseif($cityOrShop == 'city') {
                $cityName = City::findOne($cityID)->name;
            } else {
                $cityName = Country::findOne($cityID)->name;
            }

            $columns[0][] = $cityName;
            $columns[1][] = $qtyInCity;
            $qty += $qtyInCity;
        }

        $columnsNP = $columns;

        return $this->render('chart-delivery',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'clientArray' => $clientArray,
            'storeArray' => $storeArray,

            'columnsByCity' => $columnsByCity,
            'groupsByCity' => $groupsByCity,
            'namesByCity' => $namesByCity,

            'columnsM3' => $columnsM3,

            'columnsNP' => $columnsNP,
            'routeFromDP' => $routeFromDP,
        ]);
    }

    public function actionToDay()
    {
        //+ сколько приходов еще нужно собрать и сколько собрали за сегодня
        // сколько возвраты за сегодня
        // сколько сборок в разных статусах
        // сколько кросс-доков
        // сколько в пути(сборок и кросс-доков)
        // сколько больше срока доставки
        $service = new ReportToDay();
        $outboundToDay = $service->qtyScannedOutboundLotToDay();
        $sumOutboundOrderInProcess = $service->sumOutboundOrderInProcess();
        $outboundLeftToDay = $service->qtyLeftOutboundToDay();
        $outboundOnRoadToDay = $service->qtyOnRoadOutboundToDay();
        $acceptedCrossDockBoxToDay = $service->getAcceptedCrossDockBoxToDay();
        $inProcessCrossDockBoxToDay = $service->getInProcessCrossDockBoxToDay();

        $readyForDelivery = $service->readyForDelivery();
//        $readyForDeliveryByStore = $service->readyForDeliveryByStore();

        $inboundOrderInProcess = $service->inboundOrderInProcess();
        $inboundInProcessByOrders = $service->inboundInProcessByOrders();
        $inboundToDay = $service->qtyScannedInboundLotToDay();
        $moreDeliveryTime = $service->getMoreDeliveryTime();
        $moreDeliveryTimeStores = StoreRepository::getStoreByIDs(ArrayHelper::getColumn($moreDeliveryTime,'to_point_id'));
        $outboundInProcessByRouteDirections = $service->sumOutboundOrderInProcessByRouteDirection();
        $readyForDeliveryByRouteDirections = $service->readyForDeliveryByRouteDirection();
        $readyForDeliveryStores = StoreRepository::getStoreByClient();
        $currentDateTime = $service->getCurrentDateTime();
        $inboundOrderOnRoadToKz = $service->inboundOrderOnRoadToKz();

//         $r = new \common\modules\city\RouteDirection\repository\Repository();
//
//        VarDumper::dump($service->readyForDeliveryByRouteDirection(),10,true);
//        VarDumper::dump($inProcessCrossDockBoxToDay,10,true);
//        die;
//        VarDumper::dump($inboundOrderOnRoadToKz,10,true);
//        VarDumper::dump($readyForDeliveryStores,10,true);
//        VarDumper::dump(ArrayHelper::getColumn($readyForDeliveryByStore,'to_point_id'),10,true);
//        die;

//        $service = new ReportToDay();
//        $outboundToDay = $service->qtyScannedOutboundLotToDay();
//        $inboundToDay = $service->qtyScannedInboundLotToDay();
//        $acceptedCrossDockBoxToDay = $service->getAcceptedCrossDockBoxToDay();
//        $mailService = new MailService();
//        $mailService->sendMailIfReadyReportToDay($outboundToDay,$inboundToDay,$acceptedCrossDockBoxToDay);

        return $this->render('to-day',[
            'outboundToDay'=>$outboundToDay,
            'inboundToDay'=>$inboundToDay,
            'readyForDelivery'=>$readyForDelivery,
//            'readyForDeliveryByStore'=>$readyForDeliveryByStore,
            'readyForDeliveryStores'=>$readyForDeliveryStores,
            'inboundOrderInProcess'=>$inboundOrderInProcess,
            'inboundInProcessByOrders'=>$inboundInProcessByOrders,
            'sumOutboundOrderInProcess'=>$sumOutboundOrderInProcess,
            'outboundLeftToDay'=>$outboundLeftToDay,
            'outboundOnRoadToDay'=>$outboundOnRoadToDay,
            'moreDeliveryTime'=>$moreDeliveryTime,
            'moreDeliveryTimeStores'=>$moreDeliveryTimeStores,
            'acceptedCrossDockBoxToDay'=>$acceptedCrossDockBoxToDay,
            'outboundInProcessByRouteDirections'=>$outboundInProcessByRouteDirections,
            'readyForDeliveryByRouteDirections'=>$readyForDeliveryByRouteDirections,
            'currentDateTime'=>$currentDateTime,
            'inboundOrderOnRoadToKz'=>$inboundOrderOnRoadToKz,
            'inProcessCrossDockBoxToDay'=>$inProcessCrossDockBoxToDay,
        ]);
    }

}