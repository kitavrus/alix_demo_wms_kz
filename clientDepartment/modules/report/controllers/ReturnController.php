<?php

namespace app\modules\report\controllers;

use app\modules\report\models\ReturnOrderSearch;
use common\modules\client\models\ClientEmployees;
use common\modules\returnOrder\models\ReturnOrder;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use common\modules\transportLogistics\components\TLHelper;
use clientDepartment\modules\client\components\ClientManager;
use Yii;
use clientDepartment\components\Controller;
use common\modules\client\models\Client;
use common\modules\returnOrder\models\ReturnOrderItems;
use yii\helpers\BaseFileHelper;

class ReturnController extends Controller
{
    public function actionIndex()
    {
        if(!ClientManager::canIndexReport() ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }
        $clientEmploy = ClientEmployees::findOne(['user_id'=>Yii::$app->user->id]);
        $client = Client::findOne($clientEmploy->client_id);


        $searchModel = new ReturnOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['client_id' => $client->id]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        if(!ClientManager::canIndexReport() ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        $model = $this->findModel($id);
        $ItemsProvider = new ActiveDataProvider([
            'query' => $model->getOrderItems(),
        ]);

        return $this->render('view', [
            'model' => $model,
            'ItemsProvider' => $ItemsProvider,
        ]);
    }

    /**
     * Finds the TlDeliveryProposalBilling model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ReturnOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {

        $clientEmploy = ClientEmployees::findOne(['user_id'=>Yii::$app->user->id]);
        $client = Client::findOne($clientEmploy->client_id);

        if (($model = ReturnOrder::findOne(['id'=>$id, 'client_id'=>$client->id])) !== null) {
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
        $activeSheet->setCellValue('A' . $i, 'ID'); // +
        $activeSheet->setCellValue('B' . $i, Yii::t('return/forms', 'Order Number')); // +
        $activeSheet->setCellValue('C' . $i, Yii::t('return/forms', 'Expected Qty')); // +
        $activeSheet->setCellValue('D' . $i, Yii::t('return/forms', 'Accepted Qty')); // +
        $activeSheet->setCellValue('E' . $i, Yii::t('return/forms', 'Begin Datetime')); // +
        $activeSheet->setCellValue('F' . $i, Yii::t('return/forms', 'End Datetime')); // +
        $activeSheet->setCellValue('G' . $i, Yii::t('return/forms', 'Created At')); // +
        $activeSheet->setCellValue('H' . $i, Yii::t('return/forms', 'Status')); // +

        if(!ClientManager::canIndexReport() ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        $clientEmploy = ClientEmployees::findOne(['user_id'=>Yii::$app->user->id]);
        $client = Client::findOne($clientEmploy->client_id);

        if($client->id == 2) {
            $activeSheet->setCellValue('J' . $i, 'UrunKodu'); // +
            $activeSheet->setCellValue('K' . $i, 'KoliBarkod'); // +
        }

        $searchModel = new ReturnOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dataProvider->query->andFilterWhere(['client_id'=>$client->id]);
        $dps = $dataProvider->getModels();

        foreach ($dps as $model) {
            $i++;
            $activeSheet->setCellValue('A' . $i, $model->id);
            $activeSheet->setCellValue('B' . $i, $model->order_number);
            $activeSheet->setCellValue('C' . $i, $model->expected_qty);
            $activeSheet->setCellValue('D' . $i, $model->accepted_qty);
            $activeSheet->setCellValue('E' . $i, !empty($model->begin_datetime) ? Yii::$app->formatter->asDatetime($model->begin_datetime): "-"); // +
            $activeSheet->setCellValue('F' . $i, !empty($model->end_datetime)? Yii::$app->formatter->asDatetime($model->end_datetime): "-"); // +
            $activeSheet->setCellValue('G' . $i, !empty($model->created_at) ? Yii::$app->formatter->asDatetime($model->created_at): "-"); // +
            $activeSheet->setCellValue('H' . $i, $model->getStatusValue()); // +

            if($client->id == 2) {
                $KoliBarkod = '';
                $UrunKodu = '';

                if ($model->extra_fields) {
                    $extraFields = [];
                    try {
                        $extraFields = \yii\helpers\Json::decode($model->extra_fields);
                    } catch (\yii\base\InvalidParamException $e) {
                        file_put_contents('return-order-errors.log',$model->id,FILE_APPEND);
                    }

                    $koliResponseData = [];
                    if(isset($extraFields['IadeKabulResult->Koli'])) {
                        $koliResponseData = $extraFields['IadeKabulResult->Koli'];
                    } elseif(isset($extraFields['KoliIadeKabulResult->Koli'])) {
                        $koliResponseData = $extraFields['KoliIadeKabulResult->Koli'];
                    } elseif(isset($extraFields['koliResponse'] )) {
                        $koliResponseData = $extraFields['koliResponse'];
                    }

                    if(!empty($koliResponseData) && isset($koliResponseData['KoliBarkod'])) {
                        $KoliBarkod = $koliResponseData['KoliBarkod'];
                    }
                    if(!empty($koliResponseData) && isset($koliResponseData['UrunKodu'])) {
                        $UrunKodu = $koliResponseData['UrunKodu'];
                    }
                }
                $activeSheet->setCellValue('J' . $i, $UrunKodu); // +
                $activeSheet->setCellValue('K' . $i, $KoliBarkod); // +
            }

        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $dirPath = 'uploads/DeFacto/return/export/'.date('Ymd').'/'.date('His');
        BaseFileHelper::createDirectory($dirPath);
        $fileName = 'return-order-export-'.time().'.xlsx';
        $fullPath = $dirPath.'/'.$fileName;
        $objWriter->save($fullPath);
        return Yii::$app->response->sendFile($fullPath,$fileName);
    }
}
