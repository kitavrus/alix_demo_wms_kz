<?php

namespace app\modules\warehouseDistribution\controllers\tupperware;

use common\modules\client\models\ClientEmployees;
use common\modules\store\models\Store;
use common\modules\transportLogistics\components\TLHelper;
use clientDepartment\modules\client\components\ClientManager;
use Yii;
use common\modules\billing\models\TlDeliveryProposalBilling;
use clientDepartment\modules\report\models\TlDeliveryProposalBillingSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use clientDepartment\components\Controller;

/**
 * DefaultController implements the CRUD actions for TlDeliveryProposalBilling model.
 */
class BillingController extends Controller
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
     * Lists all TlDeliveryProposalBilling models.
     * @return mixed
     */
    public function actionIndex()
    {
        if(!ClientManager::canIndexBilling() ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        $searchModel = new TlDeliveryProposalBillingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


        $client = ClientManager::getClientEmployeeByAuthUser();
        $filterWidgetOptionDataRoute = TLHelper::getStoreArrayByClientID($client->client_id);

        return $this->render($this->getViewByType('index'), [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'filterWidgetOptionDataRoute' => $filterWidgetOptionDataRoute,
        ]);
    }

    /**
     * Displays a single TlDeliveryProposalBilling model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if(!ClientManager::canViewBilling($model) ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        return $this->render($this->getViewByType('view'), [
            'model' => $model,
        ]);
    }

    /*
 * Get view type
 * @param string $view Action name
 * @return string view name
 * */
    protected function getViewByType_TO_DELETE($view)
    {
        if(!Yii::$app->user->isGuest) {

//            if($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {

                if ($client = ClientManager::getClientEmployeeByAuthUser()) {
                    switch ($client->manager_type) {
                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
                            $view .= '';
                            break;

                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
                            $view = 'manager/'.$view;
                            break;
                        case ClientEmployees::TYPE_OBSERVER:
                        case ClientEmployees::TYPE_OBSERVER_NO_TARIFF:
                            $view = 'observer/'.$view;
                            break;

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

    /**
     * Creates a new TlDeliveryProposalBilling model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
//    public function actionCreate()
//    {
//        $model = new TlDeliveryProposalBilling();
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return $this->render('create', [
//                'model' => $model,
//            ]);
//        }
//    }

    /**
     * Updates an existing TlDeliveryProposalBilling model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
//    public function actionUpdate($id)
//    {
//        $model = $this->findModel($id);
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return $this->render('update', [
//                'model' => $model,
//            ]);
//        }
//    }

    /**
     * Deletes an existing TlDeliveryProposalBilling model.
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
     * Finds the TlDeliveryProposalBilling model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TlDeliveryProposalBilling the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TlDeliveryProposalBilling::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /*
* Import to excel
*
**/
    public function actionExportToExcel()
    {
        if(Yii::$app->language == 'tr') {
            Yii::$app->language = 'en';
        }
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


        $i = 1;
        $activeSheet->setCellValue('A' . $i, 'Откуда'); // +
        $activeSheet->setCellValue('B' . $i, 'Куда'); // +
        $activeSheet->setCellValue('C' . $i, 'Цена'); // +
        $activeSheet->setCellValue('D' . $i, 'Тип подсчета'); // +

        $clientEmploy = ClientEmployees::findOne(['user_id'=>Yii::$app->user->id]);
        $clientStoreArray = TLHelper::getStoreArrayByClientID($clientEmploy->client_id);

        $searchModel = new TlDeliveryProposalBillingSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['tariff_type' => TlDeliveryProposalBilling::TARIFF_TYPE_COMPANY_INDIVIDUAL]);
        $dataProvider->pagination = false;
        $dps = $dataProvider->getModels();
        $if = Yii::$app->request->get('if');

        foreach ($dps as $model) {
            $i++;

            $activeSheet->setCellValue('A' . $i,$clientStoreArray[$model->route_from]);
            $activeSheet->setCellValue('B' . $i, $clientStoreArray[$model->route_to]);
            $activeSheet->setCellValue('C' . $i, $model->price_invoice_with_vat);
            $activeSheet->setCellValue('D' . $i, $model->getRuleType());

            if($if) {
                if ($conditions = $model->conditions) {
                    foreach ($conditions as $itemCondition) {
                        $i++;
                        $activeSheet->setCellValue('A' . $i, $itemCondition->formula_tariff);
                        $activeSheet->setCellValue('B' . $i, $itemCondition->price_invoice);
                        $activeSheet->setCellValue('C' . $i, $itemCondition->price_invoice_with_vat);
                    }
                }
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="delivery-proposal-billing-report-' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }
}