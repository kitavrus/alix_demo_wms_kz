<?php

namespace stockDepartment\modules\crossDock\controllers;

use common\modules\crossDock\models\ConsignmentCrossDock;
use common\modules\stock\models\Stock;
use Yii;
use common\modules\client\models\Client;
use stockDepartment\modules\crossDock\models\GenerateCrossDockForm;
use stockDepartment\modules\crossDock\models\ConfirmCrossDockForm;
use yii\helpers\VarDumper;
use yii\web\Response;
use common\modules\crossDock\models\CrossDock;

class DefaultController extends \stockDepartment\components\Controller
{
    /*
     * Generate Cross dock picking list
     * @return mixed
     * */
    public function actionGenerateCrossDock()
    {
        $formModel = new GenerateCrossDockForm();
        $clientsArray = Client::getActiveItems();

        return $this->render('generate-cross-dock', [
            'formModel' => $formModel,
            'clientsArray' => $clientsArray,
        ]);
    }

    /*
     * Show form for confirmation cross dock
     * items actual qty
     * @return mixed
     * */
    public function actionConfirmCrossDock()
    {
        $formModel = new ConfirmCrossDockForm();
        $crossOrders = [];
        if ($formModel->load(Yii::$app->request->post()) && $formModel->validate()) {
                $crossOrders = CrossDock::find()
                    ->andWhere([
                    'internal_barcode' => $formModel->cross_dock_barcode,
                    'status' => Stock::STATUS_CROSS_DOCK_PRINTED_PICKING_LIST
                ])
                    ->all();

        }
        return $this->render('confirm-cross-dock', ['formModel' => $formModel,'crossOrders' => $crossOrders]);
    }

    /*
     * Set actual item qty
     * @param array data
     * @return mixed
     **/
    public function actionApplyQty()
    {

        if ($data = Yii::$app->request->post('data')) {

            foreach ($data as $value){
                if($record = CrossDock::findOne($value[0])){
                    $record->accepted_number_places_qty = $value[1];
                    $record->status = Stock::STATUS_CROSS_DOCK_COMPLETE;
                    if($record->save(false)){
                        $record->createDeliveryProposal();

                    }

                    if($cCD = ConsignmentCrossDock::findOne($record->consignment_cross_dock_id)) {
                        $cCD->accepted_number_places_qty += $record->accepted_number_places_qty;
                        $cCD->status = Stock::STATUS_CROSS_DOCK_COMPLETE;
                        $cCD->save(false);
                    }
                }
            }
            Yii::$app->getSession()->setFlash('success', Yii::t('inbound/messages', 'Сборочный лист успешно подтвержден'));
        }
        return $this->redirect('confirm-cross-dock');
    }

    /*
     * Get cross dock orders group by party number by client
     * @param integer client_id
     * @return JSON
     * */
    public function actionGetCrossDockOrdersByClientId()
    {
        $clientID = Yii::$app->request->post('client_id');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = ['' => ''];
        $data += CrossDock::getCrossDockListByClientID($clientID);
        return [
            'message' => 'Success',
            'dataOptions' => $data,
        ];
    }

    /*
     * Generate Cross dock list PDF file
     * @param integer client_id
     * @param string party_number
     * @return mixed
     *
     * */
    public function actionPrintCrossDockList()
    {
        $client_id = \Yii::$app->request->get('client_id');
        $party_number = \Yii::$app->request->get('party_number');
        $barcode = '';
        $rptQty = '';

        if($client_id && $party_number) {

            if($cCD = ConsignmentCrossDock::findOne(['client_id'=>$client_id,'party_number'=>$party_number])) {
                $rptQty = $cCD->expected_rpt_places_qty;
            }


            $client = Client::findOne($client_id);
            $crossOrders = CrossDock::find()
                ->andWhere([
                    'party_number'=>$party_number,
                    'client_id'=>$client_id,
                    'status'=>[Stock::STATUS_CROSS_DOCK_NEW, Stock::STATUS_CROSS_DOCK_PRINTED_PICKING_LIST],
                ])
                ->all();
            if($crossOrders){
                foreach ($crossOrders as  $co){
                    $co->status = Stock::STATUS_CROSS_DOCK_PRINTED_PICKING_LIST;
                    $co->assignBarcode();
                    $co->save(false);
                    $barcode = $co->internal_barcode;
                }

                $cCD->status = Stock::STATUS_CROSS_DOCK_PRINTED_PICKING_LIST;
                $cCD->save(false);
            }
        }

        return $this->render('print-crossdoc-list', ['rptQty'=>$rptQty,'crossOrders' => $crossOrders, 'client' => $client, 'barcode'=> $barcode]);
    }


}
