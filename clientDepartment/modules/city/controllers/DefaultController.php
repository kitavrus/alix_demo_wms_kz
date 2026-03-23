<?php

namespace app\modules\city\controllers;
use Yii;
use clientDepartment\components\Controller;
use common\modules\city\models\City;
use common\modules\city\models\Region;


class DefaultController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
    /*
    * Get List city by region id
    * */
    public function actionGetCityByRegion()
    {
        $currentRegionID = Yii::$app->request->post('region_id');
        Yii::$app->response->format = 'json';
//        $data = [''=> Yii::t('transportLogistics/titles', 'Select city')];
        $data = \yii\helpers\ArrayHelper::map(City::find()->where(['region_id'=>$currentRegionID])->orderBy('name')->all(),'id','name');
        return [
            'message' => 'Success',
            'data_options' => $data,
        ];
    }

    /*
   * Get List redion by country id
   * */
    public function actionGetRegionByCountry()
    {
        $currentCountryID = Yii::$app->request->post('country_id');
        Yii::$app->response->format = 'json';
        $data = [''=> \app\modules\city\city::t('titles', 'Select region')];
        $data += \yii\helpers\ArrayHelper::map(Region::find()->where(['country_id'=>$currentCountryID])->orderBy('name')->all(),'id','name');
//        $data += [''=>Yii::t('forms','Please select')];
        return [
            'message' => 'Success',
            'data_options' => $data,
//            'data_options' => array_unshift($data,[''=>Yii::t('forms','Please select')]),
        ];
    }
}
