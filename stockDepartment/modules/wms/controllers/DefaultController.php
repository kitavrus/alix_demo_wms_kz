<?php

namespace app\modules\wms\controllers;

use common\components\BarcodeManager;
use stockDepartment\components\Controller;

use Yii;
use common\modules\client\models\Client;
use stockDepartment\modules\product\models\ProductSearch;

class DefaultController extends Controller
{
    /*
    * Route form by client id
    *
    * */
    public function actionRouteForm()
    {
        $id = Yii::$app->request->get('id');
        $viewFile = '';

        $clientsArray =  Client::getActiveWMSItems();

        switch($id) {
//            case Client::CLIENT_COLINS: // Colins
//                $viewFile = '_colinsProcessForm';
//                break;
            case Client::CLIENT_DEFACTO: // Defacto
                $viewFile = '_defactoProcessForm';
                break;
            case Client::CLIENT_MIELE:
                $viewFile = '_mieleProcessForm';
                break;
//            case Client::CLIENT_KOTON: // Koton
//                $viewFile = '_kotonProcessForm';
//                break;
//            case Client::CLIENT_AKMARAL: // AKMARAL
//                $viewFile = '_akmaralProcessForm';
//                break;
//            case Client::CLIENT_TUPPERWARE: // TUPPERWARE
//                $viewFile = '_tupperwareProcessForm';
//                break;
//            case Client::CLIENT_MACCOFFEEKZ: // MACCOFFEE KZ
//                $viewFile = '_maccoffeekzProcessForm';
//                break;
            default:
                $viewFile = '_baseProcessForm';
                break;
        }
        return $this->render($viewFile,['id'=>$id,'clientsArray'=>$clientsArray]);
    }

    /*
     * Select order for print pick list
     *
     * */
    public function actionIndex()
    {
//        $clientsArray =  Client::getActiveItems();
        $clientsArray =  Client::getActiveWMSItems();

        return $this->render('index', ['clientsArray'=>$clientsArray]);
    }
}
