<?php

namespace app\modules\warehouseDistribution\controllers\tupperware;

use clientDepartment\modules\report\models\TlDeliveryProposalFormSearch;
use clientDepartment\modules\report\models\TlDeliveryProposalSearchReportExport;
use common\modules\billing\models\TlDeliveryProposalBilling;
use common\modules\city\models\City;
use common\modules\city\models\Country;
use common\modules\client\models\ClientEmployees;
use common\modules\store\models\Store;
use Yii;
use clientDepartment\components\Controller;
use common\modules\client\models\Client;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\components\TLHelper;
use yii\helpers\BaseFileHelper;
use yii\helpers\Inflector;
use yii\helpers\VarDumper;


class ChartDeliveryController extends Controller
{
    /*
       *
       *
       * */
    public function actionIndex()
    {
        // chart-delivery
        $searchModel = new TlDeliveryProposalFormSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $clientArray = Client::getActiveItems();
//        $storeArray = TLHelper::getStockPointArray();
        $clientEmploy = ClientEmployees::findOne(['user_id'=>Yii::$app->user->id]);
        $storeArray = TLHelper::getStoreArrayByClientID($clientEmploy->client_id);

        $client_id = 77;
        $routeFromDP = 388;
        if (!empty($searchModel->client_id)) {
            $client_id = $searchModel->client_id;
        } else {
            $searchModel->client_id = $client_id;
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

        } elseif($cityOrShop == 'country') {
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
        $statusKey = [];
        foreach ($cityStoreAll as $store) {

            $queryDP = TlDeliveryProposal::find();

            if(!empty($dateFrom) && !empty($dateTo)) {
                $queryDP->andWhere(['between', 'created_at', $dateFrom,$dateTo]);
//                $queryDP->andWhere(['between', 'shipped_datetime', $dateFrom,$dateTo]);
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
//                'title' => 'Тариф не найден'
                'title' => Yii::t('kpi-delivered/titles','Rate not found')
            ];
            $statusKey[$keyID]['no_delivery_time'] = [
                'qty' => 0,
//                'title' => 'Нет сроков доставки'
                'title' =>  Yii::t('kpi-delivered/titles','There is no delivery time')
            ];
            $statusKey[$keyID]['no_delivery_date'] = [
                'qty' => 0,
//                'title' => 'Нет даты доставки'
                'title' =>  Yii::t('kpi-delivered/titles','No date of delivery')
            ];
            $statusKey[$keyID]['in_time'] = [
                'qty' => 0,
//                'title' => 'В срок'
                'title' =>  Yii::t('kpi-delivered/titles','In time')
            ];
            $statusKey[$keyID]['more_delivery_time'] = [
                'qty' => 0,
//                'title' => 'Больше сроков доставки'
                'title' =>  Yii::t('kpi-delivered/titles','More delivery time')
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
//            'title' => 'Тариф не найден'
            'title' => Yii::t('kpi-delivered/titles','Rate not found')
        ];
        $statusKeyColumn['no_delivery_time'] = [
//            'title' => 'Нет сроков доставки'
            'title' => Yii::t('kpi-delivered/titles','There is no delivery time')
        ];
        $statusKeyColumn['no_delivery_date'] = [
//            'title' => 'Нет даты доставки'
            'title' => Yii::t('kpi-delivered/titles','No date of delivery')
        ];
        $statusKeyColumn['in_time'] = [
//            'title' => 'В срок'
            'title' => Yii::t('kpi-delivered/titles','In time')
        ];
        $statusKeyColumn['more_delivery_time'] = [
//            'title' => 'Больше сроков доставки'
            'title' => Yii::t('kpi-delivered/titles','More delivery time')
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
//                $cityName = Store::findOne($key)->getPointTitleByPattern('default');
                $cityName = $storeArray[$key];
            } elseif($cityOrShop == 'city') {

                if(Yii::$app->language == 'tr') {
                    $cityName = Inflector::slug(City::findOne($key)->name);
                } else {
                    $cityName = City::findOne($key)->name;
                }

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
                $queryDP->andWhere(['between', 'created_at', $dateFrom,$dateTo]);
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
//        $columns[1][] = 'по М3';
        $columns[1][] = Yii::t('kpi-delivered/titles','By M3');
        $qty = 0;
        foreach ($storeM3 as $cityID => $qtyInCity) {

            if($cityOrShop == 'shop') {
//                $cityName = Store::findOne($cityID)->getPointTitleByPattern('default');
                $cityName = $storeArray[$cityID];
            } elseif($cityOrShop == 'city') {
                //$cityName = City::findOne($cityID)->name;
                if(Yii::$app->language == 'tr') {
                    $cityName = Inflector::slug(City::findOne($cityID)->name);
                } else {
                    $cityName = City::findOne($cityID)->name;
                }
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
                $queryDP->andWhere(['between', 'created_at', $dateFrom,$dateTo]);
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
//        $columns[1][] = 'по местам';
        $columns[1][] = Yii::t('kpi-delivered/titles','By Places');
        $qty = 0;
        foreach ($storeM3 as $cityID => $qtyInCity) {

            if($cityOrShop == 'shop') {
//                $cityName = Store::findOne($cityID)->getPointTitleByPattern('default');
                $cityName = $storeArray[$cityID];
            } elseif($cityOrShop == 'city') {
//                $cityName = City::findOne($cityID)->name;
                if(Yii::$app->language == 'tr') {
                    $cityName = Inflector::slug(City::findOne($cityID)->name);
                } else {
                    $cityName = City::findOne($cityID)->name;
                }
            } else {
                $cityName = Country::findOne($cityID)->name;
            }

            $columns[0][] = $cityName;
            $columns[1][] = $qtyInCity;
            $qty += $qtyInCity;
        }

        $columnsNP = $columns;

        return $this->render('index',[
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

    /*
    *
    * Export data to EXEL
    *
    * */
    public function actionExportToExcelKpiDelivered()
    {
        // export-to-excel-kpi-delivered

        if(Yii::$app->language == 'tr') {
            Yii::$app->language = 'en';
        }

        $filter = Yii::$app->request->get('filter');

        $clientEmploy = ClientEmployees::findOne(['user_id'=>Yii::$app->user->id]);
        $clientStoreArray = TLHelper::getStoreArrayByClientID($clientEmploy->client_id);

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
        $activeSheet->setCellValue('A'.$i, Yii::t('kpi-delivered/titles','Client')); // + 'Клиент'
        $activeSheet->setCellValue('B'.$i, Yii::t('kpi-delivered/titles','From')); // + 'Из'
        $activeSheet->setCellValue('C'.$i, Yii::t('kpi-delivered/titles','To')); // + 'В'
        $activeSheet->setCellValue('D'.$i, Yii::t('kpi-delivered/titles','Ship date')); // +   'Дата отгрузки'
        $activeSheet->setCellValue('E'.$i, Yii::t('kpi-delivered/titles','Date of receipt')); // +  'Дата получения'
        $activeSheet->setCellValue('F'.$i, Yii::t('kpi-delivered/titles','On the way')); // + 'В пути'
        $activeSheet->setCellValue('G'.$i, Yii::t('kpi-delivered/titles','The difference')); // + 'Разница'
        $activeSheet->setCellValue('H'.$i, Yii::t('kpi-delivered/titles','Under the contract')); // + 'По договору'
        $activeSheet->setCellValue('I'.$i, Yii::t('kpi-delivered/titles','ID delivery Request') ); // + 'ID Заявка на доставку'
        $activeSheet->setCellValue('J'.$i, Yii::t('kpi-delivered/titles','ID Tariff') ); // + 'ID Тариф'
        $activeSheet->setCellValue('K'.$i, Yii::t('kpi-delivered/titles','The delivery type') ); // + 'Тип доставки'
        $activeSheet->setCellValue('L'.$i, Yii::t('kpi-delivered/titles','Out of city') );  // 'Из города'
        $activeSheet->setCellValue('M'.$i, Yii::t('kpi-delivered/titles','In the city') ); // 'В город'
        $activeSheet->setCellValue('N'.$i, Yii::t('kpi-delivered/titles','From the country')); //  'Из страны'
        $activeSheet->setCellValue('O'.$i, Yii::t('kpi-delivered/titles','In the country') ); // 'В страну'

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
//            'title'=>'Тариф не найден'
            'title'=>Yii::t('kpi-delivered/titles','Rate not found')
        ];
        $statusKey['no_delivery_time'] = [
            'qty'=>0,
//            'title'=>'Нет сроков доставки'
            'title'=>Yii::t('kpi-delivered/titles','There is no delivery time')
        ];
        $statusKey['no_delivery_date'] = [
            'qty'=>0,
//            'title'=>'Нет даты доставки'
            'title'=>Yii::t('kpi-delivered/titles','No date of delivery')
        ];
        $statusKey['in_time'] = [
            'qty'=>0,
//            'title'=>'В срок'
            'title'=>Yii::t('kpi-delivered/titles','In time')
        ];
        $statusKey['more_delivery_time'] = [
            'qty'=>0,
//            'title'=>'Больше сроков доставки'
            'title'=>Yii::t('kpi-delivered/titles','More delivery time')
        ];
        $statusKey['less_delivery_time'] = [
            'qty'=>0,
//            'title'=>'Меньше сроков доставки'
            'title'=>Yii::t('kpi-delivered/titles','Less delivery time')
        ];
        $statusKey['all'] = [
            'qty'=>0,
//            'title'=>'Всего'
            'title'=>Yii::t('kpi-delivered/titles','Total')
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
//                        $diffDaysOnWay = 'Нет сроков доставки';
                        $diffDaysOnWay = Yii::t('kpi-delivered/titles','There is no delivery time');
                        $diffDaysOnWayKey = 'no_delivery_time';
                        $statusKey['no_delivery_time']['qty'] += 1;
                    } elseif ($daysOnWay >= (int)$billing->delivery_term_from && $daysOnWay <= (int)$billing->delivery_term_to) {
//                        $diffDaysOnWay = 'В срок';
                        $diffDaysOnWay = Yii::t('kpi-delivered/titles','In time');
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
//                    $diffDaysOnWay = 'Нет даты доставки';
                    $diffDaysOnWay = Yii::t('kpi-delivered/titles','No date of delivery');
                    $diffDaysOnWayKey = 'no_delivery_date';
                    $statusKey['no_delivery_date']['qty'] += 1;
                }

                $deliveryTerm = $billing->delivery_term; // 'По договору'
//                $activeSheet->setCellValue('H' . $i, $billing->delivery_term); // 'По договору'
            } else {
//                $diffDaysOnWay = 'Тариф не найден';
                $diffDaysOnWay = Yii::t('kpi-delivered/titles','Rate not found');
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
//                $activeSheet->setCellValue('B' . $i, Store::getPointTitle($model->route_from)); // Из
                $activeSheet->setCellValue('B' . $i, isset($clientStoreArray[$model->route_from]) ? $clientStoreArray[$model->route_from] : '' ); // Из
//                $activeSheet->setCellValue('C' . $i, Store::getPointTitle($model->route_to)); // В
                $activeSheet->setCellValue('C' . $i,isset($clientStoreArray[$model->route_to]) ? $clientStoreArray[$model->route_to] : '' ); // В

//                $shippedDatetime = '';
//                if(!empty($model->shipped_datetime)) {
//                    $shippedDatetime = Yii::$app->formatter->asDatetime($model->shipped_datetime);
//                }
//                if($model->client_id == 77) {
                $shippedDatetime = Yii::$app->formatter->asDatetime($model->created_at);
//                }

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
                $activeSheet->setCellValue('L' . $i, isset($model->routeFrom->city) ? Inflector::slug($model->routeFrom->city->name) : '-');
                $activeSheet->setCellValue('M' . $i, isset($model->routeTo->city) ? Inflector::slug($model->routeTo->city->name) : '-');

                $activeSheet->setCellValue('N' . $i, isset($model->routeFrom->country) ? Inflector::slug($model->routeFrom->country->name) : '-');
                $activeSheet->setCellValue('O' . $i, isset($model->routeTo->country) ? Inflector::slug($model->routeTo->country->name) : '-');
            }
        }
        $i += 3;
//        $activeSheet->setCellValue('A'.$i, 'статус');
        $activeSheet->setCellValue('A'.$i, Yii::t('kpi-delivered/titles','Status'));
//        $activeSheet->setCellValue('B'.$i, 'количество');
        $activeSheet->setCellValue('B'.$i, Yii::t('kpi-delivered/titles','Quantity'));
//        $activeSheet->setCellValue('C'.$i, 'в процентах');
        $activeSheet->setCellValue('C'.$i, Yii::t('kpi-delivered/titles','Percentage'));
        $i++;
        $OnePercent = $statusKey['all']['qty'] / 100;

        foreach($statusKey as $k=>$item) {
            $activeSheet->setCellValue('A'.$i, $item['title']);
            $activeSheet->setCellValue('B'.$i, $item['qty']);
            if($item['qty']) {
                $activeSheet->setCellValue('C' . $i, number_format($item['qty'] / $OnePercent, 2));
            } else {
                $activeSheet->setCellValue('C' . $i, number_format(0, 2));

            }
            ($k == 'no_delivery_date' || $k == 'less_delivery_time'  ? $i += 2 : $i++);
        }
        //die('dddd9');

//        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//        header('Content-Disposition: attachment;filename="full-report-' . time() . '.xlsx"');
//        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//        $objWriter->save('php://output');
        $fileName = 'kpi-delivered-report-' . time() . '.xlsx';


        $dirPath = 'uploads/kpi-delivered/'.date('Ymd').'/'.date('His');
//        $fileName = time() . '-box-label.pdf';
        BaseFileHelper::createDirectory($dirPath);
        $fullPath = $dirPath.'/'.$fileName;
        $objWriter->save($fullPath);

        return Yii::$app->response->sendFile($fullPath,$fileName);
//        return $this->send
//        Yii::$app->end();
    }
}