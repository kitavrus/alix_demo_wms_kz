<?php

namespace app\modules\warehouseDistribution\controllers\erenRetail;

use app\modules\warehouseDistribution\models\ErenRetailOutboundForm;

use common\modules\client\models\Client;
use common\modules\outbound\models\OutboundOrder;
use yii\helpers\VarDumper;
use common\modules\outbound\models\ConsignmentOutboundOrder;
use Yii;
use common\modules\transportLogistics\components\TLHelper;
use clientDepartment\modules\client\components\ClientManager;
use yii\web\UploadedFile;
use yii\helpers\BaseFileHelper;
use common\components\OutboundManager;
use yii\helpers\ArrayHelper;
use common\modules\inbound\models\InboundOrder;
use common\modules\stock\models\Stock;
use yii\web\Response;

class OutboundControllerDELETE extends \clientDepartment\components\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionUploadOutboundOrder()
    {
        $model = new ErenRetailOutboundForm();
        $session = Yii::$app->session;
        $client = ClientManager::getClientEmployeeByAuthUser();
        $client_id = $client->client_id;
        $filterWidgetOptionDataRoute = TLHelper::getStoreArrayByClientID($client_id);
        $previewData = [];
        $itemsQty = 0;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->file = UploadedFile::getInstance($model, 'file');

            if (!$model->file) {
                $model->addError('file', Yii::t('outbound/messages', 'Please select file for upload'));
                return $this->render('upload-outbound',
                    [
                        'model' => $model,
                        'filterWidgetOptionDataRoute' => $filterWidgetOptionDataRoute,
                        'previewData' => $previewData,
                        'itemsQty' => $itemsQty,
                    ]);
            }

            $dirPath = 'uploads/erenRetail/outbound/' . date('Ymd') . '/' . date('Hi');

            BaseFileHelper::createDirectory($dirPath);
            $previewData = [];
            $file = $model->file;
            $fileToPath = $dirPath . '/' . $file->baseName . '.' . $file->extension;
            $file->saveAs($fileToPath);
            if (file_exists($fileToPath)) {
                if (($handle = fopen($fileToPath, "r")) !== FALSE) {
                    $row = 0;
                    $itemsQty = 0;
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $row++;
                        $data = array_filter($data, 'trim');
                        if ($row == 1) {
                            if (count($data) != ErenRetailOutboundForm::FILE_COLUMN_QTY) {
                                Yii::$app->getSession()->setFlash('error', Yii::t('outbound/messages', 'File has incorrect number of columns'));
                                return $this->redirect('upload-outbound-order');
                            }
                        }
                        if ($row > 1) {
                            $previewData[$row]['brand'] = isset ($data[0]) ? $data[0] : "";
                            $previewData[$row]['category'] = isset ($data[1]) ? $data[1] : "";
                            $previewData[$row]['internal_id'] = isset ($data[2]) ? $data[2] : "";
                            $previewData[$row]['article'] = isset ($data[3]) ? $data[3] : "";
                            $previewData[$row]['model'] = isset ($data[4]) ? $data[4] : "";
                            $previewData[$row]['color'] = isset ($data[5]) ? $data[5] : "";
                            $previewData[$row]['size'] = isset ($data[6]) ? $data[6] : "";
                            $previewData[$row]['kavala'] = isset ($data[7]) ? $data[7] : "";
                            $previewData[$row]['qty'] = isset ($data[8]) ? $data[8] : "";
                            $previewData[$row]['product_barcode'] = isset ($data[9]) ? $data[9] : "";
                            $itemsQty += isset ($data[8]) ? $data[8] : 0;
                        }
                    }
                }

                fclose($handle);
                $session->set('erenRetailOutboundFilePath', $fileToPath);
            }

        }


        return $this->render('upload-outbound',
            [
                'model' => $model,
                'filterWidgetOptionDataRoute' => $filterWidgetOptionDataRoute,
                'previewData' => $previewData,
                'itemsQty' => $itemsQty,
            ]);
    }

    /*
     *
     *
     * */
    public function actionCreateOutboundOrder()
    {
        $from_point_id = Yii::$app->request->post('from');
        $to_point_id = Yii::$app->request->post('to');
        $session = Yii::$app->session;
        $client = ClientManager::getClientEmployeeByAuthUser();
        $count = ConsignmentOutboundOrder::find()->where(['client_id' => $client->client_id])->count();
        $orderCount = OutboundOrder::find()->where(['client_id' => $client->client_id])->count();
        $partyNumber = date('Ymd').'-'.$client->client_id.'-'.$count;
        $orderNumber = date('Ymd').'-'.$client->client_id.'-'.$orderCount;
        $tableRows = [];

        if($from_point_id && $to_point_id && file_exists($session->get('erenRetailOutboundFilePath'))){
            $fileToPath = $session->get('erenRetailOutboundFilePath');
            if (($handle = fopen($fileToPath, "r")) !== FALSE) {
                $row = 0;
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $row++;
                    $data = array_filter($data, 'trim');
                    if ($row > 1) {
                        $tableRows[$row]['brand'] = isset ($data[0]) ? $data[0] : "";
                        $tableRows[$row]['category'] = isset ($data[1]) ? $data[1] : "";
                        $tableRows[$row]['internal_id'] = isset ($data[2]) ? $data[2] : "";
                        $tableRows[$row]['article'] = isset ($data[3]) ? $data[3] : "";
                        $tableRows[$row]['model'] = isset ($data[4]) ? $data[4] : "";
                        $tableRows[$row]['color'] = isset ($data[5]) ? $data[5] : "";
                        $tableRows[$row]['size'] = isset ($data[6]) ? $data[6] : "";
                        $tableRows[$row]['kavala'] = isset ($data[7]) ? $data[7] : "";
                        $tableRows[$row]['expected_qty'] = isset ($data[8]) ? $data[8] : "";
                        $tableRows[$row]['product_barcode'] = isset ($data[9]) ? $data[9] : "";
                    }
                }
                fclose($handle);
            }

            if($tableRows) {
                $data = [];
                $oManager = new OutboundManager();
                $oManager->initBaseData($client->client_id, $partyNumber, $orderNumber);
                if($coo =  $oManager->createUpdateConsignmentOutbound()){
                    $data['consignment_outbound_order_id'] = $coo->id;
                    $data['parent_order_number'] = $coo->party_number;
                    $data['order_number'] = $orderNumber;
                    $data['from_point_id'] = $from_point_id;
                    $data['to_point_id'] = $to_point_id;
                    if($oo = $oManager->createUpdateOutbound($data)){
                        $oManager->addItems($tableRows);
                        $oManager->addProducts($tableRows);
                        $oManager->createUpdateDeliveryProposalAndOrder();
                        $oManager->reservationOnStockByPartyNumber();

                        $session->remove('erenRetailOutboundFilePath');
                        Yii::$app->getSession()->setFlash('success', Yii::t('outbound/messages', 'Outbound Order № {0} was successfully created',[$oo->order_number]));
                        return $this->redirect('upload-outbound-order');
                    }

                }
            }


        } else {
            $session->remove('erenRetailOutboundFilePath');
            Yii::$app->getSession()->setFlash('error', Yii::t('outbound/messages', 'File upload error. Please try again'));
            return $this->redirect('upload-outbound-order');
        }

    }


}