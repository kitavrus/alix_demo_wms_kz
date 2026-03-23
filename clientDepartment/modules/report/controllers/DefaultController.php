<?php

namespace app\modules\report\controllers;

use yii\web\NotFoundHttpException;
use common\modules\client\models\ClientEmployees;
use common\modules\transportLogistics\components\TLHelper;
use clientDepartment\modules\client\components\ClientManager;
use clientDepartment\modules\report\models\TlDeliveryProposalSearch;
use Yii;
use clientDepartment\components\Controller;
use common\modules\client\models\Client;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use \DateTime;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        if(!ClientManager::canIndexReport() ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        $searchModel = new TlDeliveryProposalSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $client = ClientManager::getClientEmployeeByAuthUser();
        $filterWidgetOptionDataRoute= TLHelper::getStoreArrayByClientID($client->client_id,true);

        return $this->render($this->getViewByType('index'), [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'filterWidgetOptionDataRoute' => $filterWidgetOptionDataRoute,
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

}
