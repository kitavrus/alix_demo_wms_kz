<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 10:03
 */

namespace app\modules\ecommerce\controllers\intermode;

class DefaultController extends  \stockDepartment\components\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}