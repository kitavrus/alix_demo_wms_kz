<?php

namespace app\modules\transportLogistics\controllers;

use clientDepartment\components\Controller;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
