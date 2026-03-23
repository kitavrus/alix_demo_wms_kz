<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace stockDepartment\modules\inbound\models;

use common\modules\crossDock\models\CrossDock;
use common\modules\employees\models\Employees;
use common\modules\inbound\models\InboundOrder;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\stock\models\Stock;
use yii\base\Model;
use Yii;


class AllocationListForm extends Model {


    public $box_barcode;

    /*
    * Validate box barcode
    * */
    public function validateBarcode($attribute, $params)
    {

        if($barcode = $this->box_barcode){
            $orders = InboundOrder::find()->andWhere(['client_box_barcode'=>$barcode])->one();
            if(!$orders){
                $this->addError($attribute, Yii::t('outbound/errors','Вы ввели штрих-код несуществующего короба') );
            }
        }
    }

    /*
     *
     * */
    public function attributeLabels()
    {
        return [
            'box_barcode' => Yii::t('inbound/forms', 'Box barcode'),
        ];
    }

    /*
     *
     *
     * */
    public function rules()
    {

        return [
            [['box_barcode'], 'required'],
            [['box_barcode'], 'string'],
            [['box_barcode'], 'trim'],
            [['box_barcode'], 'validateBarcode'],
        ];
    }

}