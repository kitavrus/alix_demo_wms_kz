<?php
namespace stockDepartment\modules\wms\models\miele\form;

use common\modules\inbound\models\InboundOrder;
use yii\base\Model;
use Yii;

class InboundChangeAddressForm extends Model {

    public $order_id;
    public $type;
    public $from;
    public $to;

    public function attributeLabels()
    {
        return [
            'type' => Yii::t('stock/forms', 'Type'),
            'from' => Yii::t('stock/forms', 'From'),
            'to' => Yii::t('stock/forms', 'To'),
            'order_id' => Yii::t('stock/forms', 'Номер приходной накладной'),
        ];
    }

    public function rules()
    {
        return [
            [['type','order_id'], 'required'],
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

    public function getOrder() {
        return InboundOrder::findOne($this->order_id);
    }
}