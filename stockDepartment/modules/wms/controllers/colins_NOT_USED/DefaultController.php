<?php

namespace app\modules\wms\controllers\colins;

use stockDepartment\components\Controller;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
