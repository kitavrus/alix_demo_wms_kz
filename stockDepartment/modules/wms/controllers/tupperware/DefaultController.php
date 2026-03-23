<?php

namespace app\modules\wms\controllers\tupperware;

use stockDepartment\components\Controller;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
