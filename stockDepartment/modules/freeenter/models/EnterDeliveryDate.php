<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace stockDepartment\modules\freeenter\models;

use common\modules\crossDock\models\CrossDock;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use yii\base\Model;
use Yii;

class EnterDeliveryDate extends Model {

    public $ttn_number;
    public $qty_places;

    /*
    *
    * */
    public function attributeLabels()
    {
        return [
            'ttn_number' => 'Номер TTН',
            'qty_places' => 'Кол-во мест',
        ];
    }
    /*
     *
     * */
    public function rules()
    {
        return [
            [['ttn_number','qty_places'], 'required'],
            [['ttn_number','qty_places'], 'integer'],
            [['ttn_number'], 'validateTTNNumber'],
        ];
    }

    /*
     * Check ttn_number
     *
     * */
    public function validateTTNNumber($attribute, $params)
    {
        // 1 - Заявка должна быть в пути
        // 2 - Заявка быть не оплачена
        $value = $this->$attribute;

        if(!TlDeliveryProposal::find()->andWhere([
            'status'=>[TlDeliveryProposal::STATUS_ON_ROUTE],
            'id'=>$value
        ])->exists()) {
            $this->addError($attribute, 'Этой доставке уже указали дату доставку или еще не в пути');
        }
    }
}