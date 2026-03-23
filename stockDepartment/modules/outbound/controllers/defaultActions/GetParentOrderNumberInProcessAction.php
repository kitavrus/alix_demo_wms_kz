<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 02.03.15
 * Time: 14:51
 */

namespace app\modules\outbound\controllers\defaultActions;

use common\modules\stock\models\Stock;
use Yii;
use common\modules\outbound\models\OutboundOrder;
use yii\helpers\ArrayHelper;

/*
 * Get parent order number in process by client
 * @return JSON  dataOptions ['id'=>'parent order number title']
 * */
class GetParentOrderNumberInProcessAction extends \yii\base\Action
{
    public function run()
    {
        echo 'get-parent-order-number-in-process';
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $client_id = Yii::$app->request->post('client_id');

        $data = ['' => ''];
        $data += ArrayHelper::map(
            OutboundOrder::find()
            ->select('parent_order_number')
            ->where(['client_id'=>$client_id])
            ->andWhere(['NOT IN','status',[Stock::STATUS_OUTBOUND_ON_ROAD,Stock::STATUS_OUTBOUND_DELIVERED]])
            ->groupBy('parent_order_number')
            ->asArray()
            ->all(),'parent_order_number','parent_order_number');
        return [
            'dataOptions' => $data
        ];
    }
}