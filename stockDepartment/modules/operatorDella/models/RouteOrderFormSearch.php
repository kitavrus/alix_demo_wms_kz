<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 08.02.2016
 * Time: 9:32
 */

namespace app\modules\operatorDella\models;


use yii\base\Model;
use Yii;

class RouteOrderFormSearch extends Model
{
    public $cityFrom;
    public $cityTo;
    public $dateLoadingCargo;
    public $m3;
    public $kg;
    public $places;
    public $phone;
    public $fio;
    public $ttn;
    public $client_id;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['places','cityTo','cityFrom','ttn','client_id'], 'integer'],
            [['ttn'], 'trim'],
            [['m3','kg'], 'number'],
            [['dateLoadingCargo','phone','fio'], 'string'],
        ];
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'cityFrom' => 'Из города', //Yii::t('client/forms', 'City From'),
            'cityTo' => 'В город', //Yii::t('client/forms', 'City to'),
            'm3' => 'м3',
            'kg' => 'кг',
            'places' => 'Мест',
            'phone' => 'Телефон',
            'fio' => 'ФИО',
            'ttn' => 'ТТН',
            'client_id' => 'Клиент',
        ];
    }
}