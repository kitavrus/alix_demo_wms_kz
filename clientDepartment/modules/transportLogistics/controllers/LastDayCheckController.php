<?php

namespace app\modules\transportLogistics\controllers;

use clientDepartment\components\Controller;
use Yii;
use yii\web\NotFoundHttpException;
use common\modules\transportLogistics\models\TlDeliveryProposal;
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
        $service = new ReportToDay();
        $moreDeliveryTime = $service->getMoreDeliveryTime();

        return $this->render('index-v2', [
                    'moreDeliveryTime' => $moreDeliveryTime,
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