<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 11.07.2016
 * Time: 7:30
 */
namespace stockDepartment\modules\city\models;

use yii\base\Model;
use Yii;

class RouteDirectionCitySearchForm extends Model {

    public $cityId;
    public $regionId;
    public $countryId;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cityId' => Yii::t('app', 'Город'),
            'regionId' => Yii::t('app', 'Область'),
            'countryId' => Yii::t('app', 'Страна'),
        ];
    }
}