<?php

namespace app\modules\outbound\controllers;

use common\api\DeFactoSoapAPI;
use common\components\BarcodeManager;
use common\components\MailManager;
use common\modules\outbound\models\ConsignmentOutboundOrder;
use common\modules\outbound\models\OutboundUploadItemsLog;
use common\modules\outbound\models\OutboundUploadLog;
use stockDepartment\modules\outbound\models\OutboundOrderGridSearch;
use common\modules\employees\models\Employees;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\stock\models\Stock;
use common\modules\store\models\Store;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use stockDepartment\modules\outbound\models\BeginEndPickListForm;
use stockDepartment\modules\outbound\models\DeFactoAPIOutboundForm;
use stockDepartment\modules\outbound\models\OutboundOrderSearch;
use stockDepartment\modules\outbound\models\OutboundPickingListSearch;
use stockDepartment\modules\outbound\models\ScanningForm;
use Yii;
use common\modules\client\models\Client;
use stockDepartment\components\Controller;
use stockDepartment\modules\outbound\models\OutboundPickListForm;
use stockDepartment\modules\product\models\ProductSearch;
use yii\base\DynamicModel;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\BaseFileHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;
use common\helpers\DateHelper;

class NewController extends Controller
{
    /*
    * Route form by client id
    *
    * */
    public function actionRouteForm()
    {
        $id = Yii::$app->request->post('id');
        $viewFile = '';

        switch($id) {
            case '1': // Colins
                $viewFile = '_colinsProcessForm';
                break;
            case '2': // Defacto
                $viewFile = '_defactoProcessForm';
                break;
            default:
                break;
        }
        return $this->renderAjax($viewFile);
    }

    /*
     * Select order for print pick list
     *
     * */
    public function actionIndex()
    {
        $clientsArray =  Client::getActiveItems();

        return $this->render('index', ['clientsArray'=>$clientsArray]);
    }
}




