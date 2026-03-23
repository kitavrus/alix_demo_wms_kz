<?php

namespace app\modules\warehouseDistribution\controllers\colins;

use clientDepartment\components\Controller;

class DefaultController extends Controller
{
    public function actionFileUpload()
    {
        return $this->render('file-upload');
    }
}
