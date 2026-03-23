<?php

namespace app\modules\warehouseDistribution\controllers\tupperware;

use app\modules\warehouseDistribution\models\tupperware\TlDeliveryProposalSearchExport;
use common\components\MailManager;
use common\helpers\DateHelper;
use common\modules\client\models\ClientEmployees;
use common\modules\codebook\models\Codebook;
use common\modules\store\models\StoreReviews;
use Yii;
use yii\base\Event;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseFileHelper;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use clientDepartment\components\Controller;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use app\modules\warehouseDistribution\models\tupperware\TlDeliveryProposalSearch;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteCars;
use common\modules\transportLogistics\models\TlDeliveryRoutes;
use common\modules\transportLogistics\components\TLHelper;
use common\modules\store\models\Store;
use common\modules\transportLogistics\models\TlCars;
use common\modules\user\models\User;
use common\modules\client\models\Client;
use clientDepartment\modules\client\components\ClientManager;
use common\components\DeliveryProposalManager;



/**
 * TlDeliveryProposalController implements the CRUD actions for TlDeliveryProposal model.
 */
class TlDeliveryProposalController extends Controller
{


    /**
     * Lists all TlDeliveryProposal models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TlDeliveryProposalSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $client = ClientManager::getClientEmployeeByAuthUser();
        $filterWidgetOptionDataRoute= TLHelper::getStoreArrayByClientID($client->client_id);

        return $this->render($this->getViewByType('index'), [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'filterWidgetOptionDataRoute' => $filterWidgetOptionDataRoute,
        ]);
    }

    /**
     * Displays a single TlDeliveryProposal model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if(!ClientManager::canViewDeliveryProposal($model) ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        $dataProviderProposalOrders = new ActiveDataProvider([
            'query' => $model->getProposalOrders(),
        ]);

        $storeReviewButton1 = '';
        if(ClientManager::canUpdateStoreReview($model)) {
            $storeReviewButton1 = '{update}';
        }




        return $this->render($this->getViewByType('view'), [
            'model' => $model,
            'dataProviderProposalOrders' => $dataProviderProposalOrders,
            'storeReviewButton1' => $storeReviewButton1,
        ]);
    }

    /**
     * Creates a new TlDeliveryProposal model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TlDeliveryProposal(['scenario'=>'create-on-client']);
        $ce = ClientManager::getClientEmployeeByAuthUser();

        $model->route_from = $ce->store_id;


        if ($model->load(Yii::$app->request->post())) {

            $model->source = $model::SOURCE_CLIENT;

            $model->client_id = ClientManager::getClientEmployeeByAuthUser()->client_id;
            $model->status = TlDeliveryProposal::STATUS_NEW;
            $model->cash_no = TlDeliveryProposal::METHOD_CHAR;
//            $model->delivery_type = TlDeliveryProposal::DELIVERY_TYPE_TRANSFER_MORE_ROUTE;
            $model->delivery_type = TlDeliveryProposal::DELIVERY_TYPE_ONE_ROUTE;
            if ($model->save()) {
                $dpManager = new DeliveryProposalManager(['id'=>$model->id]);
                $dpManager->onCreateProposal();

                $bm = new MailManager();
                $bm->sendMailToStockIfClientCreateNewDP($model);

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        $client = ClientManager::getClientEmployeeByAuthUser();
        $storeArray= TLHelper::getStoreArrayByClientID($client->client_id,true);

        return $this->render($this->getViewByType('create'), [
            'model' => $model,
            'storeArray' => $storeArray,
        ]);
    }

    /**
     * Updates an existing TlDeliveryProposal model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $model->setScenario('create-on-client');

        if(!ClientManager::canUpdateDeliveryProposal($model) ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $dpManager = new DeliveryProposalManager(['id'=>$model->id]);
            $dpManager->onCreateProposal();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {

            $client = ClientManager::getClientEmployeeByAuthUser();
            $storeArray= TLHelper::getStoreArrayByClientID($client->client_id,true);

            return $this->render($this->getViewByType('update'), [
                'model' => $model,
                'storeArray' => $storeArray,
            ]);
        }
    }

    /**
     * Deletes an existing TlDeliveryProposal model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if(!ClientManager::canDeleteDeliveryProposal($model) ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        if($model) {
            $model->deleted = 1;
            $model->save(false);
        }


        return $this->redirect(['index']);
    }

    /**
     * Finds the TlDeliveryProposal model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TlDeliveryProposal the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TlDeliveryProposal::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    /*
     * The client confirms the correctness of the data entered by the Operator at the warehouse
     * */
    public function actionIsClientConfirm($id)
    {
//        $id = Yii::$app->request->post('id');
        $model = $this->findModel($id);
        $model->is_client_confirmed = $model::IS_CLIENT_CONFIRMED_YES;
        if ($model->save()) {
            $model->refresh();
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    /*
     * Print TTN
     *
     * */
    public function actionPrintTtn()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);

        $ceID = '';
        $userName = '';
//        if(!Yii::$app->user->isGuest) {
//            if($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {
                if($userClient = ClientManager::getClientEmployeeByAuthUser()) {
                    $ceID = $userClient->store_id;
                    $userName = $userClient->last_name.' '.$userClient->first_name;
                }
//            }
//        }

//        $storeTo = $model->routeTo;
        $storeFrom = $model->routeFrom;

        $managersNamesTo = 'Контакты получателей:<br />';
        if($routeTo = $model->routeTo) {
            // находим всех директоров магазина и отправляем им имейлы
            $clientEmployees = ClientEmployees::find()
                ->where([
                    'deleted'=>0,
                    'client_id'=>$model->client_id,
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

//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        VarDumper::dump($storeTo,10,true);
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        VarDumper::dump($userModel,10,true);



        // если отправляем груз со склада из магазина, то печатаем 3 копии файла ТТН
        // 4 = DC - это наш склад
//        $to = 1;
        if(in_array($storeFrom->id,[$ceID])) {
            //Yii::$app->formatter->timeZone = 'Asia/Almaty';
//            $to = 3;
            $model->shipped_datetime = DateHelper::getTimestamp();
            $model->status = TlDeliveryProposal::STATUS_ON_ROUTE;
            //Yii::$app->formatter->timeZone = '';
            $model->save(false);
        }

        // Если груз получаем, по печатаем 1 экземпляр ТТН
//        if(in_array($storeTo->id,[$ceID])) {
//            $to = 1;
//            $model->delivery_date = Yii::$app->formatter->asDateTime(time(),'php:Y-m-d H:i:s');
//            $model->status = TlDeliveryProposal::STATUS_DELIVERED;
//        }



        $dpManager = new DeliveryProposalManager(['id' =>$model->id]);
        $dpManager->onPrintTtn();

        return $this->render('print-ttn-pdf',['model'=>$model,'userName'=>$userName,'managersNamesTo'=>$managersNamesTo]);
//        return $this->render('print-ttn-pdf',['model'=>$model,'to'=>$to,'userName'=>$userName]);
    }

    /*
     * Print box label
     *
     * */
    public function actionPrintBoxLabel()
    {
        $id = Yii::$app->request->get('id');
        $type = Yii::$app->request->get('type');

        $model = $this->findModel($id);
//
        $codeBookModel = Codebook::findOne(['base_type'=>Codebook::BASE_TYPE_BOX]); // Короб

//        VarDumper::dump($codeBookModel,10,true);
//        die('----STOP----');

        $view = ($type == 1 ? 'print-box-label-a4-pdf' : 'print-box-label-self-adhesive-pdf' );

        return $this->render($view,['model'=>$model,'codeBookModel'=>$codeBookModel]);
    }

    /*
   *
   * Export data to EXEL
   *
   * */
    public function actionExportToExcel()
    {
        if(Yii::$app->language == 'tr') {
            Yii::$app->language = 'en';
        }

        $searchModel = new TlDeliveryProposalSearchExport();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dps = $dataProvider->getModels();

        $checkModel = isset($dps[0]) ? $dps[0] : '';
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

        $activeSheet->setCellValue('A1', '');
        $activeSheet->setCellValue('A2', Yii::t('forms', 'Application to the act'));
        $activeSheet->setCellValue('A3', '');
        $activeSheet->setCellValue('A4', Yii::t('forms', 'Client'));
//        $activeSheet->setCellValue('B4', '');
        $activeSheet->setCellValue('A5', '');
        $activeSheet->setCellValue('A6', '');
        $activeSheet->setCellValue('H1', date('d/m/Y'));

        $i = 7;
        $activeSheet->setCellValue('A'.$i, Yii::t('transportLogistics/forms', 'Route From'));
        $activeSheet->setCellValue('B'.$i, Yii::t('transportLogistics/forms', 'Route To'));
        $activeSheet->setCellValue('C'.$i, Yii::t('transportLogistics/forms', 'Shipped date'));
        $activeSheet->setCellValue('D'.$i, Yii::t('transportLogistics/forms', 'Delivery Date'));
        $activeSheet->setCellValue('E'.$i, Yii::t('transportLogistics/forms', 'Number Places Actual'));

        if($checkModel){
            if(ClientManager::canViewAttribute($checkModel)){
                $activeSheet->setCellValue('F'.$i, Yii::t('transportLogistics/forms', 'Kg Actual'));
            } else {
                $activeSheet->setCellValue('F'.$i, '-');
            }
        }

        $activeSheet->setCellValue('G'.$i, Yii::t('transportLogistics/forms', 'Mc Actual'));
        $activeSheet->setCellValue('H'.$i, Yii::t('transportLogistics/forms', 'Price Invoice With Vat'));
        $activeSheet->setCellValue('I'.$i, Yii::t('transportLogistics/forms', 'ID'));

        $searchModel = new TlDeliveryProposalSearchExport();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dps = $dataProvider->getModels();


        $priceInvoiceWithVatSum = 0;

        foreach($dps as $model) {
            $i++;

            $value = TLHelper::getStoreArrayByClientID($model->client_id);


            $from =  isset ($value[$model->routeFrom->id]) ? $value[$model->routeFrom->id] : '-EMPTY-';
            $activeSheet->setCellValue('A' . $i, $from);

            $to =  isset ($value[$model->routeTo->id]) ? $value[$model->routeTo->id] : '-EMPTY-';
            $activeSheet->setCellValue('B' . $i, $to);

            $shippedDatetime = '';
            if(!empty($model->shipped_datetime)) {
                $shippedDatetime = Yii::$app->formatter->asDate($model->shipped_datetime,'php:d/m/Y');
            }
            $activeSheet->setCellValue('C' . $i, $shippedDatetime);

            $deliveryDatetime = '';
            if($model->delivery_date) {
                $deliveryDatetime = Yii::$app->formatter->asDate($model->delivery_date,'php:d/m/Y');
            }
            $activeSheet->setCellValue('D' . $i, $deliveryDatetime);

            $numberPlacesActual = $model->number_places_actual;
            $activeSheet->setCellValue('E' . $i, $numberPlacesActual);

            $kgActual = $model->kg_actual;
            if($checkModel){
                if(ClientManager::canViewAttribute($checkModel)){
                    $activeSheet->setCellValue('F' . $i, $kgActual);
                } else {
                    $activeSheet->setCellValue('F' . $i, '-');
                }
            }


            $mcActual = $model->mc_actual;
            $activeSheet->setCellValue('G' . $i, $mcActual);

            $priceInvoiceWithVat = $model->price_invoice_with_vat;
            $activeSheet->setCellValue('H' . $i, $priceInvoiceWithVat);

            $priceInvoiceWithVatSum += $priceInvoiceWithVat;

            $activeSheet->setCellValue('I' . $i, $model->id);

        }

        $clientTitle = '';
        if($rClient =  $model->client) {
            $clientTitle = $rClient->legal_company_name;
        }

        $activeSheet->setCellValue('B4', $clientTitle);
//        $activeSheet->setCellValue('B4', $model->client->legal_company_name);
        $activeSheet->setCellValue('H' . ($i+1), $priceInvoiceWithVatSum);



        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $dirPath = 'uploads/DeFacto/delivery-proposal/export/'.date('Ymd').'/'.date('His');
        BaseFileHelper::createDirectory($dirPath);
        $fileName = 'delivery-proposal-export-'.time().'.xlsx';
        $fullPath = $dirPath.'/'.$fileName;
        $objWriter->save($fullPath);
        return Yii::$app->response->sendFile($fullPath,$fileName);
    }

    /*
     *
     *
     * */
    public function actionShowStoreReviewForm()
    {
        $storeRevID = Yii::$app->request->post('review-id');

        if($model = StoreReviews::findOne($storeRevID)){
            return $this->renderAjax('_store-review-form', ['model'=>$model]);
        }


    }

    /*
        * Get view type
        * @param string $view Action name
        * @return string view name
        * */
    protected function getViewByType($view)
    {
        if(!Yii::$app->user->isGuest) {

//            if($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {

            if ($client = ClientManager::getClientEmployeeByAuthUser()) {
                switch ($client->manager_type) {
                    case ClientEmployees::TYPE_BASE_ACCOUNT:
//                        case ClientEmployees::TYPE_LOGIST:
                        $view .= '';
                        break;

                    case ClientEmployees::TYPE_DIRECTOR:
//                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
//                        case ClientEmployees::TYPE_MANAGER:
//                        case ClientEmployees::TYPE_MANAGER_INTERN:
                        $view = 'manager/'.$view;
                        break;
                    case ClientEmployees::TYPE_LOGIST:
//                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
//                        case ClientEmployees::TYPE_MANAGER:
//                        case ClientEmployees::TYPE_MANAGER_INTERN:
                        $view = 'logist/'.$view;
                        break;
//                        case ClientEmployees::TYPE_OBSERVER:
//                        case ClientEmployees::TYPE_OBSERVER_NO_TARIFF:
//                        case ClientEmployees::TYPE_REGIONAL_OBSERVER:
//                        case ClientEmployees::TYPE_REGIONAL_OBSERVER_RUSSIA:
//                        case ClientEmployees::TYPE_REGIONAL_OBSERVER_BELARUS:
//                            $view = 'observer/'.$view;
//                            break;

                    default:
                        $view = 'empty-default';
                        break;
                }
//                }
            } else {
                $view = 'empty-default';
            }
        } else {
            $view = 'empty-default';
        }
        return $view;
    }
}