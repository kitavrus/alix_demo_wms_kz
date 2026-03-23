<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace stockDepartment\modules\stock\models;

use yii\base\Model;
use Yii;
use common\modules\codebook\models\Codebook;

class AccommodationForm extends Model {

    public $type;
    public $from;
    public $to;

    public function attributeLabels()
    {
        return [
            'type' => Yii::t('stock/forms', 'Type'),
            'from' => Yii::t('stock/forms', 'From'),
            'to' => Yii::t('stock/forms', 'To'),
        ];
    }

    public function rules()
    {
        return [
//            [['from','to','type'], 'required'],
            [['type'], 'required'],
            [['type'], 'integer'],
            [['from','to'], 'string'],
            [['from','to'], 'trim'],
        ];
    }

    /*
     * Get type array
     * */
    public static function getTypeArray()
    {
        return [
            '1'=>Yii::t('stock/form','Короб на Полку'), // Перемещает конкретный короб (коробов в одном месте может быть несколько) перемещаем на Полку
            '2'=>Yii::t('stock/form','Из Короба в Короб'), // Перемещаем все содержимое адреса (все короба) в другой адрес (место / полку)
//            '2'=>Yii::t('stock/form','С Полки на Полку'), // Перемещаем все содержимое адреса (все короба) в другой адрес (место / полку)
//            '3'=>Yii::t('stock/form','Палету на Стелаж'), // Перемещаем Палеты с одного адреса в другой
//            '4'=>Yii::t('stock/form','С палеты/короба содержимое на полку'), // Высыпаем содержимое палеты или короба на полку. Если на палете есть короба удаляем адрес палеты а коробам ставим адрес места
        ];
    }

    /*
     * Get array label translate
     *
     * */
    public static function labelTranslateArray()
    {
        return [
            '1'=>[
                'from'=>Yii::t('stock/form','Штрих код короба'),
                'to'=>Yii::t('stock/form','Штрих код полки'),
            ],
            '2'=>[
                'from'=>Yii::t('stock/form','Штрих код полки'),
                'to'=>Yii::t('stock/form','На штрих-код полки'),
            ],
            '3'=>[
                'from'=>Yii::t('stock/form','Штрих код палеты'),
                'to'=>Yii::t('stock/form','На штрих-код Стелаж'),
            ],
            '4'=>[
                'from'=>Yii::t('stock/form','Штрих код палеты/короба'),
                'to'=>Yii::t('stock/form','На штрих-код полки/стелаж'),
            ],
        ];
    }


    /*
     * IsBox
     * @param string barcode
     * @return boolean
     * */
//    public static function IsBoxValidate($attribute, $params)
//    {
//        $r = false;
//        return $r;
//        self::addError('to', 'Incorrect username or password. To');
//    }

    /*
     * Is Regiment
     * @param string barcode
     * @return boolean
     * */
//    public static function IsRegimentValidate($attribute, $params)
//    {
//        $r = false;
//        return $r;
//        self::addError('from', 'Incorrect username or password. From');
//    }

    /*
     * Is Rack
     * @param string barcode
     * @return boolean
     * */
//    public static function IsRackValidate($attribute, $params)
//    {
//        $r = false;
//        return $r;
//    }
}