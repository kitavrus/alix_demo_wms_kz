<?php

namespace app\modules\audit\controllers;

use stockDepartment\components\Controller;
use stockDepartment\modules\audit\models\AuditSearch;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        $className = \Yii::$app->request->get('classname');
        $parent_id = \Yii::$app->request->get('parent_id');

        if (!empty($className) && !empty($parent_id)) {
            $search = new AuditSearch($className);
            $dataProvider = $search->search($parent_id);

            if (!empty($dataProvider)) {
                return $this->render(
                    'index',
                    [
                        'dataProvider' => $dataProvider,
                    ]
                );
            } else {
                throw new \yii\web\HttpException(404, 'Not found');
            }


        } else {
            throw new \yii\web\HttpException(500, 'Required parameters missing');
        }


    }
}
