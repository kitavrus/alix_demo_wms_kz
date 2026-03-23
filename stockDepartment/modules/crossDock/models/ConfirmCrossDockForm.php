<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace stockDepartment\modules\crossDock\models;

use common\modules\crossDock\models\CrossDock;
use common\modules\employees\models\Employees;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\stock\models\Stock;
use yii\base\Model;
use Yii;


class ConfirmCrossDockForm extends Model {


    public $cross_dock_barcode;
    public $accepted_qty;

    /*
    * Validate cross dock barcode
    * */
    public function validateBarcode($attribute, $params)
    {

        if($barcode = $this->cross_dock_barcode){
            $orders = CrossDock::find()->andWhere(['internal_barcode'=>$barcode, 'status' => [Stock::STATUS_CROSS_DOCK_PRINTED_PICKING_LIST]])->one();
            if(!$orders){
                $this->addError($attribute, Yii::t('outbound/errors','Вы ввели штрих-код еще не существующего или не распечатанного сборочного листа') );
            }
        }
    }

    /*
     *
     * */
    public function attributeLabels()
    {
        return [
            'cross_dock_barcode' => Yii::t('outbound/forms', 'Barcode'),
            'accepted_qty' => Yii::t('outbound/forms', 'Accepted qty'),
        ];
    }

    /*
     *
     *
     * */
    public function rules()
    {

        return [
            [['accepted_qty'], 'safe'],
            [['cross_dock_barcode'], 'required'],
            [['cross_dock_barcode'], 'string'],
            [['cross_dock_barcode'], 'trim'],
            [['cross_dock_barcode'], 'validateBarcode'],
        ];
    }

}