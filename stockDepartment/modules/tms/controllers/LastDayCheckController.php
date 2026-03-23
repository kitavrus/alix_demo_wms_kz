<?php

namespace app\modules\tms\controllers;

use common\modules\city\models\RouteDirections;
use stockDepartment\components\Controller;

use common\components\BookkeeperManager;
use common\modules\bookkeeper\models\Bookkeeper;
use common\modules\city\models\City;
use common\modules\city\models\Country;
use common\modules\city\models\Region;
use common\modules\client\models\Client;
use Yii;
use common\components\DeliveryProposalManager;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use stockDepartment\modules\tms\models\TlDeliveryProposalSearch;
use common\modules\transportLogistics\components\TLHelper;
use stockDepartment\modules\report\service\reportToDay\Service as ReportToDay;
use common\components\FailDeliveryStatus\AddStatusForm;
use common\components\FailDeliveryStatus\Service;

/**
 * Default controller for the `TMS` module
 */
class LastDayCheckController extends Controller
{
    /**
     * Lists all TlDeliveryProposal models.
     * @return mixed
     */
    public function actionIndex()
    {
//        $searchModel = new TlDeliveryProposalSearch();
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//        $dataProvider->query->andWhere(['not', ['status_invoice'=>TlDeliveryProposal::INVOICE_PAID]]);
//
//        $clientArray = Client::getActiveTMSItems();
//        $storeArray = TLHelper::getStockPointArray();
//        $cityArray = City::getArrayData();
//        $regionArray = Region::getArrayData();
//        $countryArray = Country::getArrayData();
//        $routeDirectionArray = RouteDirections::getArrayData();
//
//        $tomorrow = new \DateTime();
//        $tomorrow->modify('+1 day');
//        $tomorrow= $tomorrow->format('Y-m-d');



        $service = new ReportToDay();
        $moreDeliveryTime = $service->getMoreDeliveryTime();

//        VarDumper::dump($moreDeliveryTime,10,true);
//        die;


        return $this->render('index-v2', [
                    'moreDeliveryTime' => $moreDeliveryTime,
            ]);

//        return $this->render('index', [
//            'searchModel' => $searchModel,
//            'dataProvider' => $dataProvider,
//            'clientArray' => $clientArray,
//            'storeArray' => $storeArray,
//            'tomorrow' => $tomorrow,
//            'cityArray' => $cityArray,
//            'regionArray' => $regionArray,
//            'countryArray' => $countryArray,
//            'routeDirectionArray' => $routeDirectionArray,
//        ]);
    }

    /**
     * @param integer $id
     * @return mixed
     */
    public function actionAddStatus($id)
    {
        $addStatusForm = new AddStatusForm();
        $addStatusForm->deliveryProposalId = $id;

        if ($addStatusForm->load(Yii::$app->request->post())) {
//            echo "<br />";
//            echo "<br />";
//            echo "<br />";
//            echo "<br />";
//            echo "<br />";
//            echo "<br />";
            //VarDumper::dump($addStatusForm->getDTO(),10,true);

            if(Service::add($addStatusForm->getDTO())) {
                return $this->redirect('index');
            }
        }

        $deliveryProposal = $this->findModel($addStatusForm->deliveryProposalId);
        $storeArray = TLHelper::getStockPointArray();
        return $this->render('add-status', [
            'addStatusForm' => $addStatusForm,
            'deliveryProposal' => $deliveryProposal,
            'storeArray' => $storeArray,
        ]);
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
}