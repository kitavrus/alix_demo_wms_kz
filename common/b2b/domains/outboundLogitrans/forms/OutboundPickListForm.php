<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace common\b2b\domains\outboundLogitrans\forms;

use Yii;


class OutboundPickListForm extends \stockDepartment\modules\outbound\models\OutboundPickListForm {

    public $client_id;
    public $parent_order_number;
    public $order_number;

    public function attributeLabels()
    {
        return [
            'client_id' => Yii::t('outbound/forms', 'Client ID'),
            'parent_order_number' => Yii::t('outbound/forms', 'Parent order number'),
            'order_number' => Yii::t('outbound/forms', 'Order number'),
        ];
    }

    public function rules()
    {
        return [
            [['client_id','parent_order_number'], 'required'],
            [['client_id'], 'integer'],
            [['parent_order_number'], 'string'],
        ];
    }

}