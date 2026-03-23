<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.10.14
 * Time: 10:49
 */
namespace common\ecommerce\main\service;

use common\modules\inbound\models\InboundOrder;
use common\modules\outbound\models\OutboundOrder;
use Yii;

class MailService extends \common\components\MailManager
{
    public function sendIfNewOutboundOrder($newOrderList)
    {
        if(empty($order)) {
           return null;
        }
        $emails = [];
        $emails[] = 'kitavrus@ya.ru';
        $emails[] = 'ipotema@nomadex.kz';

        $subject = 'Новые заказы ';

        return $this->sendMessage($emails, $subject,
            'ecommerce/if-new-outbound-order', [
                'newOrderList' => $newOrderList,
            ]
        );
    }
}