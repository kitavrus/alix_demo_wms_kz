<?php

namespace stockDepartment\modules\alix\controllers\common\notify;


class TelegramIntermodeB2CNotification
{
    const INTERMODE_B2C_GROUP_ID = '-4684982524';

    public static function sendMessageIfNewOrder($data)
    {
        $orderNumber =  isset($data['orderNumber'])?$data['orderNumber']:'';
        $expectedQty =  isset($data['expectedQty'])?$data['expectedQty']:'';
        $message  = 'Новый заказ: '.$orderNumber."\n";
		$message .= 'товаров в заказе: '.$expectedQty."\n";

        return TelegramIntermodeB2CManager::sendMessage(self::INTERMODE_B2C_GROUP_ID,$message);
    }
}