<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.10.14
 * Time: 10:49
 */
namespace common\clientObject\main\service;

use common\modules\inbound\models\InboundOrder;
use common\modules\outbound\models\OutboundOrder;
use Yii;

class MailService extends \common\components\MailManager
{
    public function sendMailIfClientUploadNewOrder($order)
    {
        if(empty($order)) {
           return null;
        }
        $emails = [];
//        $emails[] = 'kitavrus@ya.ru';
        $emails[] = 'ipotema@nomadex.kz';
        //$emails[] = 'bmambetsadykova@nomadex.kz';
        $emails[] = 'aamankeldy@nomadex.kz';
        $emails[] = 'm.yerbolatov@nomadex.kz';

        $subject = Yii::t('custom-mail', 'Клиент '.$order->clientName.' загрузил новую накладную : ').' '.$order->orderNumber;
        return $this->sendMessage($emails, $subject,
            'if-client-upload-new-order', [
                'order' => $order,
            ]
        );
    }

    public function makeDTOForInbound($inboundID)
    {
       $inbound = InboundOrder::find()->with('client')->andWhere(['id'=>$inboundID])->one();
        if($inbound) {
            $order = new \stdClass();
            $order->id = $inbound->id;
            $order->clientName = $inbound->client->legal_company_name;
            $order->orderNumber = $inbound->order_number;
            $order->comment = $inbound->comments;
            $order->isOrderType = 'inbound';
            return $order;
        }
       return null;
    }

    public function makeDTOForOutbound($outboundID)
    {
       $outbound = OutboundOrder::find()->with('client')->andWhere(['id'=>$outboundID])->one();
        if($outbound) {
            $order = new \stdClass();
            $order->id = $outbound->id;
            $order->clientName = $outbound->client->legal_company_name;
            $order->orderNumber = $outbound->order_number;
            $order->comment = $outbound->description;
            $order->isOrderType = 'outbound';
            return $order;
        }
       return null;
    }
}