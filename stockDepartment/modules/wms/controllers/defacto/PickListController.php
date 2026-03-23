<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 30.08.2017
 * Time: 8:43
 */

namespace app\modules\wms\controllers\defacto;

use stockDepartment\modules\wms\models\defacto\PickList\service\PickListService;
use stockDepartment\modules\wms\models\defacto\PickListScanForm;
use yii\bootstrap\ActiveForm;
use yii\web\Response;
use Yii;

class PickListController extends \stockDepartment\components\Controller
{
    public function actionIndex()
    {
        $pickScanListForm = new PickListScanForm();
        return $this->render('index', ['pickScanListForm' => $pickScanListForm]);
    }

    public function actionScanPickList() {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $pickScanListForm = new PickListScanForm();
        $pickScanListForm->setScenario('sPickListBarcode');
        if($pickScanListForm->load(Yii::$app->request->post()) && $pickScanListForm->validate()) {
            return [
                'success'=>1,
            ];
        }

        $errors = ActiveForm::validate($pickScanListForm);
        return [
            'success'=>(empty($errors) ? '1' : '0'),
            'errors' => $errors
        ];

    }
    public function actionScanLot() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $pickScanListForm = new PickListScanForm();
        $pickScanListForm->setScenario('sLotBarcode');
        if($pickScanListForm->load(Yii::$app->request->post()) && $pickScanListForm->validate()) {
            $service = new PickListService($pickScanListForm->getDTO());
            $service->setStatusScanned();
            return [
                'success'=>1,
            ];
        }

        $errors = ActiveForm::validate($pickScanListForm);
        return [
            'success'=>(empty($errors) ? '1' : '0'),
            'errors' => $errors
        ];
    }
}