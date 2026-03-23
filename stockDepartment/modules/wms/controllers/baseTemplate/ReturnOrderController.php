<?php

namespace app\modules\wms\controllers\baseTemplate;

use Yii;
use stockDepartment\modules\wms\models\baseTemplate\ReturnForm;
use yii\web\Controller;
use stockDepartment\modules\returnOrder\api\ReturnDeFactoSoapAPI;

class ReturnOrderController extends Controller
{
    public function actionIndex()
    {
        $model = new ReturnForm();
        return $this->render('index', ['model' => $model]);
    }
}