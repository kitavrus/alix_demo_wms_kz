<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 12.01.2015
 * Time: 10:54
 */
namespace console\controllers;
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
}