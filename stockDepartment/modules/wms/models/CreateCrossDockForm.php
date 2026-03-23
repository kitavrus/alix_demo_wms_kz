<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace stockDepartment\modules\wms\models;

use common\modules\crossDock\models\CrossDock;
use yii\base\Model;
use Yii;

class CreateCrossDockForm extends Model {

    public $client_id;
    public $order_number;
    public $expected_number_places_qty;
    public $box_m3;

    /*
    *
    * */
    public function attributeLabels()
    {
        return [
            'client_id' => Yii::t('inbound/forms', 'Client'),
            'order_number' => Yii::t('inbound/forms', 'Order number'),
            'route_to' => Yii::t('transportLogistics/forms', 'Route To'),
            'box_m3' => Yii::t('transportLogistics/forms', 'Mc'),
            'number_places_qty' => Yii::t('transportLogistics/forms', 'Number Places'),
        ];
    }
    /*
     *
     * */
    public function rules()
    {
        return [
            [['order_number'], 'required'],
            [['expected_number_places_qty'], 'integer'],
            [['box_m3'], 'number'],
        ];
    }

    /*
     * Check unique route_to
     *
     * */
/*    public function validateUniqueRouteTo($attribute, $params)
    {
        $route_to = $this->route_to;
        $order_number = $this->order_number;
        $client_id = $this->client_id;

        if(CrossDock::find()->where(['to_point_id'=>$route_to,'client_id'=>$client_id,'party_number'=> $order_number])->exists()) {
            $this->addError('additemcrossdockform-route_to', 'Этот магазин уже есть в этом заказе');
        }
    }*/
}