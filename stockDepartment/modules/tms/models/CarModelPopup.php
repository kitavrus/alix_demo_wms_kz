<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace stockDepartment\modules\tms\models;

use yii\base\Model;
use Yii;
use common\modules\transportLogistics\models\TlDeliveryProposal;

class CarModelPopup extends Model {

    /*
     * @var string
     * */

    public $agent_id;
    public $car_id;
    public $driver_name;
    public $driver_phone;
    public $driver_auto_number;
    public $car_price_invoice;
    public $car_price_invoice_with_vat;
    public $ids;
    //Unforseen expences
    public $ue_name;
    public $ue_price_cache;
    public $ue_cash_no;
    public $ue_who_pays;
    public $ue_comment;

//* @property integer $price_invoice
//* @property string $price_invoice_with_vat

    public function attributeLabels()
    {
        return [
            'agent_id' => Yii::t('titles', 'Subcontractor'),
            'car_id' => Yii::t('titles', 'Car'),
            'driver_name' => Yii::t('titles', 'Driver name'),
            'driver_phone' => Yii::t('titles', 'Driver phone'),
            'driver_auto_number' => Yii::t('titles', 'Driver auto number'),
            'car_price_invoice' => Yii::t('transportLogistics/forms', 'Price Invoice'),
            'car_price_invoice_with_vat' => Yii::t('transportLogistics/forms', 'Price Invoice With Vat'),
            'ue_name' => Yii::t('transportLogistics/forms', 'Name'),
            'ue_price_cache' => Yii::t('transportLogistics/forms', 'Price expenses'),
            'ue_cash_no' => Yii::t('transportLogistics/forms', 'Cash No'),
            'ue_who_pays' => Yii::t('transportLogistics/forms', 'Who pays'),
            'ue_comment' => Yii::t('transportLogistics/forms', 'Comment'),
        ];
    }

    public function rules()
    {
        return [
            [['agent_id','car_id','driver_name','driver_phone','driver_auto_number'], 'required'],
            [['agent_id','car_id','ue_cash_no', 'ue_who_pays'], 'integer'],
            [['driver_name','driver_phone','driver_auto_number', 'ue_name', 'ue_comment'], 'string'],
            [['car_price_invoice_with_vat','car_price_invoice','ue_price_cache'], 'number'],
            [['ids',], 'safe'],
            [['ids'], 'validateFrom'],
        ];
    }

    /*
    * Remove product in box
    *
    * */
    public function validateFrom($attribute, $params)
    {
        if($this->$attribute){
            $value = explode(',', $this->$attribute);
            $checkFromDP = '';
            $checkFromRouteDP = '';

            //Проверяем точки отправления в заявках
            foreach ($value as $dpID){
                if($dp = TlDeliveryProposal::findOne($dpID)){
                    if(!$checkFromDP){
                        $checkFromDP = $dp->route_from;
                    }

                    if ($dpRoutes = $dp->proposalRoutes){

                        $modelDpRoute = isset ($dpRoutes[0]) ? $dpRoutes[0] : 0;
                        if(!$checkFromRouteDP && $modelDpRoute){
                            $checkFromRouteDP = $modelDpRoute->route_from;
                        }

                        if($dp->route_from != $checkFromRouteDP){
                            $this->addError('agent_id', 'В первом маршруте заявки <b>['.$dpID.']</b> точка отправления отличается от других </br>');
                        }
                    }

                    if($dp->route_from != $checkFromDP){
                        $this->addError('agent_id', 'В заявке <b>['.$dpID.']</b> точка отправления отличается от других </br>');
                    }
                }
            }
        }

    }
}