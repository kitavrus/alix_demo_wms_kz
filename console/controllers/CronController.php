<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 12.01.2015
 * Time: 10:54
 */
namespace console\controllers;
use stockDepartment\modules\kaspi\services\Alix1CApiService;
use stockDepartment\modules\kaspi\services\OneCSalesSyncService;
use stockDepartment\modules\kaspi\services\OrderImportService;
use stockDepartment\modules\kaspi\services\OrderReturnService;
use stockDepartment\modules\kaspi\services\OrderStatusSyncService;
use stockDepartment\modules\kaspi\services\PriceService;
use stockDepartment\modules\kaspi\services\ProductSyncService;
use common\modules\billing\models\TlDeliveryProposalBilling;
use common\modules\city\models\City;
use common\modules\city\models\Country;
use common\modules\city\models\Region;
use common\modules\client\models\Client;
use common\modules\transportLogistics\components\TLHelper;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use Yii;
use yii\console\Controller;
use bossDepartment\modules\report\models\TlDeliveryProposalSearchReportExport;
use common\modules\store\models\Store;
use common\components\MailManager;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class CronController extends Controller
{

    /* Отпрявляем письмо(уведомление) если последний день доставки заявки.
    * в связанных записях
    * @param array $statusArray
    * @return mixed
    * TODO Добавить в крон на живом!!! (11,01,2015)
    **/
    public function actionReportKpiDeliveryLastDay()
    {
        // php yii cron/report-kpi-delivery-last-day
        $sendMailData = [];

        $deliveryProposals = TlDeliveryProposal::find()
            ->andWhere([
                'status'=>[TlDeliveryProposal::STATUS_ON_ROUTE],
                'delivery_type'=>[TlDeliveryProposal::DELIVERY_TYPE_OUTBOUND],
            ])
            ->orderBy(['id'=>SORT_DESC])
            ->all();

        $i = 0;
        if(!empty($deliveryProposals)) {

            $clientArray = Client::getActiveTMSItems();
            $storeArray = TLHelper::getStockPointArray();
            $cityArray = City::getArrayData();
            $regionArray = Region::getArrayData();
            $countryArray = Country::getArrayData();

            $tz = Yii::$app->params['dateControlDisplayTimezone'];
            $dtNow = new \DateTime('now', new \DateTimeZone($tz));

//            VarDumper::dump($clientArray);
//            die;
            foreach ($deliveryProposals as $dpItem) {

                $billing = TlDeliveryProposalBilling::find()
                    ->select('delivery_term, delivery_term_from, delivery_term_to, id')
                    ->andWhere(
                        [
                            'client_id' => $dpItem->client_id,
                            'tariff_type' => TlDeliveryProposalBilling::TARIFF_TYPE_COMPANY_INDIVIDUAL,
                            'route_from' => $dpItem->route_from,
                            'route_to' => $dpItem->route_to,
                        ]
                    )
                    ->one();

                if ($billing) {
                    if (!empty($dpItem->shipped_datetime)) {

                        $dtStart = new \DateTime();
                        $dtStart->setTimestamp($dpItem->shipped_datetime);
                        $dtStart->setTimezone( new \DateTimeZone($tz));
                        $strStart = $dtStart->format('Y').'-'.$dtStart->format('m').'-'.$dtStart->format('d').' '.$dtStart->format('H').':'.$dtStart->format('i').':'.$dtStart->format('s');
//                        echo ++$i . ' ' . $strStart. ' ' . $dpItem->id."\n";
//                        $dtNow = new \DateTime('now', new \DateTimeZone($tz));
                        $strNow = $dtNow->format('Y').'-'.$dtNow->format('m').'-'.$dtNow->format('d').' '.$dtNow->format('H').':'.$dtNow->format('i').':'.$dtNow->format('s');
//                        echo $i . ' ' . $strNow. ' ' . $dpItem->id."\n";
//                        echo $i . ' '. $billing->delivery_term_to. ' ' . $dpItem->id."\n";

                        $interval = $dtStart->diff($dtNow);
                        $daysOnWay = ((int)$interval->days * 24) + $interval->h;

                        $lastTime = ((int)$billing->delivery_term_to * 24) - 12;

                        $diffDays = $interval->days;
                        $diffHours = $interval->h;

                        echo $i . ' '. $daysOnWay. ' ' .$lastTime."\n";
                        $delivery_term = $billing->delivery_term_from.' - '. $billing->delivery_term_to;
                        if ($daysOnWay >= $lastTime ) {
                            $sendMailData [] = [
                                'id'=>$dpItem->id,
                                'client_name'=>ArrayHelper::getValue($clientArray,$dpItem->client_id),
                                'store_from'=>ArrayHelper::getValue($storeArray,$dpItem->route_from),
                                'store_to'=>ArrayHelper::getValue($storeArray,$dpItem->route_to),
                                'delivery_term'=>$delivery_term,
                                'diff_days'=>$diffDays,
                                'diff_hours'=>$diffHours,
                                'start'=>$strStart,
                                'now'=>$strNow,
                            ];
                            echo 'send mail'."\n";
                        }
                        echo "\n";
                        echo "\n";
                    }

                } else {
                    echo 'Тариф не найден'."\n";
                }
            }
        }

        if(!empty($sendMailData)) {
            $mailManager = new MailManager();
            $mailManager->sendKpiDeliveryLastDatetimeMail($sendMailData);
        }

        return 0;
    }



    public function actionReport()
    {
        $objPHPExcel = new \PHPExcel();
        $title = 'report-' . date('d_m_Y').'.xlsx';

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
            ->setTitle($title);

//        $activeSheet->setCellValue('A1', '');
//        $activeSheet->setCellValue('A2', 'Приложения к акту');
//        $activeSheet->setCellValue('A3', '');
//        $activeSheet->setCellValue('A4', 'Клиент');
//        $activeSheet->setCellValue('B4', '');
//        $activeSheet->setCellValue('A5', '');
//        $activeSheet->setCellValue('A6', '');
//        $activeSheet->setCellValue('H1', date('d/m/Y'));

        $i = 1;
        $activeSheet->setCellValue('A' . $i, 'Из');
        $activeSheet->setCellValue('B' . $i, 'В');
        $activeSheet->setCellValue('C' . $i, 'Дата отгрузки');
        $activeSheet->setCellValue('D' . $i, 'Дата получения');
        $activeSheet->setCellValue('E' . $i, 'Кол-во мест');
        $activeSheet->setCellValue('F' . $i, 'Кол-во кг');
        $activeSheet->setCellValue('G' . $i, 'Кол-во М3');
//        $activeSheet->setCellValue('H'.$i, 'Стоимость');
        $activeSheet->setCellValue('H' . $i, 'Получили');
//        $activeSheet->setCellValue('I'.$i, 'ID');
        $activeSheet->setCellValue('I' . $i, 'Потратили');
        $activeSheet->setCellValue('J' . $i, 'Заработали');
        $activeSheet->setCellValue('K' . $i, 'ID');

        $searchModel = new TlDeliveryProposalSearchReportExport();

        $date = new \DateTime();
        $date->add(\DateInterval::createFromDateString('yesterday'));
        $yesterday = $date->format('Y-m-d');

        $dataProvider = $searchModel->search(['TlDeliveryProposalSearch'=>
            [   'id' => '',
                'orders' => '',
                'shipped_datetime'=>$yesterday.'/'.date('Y-m-d'),
                'client_id' => '',
                'route_from' => '',
                'route_to' => '' ,
                'mc_actual' => '',
                'kg_actual' => '',
                'number_places_actual' => '',
                'price_invoice_with_vat' => '',
                'delivery_type' => '',
                'status' => '',
                'status_invoice' => '']
        ]);

        $filename = 'report-'.$yesterday.'-'.date('Y-m-d').'.xlsx';

        $dps = $dataProvider->getModels();

        $priceInvoiceWithVatTotal = 0;
        $priceRowEarnedTotal = 0;
        $priceRowExpendTotal = 0;


        foreach ($dps as $model) {

            $i++;

            $activeSheet->setCellValue('A' . $i, Store::getPointTitle($model->route_from));
            $activeSheet->setCellValue('B' . $i, Store::getPointTitle($model->route_to));

            $shippedDatetime = '';
            if (!empty($model->shipped_datetime)) {
                $shippedDatetime = Yii::$app->formatter->asDate($model->shipped_datetime, 'php:d/m/Y');
            }

            $activeSheet->setCellValue('C' . $i, $shippedDatetime);

            $deliveryDatetime = '';
            if ($model->delivery_date) {
                $deliveryDatetime = Yii::$app->formatter->asDate($model->delivery_date, 'php:d/m/Y');
            }

            $activeSheet->setCellValue('D' . $i, $deliveryDatetime);

            $numberPlacesActual = $model->number_places_actual;
            $activeSheet->setCellValue('E' . $i, $numberPlacesActual);

            $kgActual = $model->kg_actual;
            $activeSheet->setCellValue('F' . $i, $kgActual);

            $mcActual = $model->mc_actual;
            $activeSheet->setCellValue('G' . $i, $mcActual);

            $priceInvoiceWithVat = $model->price_invoice_with_vat;
            $activeSheet->setCellValue('H' . $i, $priceInvoiceWithVat);

            $priceInvoiceWithVatTotal += $priceInvoiceWithVat;

            $activeSheet->setCellValue('K' . $i, $model->id); // => L

            if ($routes = $model->getProposalRoutes()->all()) {

                $priceRowEarnedSum = $priceInvoiceWithVat;
                $priceRowExpendSum = 0;

                foreach ($routes as $route) {

                    if ($route->deleted == 1) {
                        continue;
                    }

                    $i++;

                    $activeSheet->setCellValue('A' . $i, Store::getPointTitle($route->route_from));
                    $activeSheet->setCellValue('B' . $i, Store::getPointTitle($route->route_to));

                    $shippedDatetime = '';
                    if (!empty($route->shipped_datetime)) {
                        $shippedDatetime = Yii::$app->formatter->asDate($route->shipped_datetime, 'php:d/m/Y');
                    }

                    $activeSheet->setCellValue('C' . $i, $shippedDatetime);

                    $deliveryDatetime = '';
                    if ($route->delivery_date) {
                        $deliveryDatetime = Yii::$app->formatter->asDate($route->delivery_date, 'php:d/m/Y');
                    }

                    $activeSheet->setCellValue('D' . $i, $deliveryDatetime);
                    $activeSheet->setCellValue('E' . $i, '');
                    $activeSheet->setCellValue('F' . $i, '');
                    $activeSheet->setCellValue('G' . $i, '');

                    $priceRoute = $route->price_invoice;
                    $activeSheet->setCellValue('I' . $i, $priceRoute);

                    $priceRowExpendSum += $priceRoute;
                    $priceRowEarnedSum -= $priceRoute;

                    $activeSheet->setCellValue('K' . $i, $route->id);

                    if ($unforeseenExpenses = $route->getTlDeliveryProposalRouteUnforeseenExpenses()->all()) {
                        foreach ($unforeseenExpenses as $ue) {
                            if ($ue->deleted == 1) {
                                continue;
                            }

                            $i++;
                            $activeSheet->setCellValue('A' . $i, $ue->name);
                            $priceUe = $ue->price_cache;
                            $activeSheet->setCellValue('I' . $i, $priceUe);
                        }
                    }
                }

                ++$i;
                $activeSheet->setCellValue('H' . $i, $priceInvoiceWithVat);
                $activeSheet->setCellValue('I' . $i, $priceRowExpendSum);
                $activeSheet->setCellValue('J' . $i, $priceRowEarnedSum);

                ++$i;

                $priceRowExpendTotal += $priceRowExpendSum;
                $priceRowEarnedTotal += $priceRowEarnedSum;
            }

        }

        ++$i;

        $activeSheet->setCellValue('H' . $i, $priceInvoiceWithVatTotal);
        $activeSheet->setCellValue('I' . $i, $priceRowExpendTotal);
        $activeSheet->setCellValue('J' . $i, $priceRowEarnedTotal);

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('bossDepartment/output/'.$filename);

        if(file_exists('bossDepartment/output/'.$filename)){
            $mailManager = new MailManager;
            $mailManager->sendMailWithAttach('ferze@ua.fm', 'Delivery Proposals: daily report', 'bossDepartment/output/'.$filename);
        }

        Yii::$app->end();
    }

    /* Ищет заявки с указанными статусами и при их наличии
     * отправляет на почту письмо со  списком id => status
     * @param array $statusArray
     * @return mixed
     **/
    public function actionReportProblemStatus ($statusArray = NULL)
    {
        if(is_null($statusArray)){
            $statusArray = [
                TlDeliveryProposal::STATUS_ADD_ROUTE_TO_DP,
                TlDeliveryProposal::STATUS_ADD_CAR_TO_ROUTE,
                TlDeliveryProposal::STATUS_NOT_ADDED_M3,
            ];
        }

        $data = (new Query())
            ->select('id, status')
            ->from(TlDeliveryProposal::tableName())
            ->where([
                'status' => $statusArray,
                'deleted' => TlDeliveryProposal::NOT_SHOW_DELETED,
            ])
            ->orderBy('id ASC')
            ->all();

        if($data){
            $statusArray = TlDeliveryProposal::getStatusArray();
            $mailManager = new MailManager();
            $mailManager->sendProblemProposalMail($data, $statusArray);
        }
        return 0;
    }

    /* Ищет заявки с незаполненными shipped_datetime
     * в связанных записях
     * @param array $statusArray
     * @return mixed
     **/
    public function actionReportEmptyShippedDatetime()
    {
        $data = [];
        $statusArray = [TlDeliveryProposal::STATUS_DELIVERED, TlDeliveryProposal::STATUS_DONE];
        $deliveryProposals = TlDeliveryProposal::find()
            ->where(['in', 'status', $statusArray])
            ->andWhere(['deleted' => TlDeliveryProposal::NOT_SHOW_DELETED])
            ->all();

        foreach ($deliveryProposals as $dp) {
            if ($deliveryRoutes = $dp->proposalRoutes) {
                foreach ($deliveryRoutes as $dr) {
                    if ($deliveryCars = $dr->carItems) {
                        foreach ($deliveryCars as $dc) {
                            if (empty($dc->shipped_datetime) && !empty ($dp->shipped_datetime)) {
                                $data[] = $dp->id;
                            }
                        }
                    }
                }
            }
        }
        if($data){
            $mailManager = new MailManager();
            $mailManager->sendEmptyShippedDatetimeProposalMail($data);
        }
        return 0;
    }

    /**
     * Активировать отложенные цены Kaspi, у которых наступила дата effective_from.
     *
     * php yii cron/kaspi-activate-pending-prices
     *
     */
    public function actionKaspiActivatePendingPrices()
    {
        $priceService = new PriceService();
        $priceService->init();

        $result = $priceService->activatePendingPrices();

        $status    = isset($result['status'])    ? (string) $result['status']    : 'unknown';
        $activated = isset($result['activated']) ? (int)    $result['activated'] : 0;
        $errors    = isset($result['errors'])    ? (int)    $result['errors']    : 0;
        $file      = isset($result['excel_file']) ? (string) $result['excel_file'] : '';

        echo "Kaspi price activation: status={$status}, activated={$activated}, errors={$errors}"
            . ($file !== '' ? ", excel={$file}" : '') . "\n";

        return $errors > 0 ? 1 : 0;
    }

    /**
     * Опросить Kaspi на новые подтверждённые заказы (APPROVED_BY_BANK, state=NEW)
     * и импортировать их в EcommerceOutbound + зарезервировать сток.
     *
     * Окно poll — последние orderPollWindowHours часов (по умолчанию 6).
     * Идемпотентно по external_order_number.
     * Рекомендуемое расписание: каждые 15 минут.
     *
     * php yii cron/kaspi-poll-orders
     */
    public function actionKaspiPollOrders()
    {
        $module = Yii::$app->getModule('kaspi');
        /** @var OrderImportService $service */
        $service = $module !== null ? $module->get('orderImportService') : null;
        if (!$service instanceof OrderImportService) {
            $service = new OrderImportService();
            $service->init();
        }

        $result = $service->pollAndImportNew();

        $fetched   = isset($result['fetched']) ? (int) $result['fetched'] : 0;
        $imported  = isset($result['imported']) ? (int) $result['imported'] : 0;
        $skipped   = isset($result['skipped_existing']) ? (int) $result['skipped_existing'] : 0;
        $noStock   = isset($result['failed_no_stock']) ? (int) $result['failed_no_stock'] : 0;
        $errors    = isset($result['errors']) ? (int) $result['errors'] : 0;
        $status    = isset($result['status']) ? (string) $result['status'] : 'unknown';

        echo "Kaspi order poll: status={$status}, fetched={$fetched}, imported={$imported}, "
            . "skipped_existing={$skipped}, no_stock={$noStock}, errors={$errors}\n";

        return ($status === 'OK' && $errors === 0) ? 0 : 1;
    }

    /**
     * Ретро-импорт одиночного Kaspi-заказа по его orderId.
     *
     * Используется когда штатный poll пропустил заказ (например, заказ уже ушёл
     * из APPROVED_BY_BANK/NEW — Kaspi принял его автоматически по таймауту, либо
     * merchant принял в кабинете). Тянет сам заказ и его entries через Kaspi API,
     * создаёт EcommerceOutbound + items, резервирует сток в ecommerce_stock —
     * **без** повторного вызова acceptOrder (заказ уже принят в Kaspi).
     *
     * php yii cron/kaspi-import-order ODk2ODg0NjEw
     * php yii cron/kaspi-import-order ODk2ODg0NjEw KASPI_DELIVERY
     */
    public function actionKaspiImportOrder($orderId, $markStatus = \stockDepartment\modules\kaspi\enums\OrderStatus::ORDER_ACCEPTED_BY_MERCHANT)
    {
        $orderId = trim((string) $orderId);
        if ($orderId === '') {
            echo "orderId is required\n";
            return 1;
        }

        $module = Yii::$app->getModule('kaspi');
        /** @var \stockDepartment\modules\kaspi\services\KaspiAPIService $api */
        $api = $module !== null ? $module->get('apiService') : null;
        if ($api === null) {
            echo "kaspi module/apiService not available\n";
            return 1;
        }

        $existing = \common\ecommerce\entities\EcommerceOutbound::find()
            ->andWhere(['external_order_number' => $orderId])
            ->andWhere(['deleted' => 0])
            ->one();
        if ($existing !== null) {
            echo "Order {$orderId} already imported: outbound id={$existing->id}\n";
            return 0;
        }

        try {
            $orderDto = $api->getOrderById($orderId);
        } catch (\Exception $e) {
            echo "Kaspi getOrderById failed: " . $e->getMessage() . "\n";
            return 1;
        }
        if ($orderDto === null) {
            echo "Kaspi order not found: {$orderId}\n";
            return 1;
        }

        try {
            $entriesResponse = $api->getOrderEntries($orderId);
        } catch (\Exception $e) {
            echo "Kaspi getOrderEntries failed: " . $e->getMessage() . "\n";
            return 1;
        }

        $entries = [];
        $data = isset($entriesResponse['data']) && is_array($entriesResponse['data']) ? $entriesResponse['data'] : [];
        foreach ($data as $row) {
            $attrs = isset($row['attributes']) && is_array($row['attributes']) ? $row['attributes'] : [];
            $sku = '';
            if (isset($attrs['offer']['code'])) {
                $sku = (string) $attrs['offer']['code'];
            } elseif (isset($attrs['merchantProductCode'])) {
                $sku = (string) $attrs['merchantProductCode'];
            } elseif (isset($attrs['productCode'])) {
                $sku = (string) $attrs['productCode'];
            }
            $qty = isset($attrs['quantity']) ? (int) $attrs['quantity'] : 0;
            if ($sku === '' || $qty <= 0) {
                continue;
            }
            $price = 0.0;
            if (isset($attrs['basePrice'])) {
                $price = (float) $attrs['basePrice'];
            } elseif (isset($attrs['totalPrice']) && $qty > 0) {
                $price = (float) $attrs['totalPrice'] / $qty;
            }
            $entries[] = ['sku' => $sku, 'qty' => $qty, 'price' => $price];
        }

        if (empty($entries)) {
            echo "Order {$orderId} has no entries\n";
            return 1;
        }

        $entries = OrderImportService::resolveArticlesToGuids($entries);

        $clientId = (int) (isset($module->kaspiClientId) ? $module->kaspiClientId : 0);
        $now = time();
        $expectedQty = 0;
        foreach ($entries as $e) {
            $expectedQty += (int) $e['qty'];
        }

        $customerName = '';
        $cellPhone = '';
        if ($orderDto->customer !== null) {
            $customerName = trim(((string) $orderDto->customer->firstName) . ' ' . ((string) $orderDto->customer->lastName));
            $cellPhone = (string) $orderDto->customer->cellPhone;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $outbound = new \common\ecommerce\entities\EcommerceOutbound();
            $outbound->client_id              = $clientId;
            $outbound->order_number           = 'KASPI-' . substr($orderId, 0, 30);
            $outbound->external_order_number  = $orderId;
            $outbound->expected_qty           = $expectedQty;
            $outbound->customer_name          = $customerName;
            $outbound->phone_mobile1          = $cellPhone;
            $outbound->total_price            = (string) (float) $orderDto->totalPrice;
            $outbound->status                 = \common\modules\stock\models\Stock::STATUS_OUTBOUND_NEW;
            $outbound->api_status             = 0;
            $outbound->external_kaspi_status  = (string) $markStatus;
            $outbound->data_created_on_client = $orderDto->creationDate > 0 ? (int) floor($orderDto->creationDate / 1000) : $now;
            $outbound->created_at             = $now;
            $outbound->updated_at             = $now;
            $outbound->deleted                = 0;
            if (!$outbound->save(false)) {
                throw new \RuntimeException('Failed to save EcommerceOutbound');
            }

            foreach ($entries as $e) {
                $item = new \common\ecommerce\entities\EcommerceOutboundItem();
                $item->outbound_id    = (int) $outbound->id;
                $item->product_sku    = (string) $e['sku'];
                $item->product_name   = isset($e['product_name'])  ? (string) $e['product_name']  : '';
                $item->product_brand  = isset($e['product_brand']) ? (string) $e['product_brand'] : '';
                $item->product_color  = isset($e['product_color']) ? (string) $e['product_color'] : '';
                $item->product_model  = isset($e['product_model']) ? (string) $e['product_model'] : '';
                $item->expected_qty   = (int) $e['qty'];
                $item->allocated_qty  = 0;
                $item->accepted_qty   = 0;
                $item->status         = 0;
                $item->product_price  = (string) (float) $e['price'];
                $item->created_at     = $now;
                $item->updated_at     = $now;
                $item->deleted        = 0;
                if (!$item->save(false)) {
                    throw new \RuntimeException('Failed to save EcommerceOutboundItem for sku ' . $e['sku']);
                }
            }

            $missing = [];
            $itemIdBySku = [];
            foreach (\common\ecommerce\entities\EcommerceOutboundItem::find()->andWhere(['outbound_id' => (int) $outbound->id])->all() as $it) {
                $itemIdBySku[(string) $it->product_sku] = (int) $it->id;
            }

            $totalReserved = 0;
            foreach ($entries as $e) {
                $sku    = (string) $e['sku'];
                $needed = (int) $e['qty'];
                if ($sku === '' || $needed <= 0) {
                    continue;
                }
                $ids = \common\ecommerce\entities\EcommerceStock::find()
                    ->select('id')
                    ->andWhere(['product_sku' => $sku])
                    ->andWhere(['status_availability' => \common\ecommerce\entities\EcommerceStock::STATUS_AVAILABILITY_YES])
                    ->andWhere(['deleted' => 0])
                    ->limit($needed)
                    ->column();
                if (count($ids) < $needed) {
                    $missing[] = ['sku' => $sku, 'needed' => $needed, 'available' => count($ids)];
                    continue;
                }

                $itemId = isset($itemIdBySku[$sku]) ? (int) $itemIdBySku[$sku] : 0;

                $updates = [
                    'outbound_id'         => (int) $outbound->id,
                    'outbound_item_id'    => $itemId,
                    'status_availability' => \common\ecommerce\entities\EcommerceStock::STATUS_AVAILABILITY_RESERVED,
                    'status'              => \common\modules\stock\models\Stock::STATUS_OUTBOUND_FULL_RESERVED,
                    'kaspi_order_status'  => (string) $markStatus,
                    'updated_at'          => $now,
                ];
                if (!empty($e['product_name']))  { $updates['product_name']  = (string) $e['product_name']; }
                if (!empty($e['product_brand'])) { $updates['product_brand'] = (string) $e['product_brand']; }
                if (!empty($e['product_color'])) { $updates['product_color'] = (string) $e['product_color']; }
                if (!empty($e['product_model'])) { $updates['product_model'] = (string) $e['product_model']; }

                \common\ecommerce\entities\EcommerceStock::updateAll($updates, ['id' => $ids]);

                $totalReserved += count($ids);

                if ($itemId > 0) {
                    \common\ecommerce\entities\EcommerceOutboundItem::updateAll(
                        [
                            'allocated_qty' => $needed,
                            'status'        => \common\modules\stock\models\Stock::STATUS_OUTBOUND_FULL_RESERVED,
                            'updated_at'    => $now,
                        ],
                        ['id' => $itemId]
                    );
                }
            }

            if (!empty($missing)) {
                $transaction->rollBack();
                echo "No stock for order {$orderId}: " . json_encode($missing, JSON_UNESCAPED_UNICODE) . "\n";
                return 1;
            }

            if ($totalReserved > 0) {
                \common\ecommerce\entities\EcommerceOutbound::updateAll(
                    [
                        'allocated_qty' => $totalReserved,
                        'status'        => \common\modules\stock\models\Stock::STATUS_OUTBOUND_FULL_RESERVED,
                        'updated_at'    => $now,
                    ],
                    ['id' => (int) $outbound->id]
                );
            }

            $transaction->commit();
            echo "Imported Kaspi order {$orderId}: outbound id={$outbound->id}, external_kaspi_status={$markStatus}, items=" . count($entries) . ", reserved_qty={$expectedQty}\n";
            return 0;
        } catch (\Exception $e) {
            $transaction->rollBack();
            echo "Import transaction failed: " . $e->getMessage() . "\n";
            return 1;
        }
    }

    /**
     * Синхронизировать статусы активных Kaspi-заказов: обновить external_kaspi_status,
     * снять резерв на CANCELLING/CANCELLED, пометить COMPLETED как PENDING для 1С.
     * Рекомендуемое расписание: каждые 15 минут.
     *
     * php yii cron/kaspi-sync-order-statuses
     */
    public function actionKaspiSyncOrderStatuses()
    {
        $module = Yii::$app->getModule('kaspi');
        /** @var OrderStatusSyncService $service */
        $service = $module !== null ? $module->get('orderStatusSyncService') : null;
        if (!$service instanceof OrderStatusSyncService) {
            $service = new OrderStatusSyncService();
            $service->init();
        }

        $result = $service->syncActiveOrders();

        $checked   = isset($result['checked']) ? (int) $result['checked'] : 0;
        $cancelled = isset($result['cancelled']) ? (int) $result['cancelled'] : 0;
        $completed = isset($result['completed']) ? (int) $result['completed'] : 0;
        $errors    = isset($result['errors']) ? (int) $result['errors'] : 0;
        $status    = isset($result['status']) ? (string) $result['status'] : 'unknown';

        echo "Kaspi order status sync: status={$status}, checked={$checked}, "
            . "cancelled={$cancelled}, completed={$completed}, errors={$errors}\n";

        return ($status === 'OK' && $errors === 0) ? 0 : 1;
    }

    /**
     * Передать выполненные Kaspi-заказы в 1С (one_c_status=PENDING → SENT/ERROR).
     * Сейчас 1С endpoint — заглушка в Alix1CApiService::postSale.
     * Рекомендуемое расписание: каждые 30 минут.
     *
     * php yii cron/kaspi-sync-completed-to-1c
     */
    public function actionKaspiSyncCompletedTo1c()
    {
        $module = Yii::$app->getModule('kaspi');
        /** @var OneCSalesSyncService $service */
        $service = $module !== null ? $module->get('oneCSalesSyncService') : null;
        if (!$service instanceof OneCSalesSyncService) {
            $service = new OneCSalesSyncService();
            $service->init();
        }

        $result = $service->syncPendingSales();

        $picked = isset($result['picked']) ? (int) $result['picked'] : 0;
        $sent   = isset($result['sent']) ? (int) $result['sent'] : 0;
        $errors = isset($result['errors']) ? (int) $result['errors'] : 0;
        $status = isset($result['status']) ? (string) $result['status'] : 'unknown';

        echo "Kaspi -> 1C sales sync: status={$status}, picked={$picked}, sent={$sent}, errors={$errors}\n";

        return ($status === 'OK' && $errors === 0) ? 0 : 1;
    }

    /**
     * Опросить Kaspi на предмет подтверждённых возвратов и создать Inbound.
     *
     * По диаграмме flow: Kaspi сам инициирует возврат, Nomadex подхватывает.
     * Запускать регулярно (например, раз в 30 минут).
     *
     * php yii cron/kaspi-poll-returns
     */
    public function actionKaspiPollReturns()
    {
        $service = new OrderReturnService();
        $service->init();

        $result = $service->pollKaspiReturnsAndCreateInbounds();

        $fetched = isset($result['fetched']) ? (int) $result['fetched'] : 0;
        $created = isset($result['created']) ? (int) $result['created'] : 0;
        $skipped = isset($result['skipped']) ? (int) $result['skipped'] : 0;
        $status  = isset($result['status']) ? (string) $result['status'] : 'unknown';

        echo "Kaspi return polling: status={$status}, fetched={$fetched}, created={$created}, skipped={$skipped}\n";

        return $status === 'OK' ? 0 : 1;
    }

    /**
     * Синхронизировать номенклатуру из сервиса Alix 1C
     * в product_v2 / product_barcodes_v2.
     *
     * Источник: GET {alix1cBaseUrl}/items (Basic Auth). Запуск — раз в 30 минут.
     *
     * php yii cron/alix-sync-items
     */
    public function actionAlixSyncItems()
    {
        $api = new Alix1CApiService();
        $api->init();

        $service = new ProductSyncService(['api' => $api]);
        $service->init();

        $result = $service->syncFromApi();

        $fetched       = isset($result['fetched']) ? (int) $result['fetched'] : 0;
        $created       = isset($result['created']) ? (int) $result['created'] : 0;
        $updated       = isset($result['updated']) ? (int) $result['updated'] : 0;
        $barcodesAdded = isset($result['barcodes_added']) ? (int) $result['barcodes_added'] : 0;
        $errors        = isset($result['errors']) ? (int) $result['errors'] : 0;
        $status        = isset($result['status']) ? (string) $result['status'] : 'UNKNOWN';

        echo "Alix 1C items sync: status={$status}, fetched={$fetched}, "
            . "created={$created}, updated={$updated}, "
            . "barcodes_added={$barcodesAdded}, errors={$errors}\n";

        if (!empty($result['message'])) {
            echo "message: {$result['message']}\n";
        }

        return $status === 'OK' ? 0 : 1;
    }
}