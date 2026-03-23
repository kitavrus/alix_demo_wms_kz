<?php

namespace stockDepartment\modules\outbound\models;

use common\modules\outbound\models\OutboundOrder;
use Yii;
use yii\base\Model;

class UpdateToPointForm extends Model
{
    public $to_point_id;
    public $order_id;
    public $order_number;

    public function rules()
    {
        return [
            [['order_id', 'order_number'], 'required'],
            ['to_point_id', 'required', 'message' => 'Необходимо заполнить «В пункт»']
        ];
    }

    public function attributeLabels()
    {
        return [
            'to_point_id' => Yii::t('outbound/forms', 'To point'),
        ];
    }

    public function updateOrder()
    {
        if (!$this->validate()) {
            return false;
        }

        $order = OutboundOrder::findOne($this->order_id);

        if (!$order) {
            Yii::$app->session->setFlash('error', 'Заказ не найден');
            return false;
        }

        if ($order->updateAttributes(['to_point_id' => $this->to_point_id])) {
            Yii::$app->session->setFlash('success', 'Пункт назначения успешно обновлен');
            return true;
        }

        Yii::$app->session->setFlash('error',
            'Ошибка при обновлении пункта назначения: ' .
            implode(', ', $order->getFirstErrors())
        );
        return false;
    }
}