<?php

namespace app\modules\tms\controllers;

//use common\components\DeliveryProposalManager;
use common\modules\transportLogistics\models\TlAgents;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteUnforeseenExpenses;
//use common\modules\transportLogistics\models\TlDeliveryRoutes;
use Yii;
use stockDepartment\modules\tms\models\TlDeliveryProposalRouteCarsSearch;
use stockDepartment\modules\tms\models\TlDeliveryProposalRouteCarsSearchExport;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteCars;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteTransport;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\store\models\Store;
use yii\helpers\ArrayHelper;
//use yii\helpers\VarDumper;
use stockDepartment\components\Controller;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
//use yii\filters\VerbFilter;

/**
 * TlDeliveryProposalRouteCarsController implements the CRUD actions for TlDeliveryProposalRouteCars model.
 */
class TlDeliveryProposalRouteCarsController extends Controller
{
//    public function behaviors()
//    {
//        return [
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'delete' => ['post'],
//                ],
//            ],
//        ];
//    }

    /**
     * Lists all TlDeliveryProposalRouteCars models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TlDeliveryProposalRouteCarsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TlDeliveryProposalRouteCars model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TlDeliveryProposalRouteCars model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TlDeliveryProposalRouteCars();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TlDeliveryProposalRouteCars model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing TlDeliveryProposalRouteCars model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }

    /**
     * Finds the TlDeliveryProposalRouteCars model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TlDeliveryProposalRouteCars the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TlDeliveryProposalRouteCars::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /*
     * return information about content on car
     * */
    public function actionGetRouteCarInfo()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);

        return $this->renderAjax('_after-row-search-by-driver-auto-number',['model'=>$model]);
    }

//    /*
//     * Print TTN
//     *
//     * */
//    public function actionPrintTtn()
//    {
//        $id = Yii::$app->request->get('id');
//        $model = $this->findModel($id);
//
//        if ($r = $model->getRoutes()->all()) {
//            foreach ($r as $rItem) {
//                if ($dp = TlDeliveryProposal::findOne($rItem->tl_delivery_proposal_id) ) {
//
//
////                    VarDumper::dump(date('Y-m-d H:i:s',time()),10,true);
//
//                    $dp->shipped_datetime = Yii::$app->formatter->asDateTime(time(),'php:Y-m-d H:i:s');// $dp->getAttribute('delivery_datetime');
////                    $dp->shipped_datetime = date('Y-m-d H:i:s',time());// $dp->getAttribute('delivery_datetime');
//                    $dp->status = TlDeliveryProposal::STATUS_ON_ROUTE;
//                    $dp->save();
//
////                    $dp->recalculateExpensesOrder();
////                    $dp->setCascadedStatus();
//                    $dpManager = new DeliveryProposalManager(['id' => $dp]);
//                    $dpManager->onUpdateProposal();
//                }
//            }
//        }
//
//        return $this->render('print-ttn-pdf',['model'=>$model]);
//    }

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
            ->setTitle('report-' . date('d.m.Y'));


        $searchModel = new TlDeliveryProposalRouteCarsSearchExport();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dps = $dataProvider->getModels();
        $agent = TlAgents::findOne(['id'=>$searchModel->agent_id]);


        $headerFontSize = 15;
        $headerRowHeight = $headerFontSize+3;
        $activeSheet->setCellValue('A1', '');
        $activeSheet->setCellValue('A2', 'Приложения к акту',true)->getStyle()->getFont()->setSize($headerFontSize)->setBold(true);
        $activeSheet->getRowDimension(2)->setRowHeight($headerRowHeight);
        $activeSheet->getCell('A2')->getStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $activeSheet->setCellValue('A3', '');
        $activeSheet->getRowDimension(4)->setRowHeight($headerRowHeight);
        $activeSheet->setCellValue('A4', 'Заказчик:',true)->getStyle()->getFont()->setSize($headerFontSize)->setBold(true);
        $activeSheet->setCellValue('B4', 'ТОО "Nomadex 3PL"',true)->getStyle()->getFont()->setSize($headerFontSize)->setBold(true);
        $activeSheet->getRowDimension(5)->setRowHeight($headerRowHeight);
        $activeSheet->setCellValue('A5', 'Перевозчик:',true)->getStyle()->getFont()->setSize($headerFontSize)->setBold(true);
        $activeSheet->setCellValue('B5', isset($agent) ? $agent->name : '',true)->getStyle()->getFont()->setSize($headerFontSize)->setBold(true);
        $activeSheet->setCellValue('A6', '');

        $activeSheet->setCellValue('B2', date('d.m.Y'),true)->getStyle()->getFont()->setSize($headerFontSize)->setBold(true);

        $i = 7;
        $activeSheet->setCellValue('A' . $i, 'Из');
        $activeSheet->setCellValue('B' . $i, 'В');
        $activeSheet->setCellValue('C' . $i, 'Дата отгрузки');
        $activeSheet->setCellValue('D' . $i, 'Клиент');
        $activeSheet->setCellValue('E' . $i, 'Кол-во мест'); // E
        $activeSheet->setCellValue('F' . $i, 'Кол-во кг'); // F
        $activeSheet->setCellValue('G' . $i, 'Кол-во М3'); // G
        $activeSheet->setCellValue('H' . $i, 'Стоимость'); // H
        $activeSheet->setCellValue('I' . $i, 'ИД'); // I
        $activeSheet->setCellValue('J' . $i, 'Тип оплаты'); // I



        $priceInvoiceSum = 0;
        $i += 1;


        $q = Store::find();

        $value = ArrayHelper::map($q->with('city', 'client')->all(), 'id', function ($m) {
            return $m->city->name . ' ' . '/ ' . ' ' . ($m->type_use != Store::TYPE_USE_STORE ? (!empty($m->legal_point_name) ? $m->legal_point_name : $m->name) : $m->name) . (!empty($m->shop_code) ? ' ' . $m->shop_code : '') . ((!empty($m->shopping_center_name) && $m->shopping_center_name != '-') ? ' ' . $m->shopping_center_name : '');
        });
//        $x = 2411930;
        foreach ($dps as $model) {
            $ik = 0;

            if ($r = $model->getRoutes()->all()) {

            $priceInvoice = $model->price_invoice;

                $iNumberPlacesActual = 0;
                $iMcActual = 0;
                $iKgActual = 0;
                foreach ($r as $rItem) {

                    if($rItem->deleted == 1) {
                        continue;
                    }

                    if($dp = TlDeliveryProposal::findOne($rItem->tl_delivery_proposal_id)) {

                        $i += 1;
                        $ik += 1;

                        $activeSheet->setCellValue('I' . $i, $dp->id);
                        $activeSheet->setCellValue('J' . $i, $model->getPaymentMethodValue());

//                        $modelDpRouteCar = TlDeliveryProposalRouteTransport::findOne(['tl_delivery_proposal_route_cars_id' => $model->id, 'tl_delivery_proposal_route_id' => $rItem->id]);

                        $number_places = (!empty($dp->number_places_actual) ? $dp->number_places_actual : '0');// . ' ' . Yii::t('titles', 'Кол-во мест') . "<br />";
                        $mc_actual = (!empty($dp->mc_actual) ? $dp->mc_actual : '0');// . ' ' . Yii::t('titles', 'М3') . "<br />";
                        $kg_actual = (!empty($dp->kg_actual) ? $dp->kg_actual : '0');// . ' ' . Yii::t('titles', 'Кг') . "<br />";

                        $from = isset ($value[$rItem->route_from]) ? $value[$rItem->route_from] : '-NONE-';
                        $activeSheet->setCellValue('A' . $i, $from);

                        $to = isset ($value[$rItem->route_to]) ? $value[$rItem->route_to] : '-NONE-';
                        $activeSheet->setCellValue('B' . $i, $to);

                        $clientTitle = '';
                        if($rClient =  $rItem->client) {
                            $clientTitle = $rClient->title;
                        }
                        $activeSheet->setCellValue('D' . $i, $clientTitle);

                        $numberPlacesActual = $number_places;
                        $activeSheet->setCellValue('E' . $i, $numberPlacesActual);

                        $kgActual = $kg_actual ;
                        $activeSheet->setCellValue('F' . $i, $kgActual);

                        $mcActual = $mc_actual;
                        $activeSheet->setCellValue('G' . $i, $mcActual);

                        $shippedDatetime = '';
                        if (!empty($model->shipped_datetime)) {
                            $shippedDatetime = Yii::$app->formatter->asDate($model->shipped_datetime, 'php:d/m/Y');
                        }
                        $activeSheet->setCellValue('C' . $i, $shippedDatetime);
//
                        $activeSheet->setCellValue('H' . $i, '0');


                        $iNumberPlacesActual += $numberPlacesActual;
                        $iMcActual += $kgActual;
                        $iKgActual += $mcActual;

                    } else {
                    }
                }

                if ($dp) {

                    //$activeSheet->setCellValue('A' . ($i - $ik), $model->routeCityFrom->name);
                    $activeSheet->setCellValue('A' . ($i - $ik), $dp->routeFrom->city->name);
                    //$activeSheet->setCellValue('B' . ($i - $ik), $model->routeCityTo->name);
                    $activeSheet->setCellValue('B' . ($i - $ik),  $dp->routeTo->city->name);

                    $shippedDatetime = '';

                    if (!empty($dp->shipped_datetime)) {
                        $shippedDatetime = Yii::$app->formatter->asDate($dp->shipped_datetime, 'php:d/m/Y');
                    }
                    $activeSheet->setCellValue('C' . ($i - $ik), $shippedDatetime);

                    $activeSheet->setCellValue('E' . ($i - $ik), $iNumberPlacesActual);
                    $activeSheet->setCellValue('F' . ($i - $ik), $iMcActual);
                    $activeSheet->setCellValue('G' . ($i - $ik), $iKgActual);

                    $e = 0;
                    if ($unforeseenExpenses = $rItem->getTlDeliveryProposalRouteUnforeseenExpenses()->all()) {
                        foreach ($unforeseenExpenses as $ue) {
                            if($ue->deleted == 1 || $ue->who_pays == TlDeliveryProposalRouteUnforeseenExpenses::WHO_PAY_WE ) {
                                continue;
                            }

                            $i++;
                            $e++;
                            $activeSheet->setCellValue('A' . $i, $ue->name);
                            $activeSheet->setCellValue('H' . $i, $ue->price_cache,true)->getStyle()->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                            $activeSheet->setCellValue('E' . $i, $iNumberPlacesActual);
                            $activeSheet->setCellValue('F' . $i, $iMcActual);
                            $activeSheet->setCellValue('G' . $i, $iKgActual);

                            $priceInvoiceSum += $ue->price_cache;
                        }
                    }

                    $activeSheet->setCellValue('H' . ($i - $ik-$e), $priceInvoice,true)->getStyle()->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                    $priceInvoiceSum += $priceInvoice;
                } else {
                }

                $i++;
                $i++;

            } else {

            }
        }

        $row = $i;
        $row += 1;
        $fontHeight = 13;
        $rowHeight = $fontHeight+3;
        $activeSheet->setCellValue('H' . $row, $priceInvoiceSum);
        $activeSheet->getCell('H' . $row)->getStyle()->getFont()->setSize(13)->setBold(true);
        $activeSheet->getCell('H' . $row)->getStyle()->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $activeSheet->setCellValue('E' . $row, "ИТОГО");
        $activeSheet->mergeCells('E'.$row.':G'.$row);
        $activeSheet->getCell('E' . $row)->getStyle()->getFont()->setSize(13)->setBold(true);
        $activeSheet->getCell('E' . $row)->getStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $activeSheet->getRowDimension($row)->setRowHeight($rowHeight);

        $row += 2;
        $activeSheet->setCellValue('B' . $row, "Перевозчик (сдал) ____________________________");
        $activeSheet->getCell('B' . $row)->getStyle()->getFont()->setBold(true);
        $activeSheet->setCellValue('C' . $row, "_________________________");
        $activeSheet->mergeCells('C'.$row.':D'.$row);
        $activeSheet->getCell('C' . $row)->getStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $row += 1;
        $activeSheet->setCellValue('B' . $row, "(подпись)");
        $activeSheet->getCell('B' . $row)->getStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $activeSheet->setCellValue('C' . $row, "(Ф.И.О.)");
        $activeSheet->mergeCells('C'.$row.':D'.$row);
        $activeSheet->getCell('C' . $row)->getStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $activeSheet->setCellValue('F' . $row, "М.П.");
        $activeSheet->getCell('F' . $row)->getStyle()->getFont()->setBold(true);


        $row += 3;
        $activeSheet->setCellValue('B' . $row, "Заказчик (принял) ____________________________");
        $activeSheet->getCell('B' . $row)->getStyle()->getFont()->setBold(true);
        $activeSheet->setCellValue('C' . $row, "_________________________");
        $activeSheet->mergeCells('C'.$row.':D'.$row);
        $activeSheet->getCell('C' . $row)->getStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $row += 1;
        $activeSheet->setCellValue('B' . $row, "(подпись)");
        $activeSheet->getCell('B' . $row)->getStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $activeSheet->setCellValue('C' . $row, "(Ф.И.О.)");
        $activeSheet->mergeCells('C'.$row.':D'.$row);
        $activeSheet->getCell('C' . $row)->getStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $activeSheet->setCellValue('F' . $row, "М.П.");
        $activeSheet->getCell('F' . $row)->getStyle()->getFont()->setBold(true);



        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . time() . '-0.xlsx"');
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
    public function actionExportToExcelOLD1()
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
            ->setTitle('report-' . date('d.m.Y'));

        $activeSheet->setCellValue('A1', '');
        $activeSheet->setCellValue('A2', 'Приложения к акту');
        $activeSheet->setCellValue('A3', '');
        $activeSheet->setCellValue('A4', 'Клиент');
        $activeSheet->setCellValue('A5', '');
        $activeSheet->setCellValue('A6', '');

        $activeSheet->setCellValue('H1', date('d/m/Y'));

        $i = 7;
        $activeSheet->setCellValue('A' . $i, 'Из');
        $activeSheet->setCellValue('B' . $i, 'В');
        $activeSheet->setCellValue('C' . $i, 'Дата отгрузки');
        $activeSheet->setCellValue('D' . $i, 'Клиент');
        $activeSheet->setCellValue('E' . $i, 'Кол-во мест'); // E
        $activeSheet->setCellValue('F' . $i, 'Кол-во кг'); // F
        $activeSheet->setCellValue('G' . $i, 'Кол-во М3'); // G
        $activeSheet->setCellValue('H' . $i, 'Стоимость'); // H
        $activeSheet->setCellValue('I' . $i, 'ИД'); // I

        $searchModel = new TlDeliveryProposalRouteCarsSearchExport();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//        $dataProviderExport = $searchModel->searchExport(Yii::$app->request->queryParams);
        $dps = $dataProvider->getModels();

        $priceInvoiceSum = 0;
        $i += 1;


//        VarDumper::dump($dataProviderExport->agent_id,10,true);
//        die;


        $q = Store::find();
//        $qSub = clone $q;

        $value = ArrayHelper::map($q->with('city', 'client')->all(), 'id', function ($m) {
            return $m->city->name . ' ' . '/ ' . ' ' . ($m->type_use != Store::TYPE_USE_STORE ? (!empty($m->legal_point_name) ? $m->legal_point_name : $m->name) : $m->name) . (!empty($m->shop_code) ? ' ' . $m->shop_code : '') . ((!empty($m->shopping_center_name) && $m->shopping_center_name != '-') ? ' ' . $m->shopping_center_name : '');
//            return $m->city->name . ' ' . '/' . ' ' . ($m->type_use != Store::TYPE_USE_STORE ? $m->legal_point_name : '') . (!empty($m->shop_code) ? ' ' . $m->shop_code : '') . ((!empty($m->shopping_center_name) && $m->shopping_center_name != '-') ? ' ' . 'ТЦ ' . $m->shopping_center_name : '');
        });

//        $qSub->andFilterWhere(['id' => [4]]); // Наш склад Склад DC
//        $value += ArrayHelper::map($qSub->with('city', 'client')->all(), 'id', function ($m) {
//            return $m->city->name . ' ' . '/' . ' ' . $m->name . (!empty($m->shop_code) ? ' ' . $m->shop_code : '') . ((!empty($m->shopping_center_name) && $m->shopping_center_name != '-') ? ' ' . 'ТЦ ' . $m->shopping_center_name : '');
//        });

        foreach ($dps as $model) {

//            $ik = 0;
            if ($r = $model->getRoutes()->all()) {
                foreach ($r as $rItem) {
//                    if($dpr = TlDeliveryRoutes::findOne($rItem->id)) {
//                        if($dp = TlDeliveryProposal::findOne($dpr->tl_delivery_proposal_id)) {
                        if($dp = TlDeliveryProposal::findOne($rItem->tl_delivery_proposal_id)) {

                            $i += 1;
//                            $ik += 1;

                            $activeSheet->setCellValue('I' . $i, $dp->id);

                            $modelDpRouteCar = TlDeliveryProposalRouteTransport::findOne(['tl_delivery_proposal_route_cars_id' => $model->id, 'tl_delivery_proposal_route_id' => $rItem->id]);

                            $number_places = (!empty($modelDpRouteCar->number_places_actual) ? $modelDpRouteCar->number_places_actual : '0');// . ' ' . Yii::t('titles', 'Кол-во мест') . "<br />";
                            $mc_actual = (!empty($modelDpRouteCar->mc_actual) ? $modelDpRouteCar->mc_actual : '0');// . ' ' . Yii::t('titles', 'М3') . "<br />";
                            $kg_actual = (!empty($modelDpRouteCar->kg_actual) ? $modelDpRouteCar->kg_actual : '0');// . ' ' . Yii::t('titles', 'Кг') . "<br />";

                            $from = isset ($value[$rItem->routeFrom->id]) ? $value[$rItem->routeFrom->id] : $rItem->routeFrom->name;
                            $activeSheet->setCellValue('A' . $i, $from);

                            $to = isset ($value[$rItem->routeTo->id]) ? $value[$rItem->routeTo->id] : $rItem->routeTo->name;
                            $activeSheet->setCellValue('B' . $i, $to);

                            $clientTitle = '';
                            if($rClient =  $rItem->client) {
                                $clientTitle = $rClient->title;
                            }
                            $activeSheet->setCellValue('D' . $i, $clientTitle);
//                            $activeSheet->setCellValue('D' . $i, $rItem->client->title);

                            $numberPlacesActual = $number_places;
                            $activeSheet->setCellValue('E' . $i, $numberPlacesActual);

                            $kgActual = $kg_actual ;
                            $activeSheet->setCellValue('F' . $i, $kgActual);

                            $mcActual = $mc_actual;
                            $activeSheet->setCellValue('G' . $i, $mcActual);

                            $shippedDatetime = '';
                            if (!empty($model->shipped_datetime)) {
                                $shippedDatetime = Yii::$app->formatter->asDate($model->shipped_datetime, 'php:d/m/Y');
                            }
                            $activeSheet->setCellValue('C' . $i, $shippedDatetime);
//
                            $priceInvoice = $model->price_invoice;
                            $activeSheet->setCellValue('H' . $i, $priceInvoice);

                            $priceInvoiceSum += $priceInvoice;

                        }
//                    }
                }

//                if ($ik) {
//
//                    $activeSheet->setCellValue('A' . ($i - $ik), $model->routeCityFrom->name);
//                    $activeSheet->setCellValue('B' . ($i - $ik), $model->routeCityTo->name);
//
//                    $shippedDatetime = '';
//                    if (!empty($model->shipped_datetime)) {
//                        $shippedDatetime = Yii::$app->formatter->asDate($model->shipped_datetime, 'php:d/m/Y');
//                    }
//                    $activeSheet->setCellValue('C' . ($i - $ik), $shippedDatetime);
//
//                    $priceInvoice = $model->price_invoice;
//                    $activeSheet->setCellValue('H' . ($i - $ik), $priceInvoice);
//
//                    $priceInvoiceSum += $priceInvoice;
//                }

//                    $shippedDatetime = '';
//                    if (!empty($model->shipped_datetime)) {
//                        $shippedDatetime = Yii::$app->formatter->asDate($model->shipped_datetime, 'php:d/m/Y');
//                    }
//                    $activeSheet->setCellValue('C' . $i, $shippedDatetime);
//
//                    $priceInvoice = $model->price_invoice;
//                    $activeSheet->setCellValue('H' . $i, $priceInvoice);
//
//                    $priceInvoiceSum += $priceInvoice;

//                $i++;

            } else {}
//            $i++;
        }

        $activeSheet->setCellValue('H' . ($i + 1), $priceInvoiceSum);


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

    /*
   * Popup form for mass update.
   *
   * */
    public function actionMassUpdatePopup()
    {
        $model = new TlDeliveryProposalRouteCars();

        $model->setScenario('mass-update');

        if ( $model->load(Yii::$app->request->post()) ) {

            $dpIds = Yii::$app->request->post('ids');

            if(!empty($dpIds)) {
                $dpIds = explode(',',$dpIds);
            }

            $errors = [];

            if(is_array($dpIds)) {

                $update = [];

                if(!empty($model->cash_no)) {
                    $update['cash_no'] = $model->cash_no;
                }

                if(!empty($model->status_invoice)) {
                    $update['status_invoice'] = $model->status_invoice;
                }

                if(!empty($model->status)) {
                    $update['status'] = $model->status;
                }

//                VarDumper::dump($update,10,true);
//                die;

                if(!empty($update)) {
                    foreach($dpIds as $dpID) {

//                        $pd = new TlDeliveryProposal();

                        $m = TlDeliveryProposalRouteCars::findOne($dpID);
                        $m->setScenario('mass-update');
                        $m->setAttributes($update);
//                        $m->validate();

//                        VarDumper::dump($m->delivery_date,10,true);
//                        echo "<br />";
//                        VarDumper::dump($m->getErrors(),10,true);
//                        echo "<br />";

                        if(!$m->validate()) {
                            $errors[] = [
                                'id' => $m->id,
                                'errors' => $m->getErrors()
                            ];
                        } else {
                            $m->save();
                        }
                    }
//                    $pd->updateAll($update, ['id' => $dpIds]);
//                    $pd->updateAll($update, ['id' => $dpIds]);
//                    VarDumper::dump($m->getErrors(),10,true);
//                    die;
                }

            } else {
//                VarDumper::dump($model->getErrors(),10,true);
//                die;
            }
//
//            Yii::$app->response->format = 'html';
            Yii::$app->response->format = 'json';
//            return  '';
            return [
                'message' => 'Success',
                'errors' => $errors,
            ];
        } else {
//            VarDumper::dump($model->getErrors(),10,true);
//            die;
        }

        return $this->renderAjax('_mass-update-popup', [
            'model' => $model,
        ]);
    }
}
