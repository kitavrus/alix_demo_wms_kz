<?php

namespace stockDepartment\modules\alix\controllers\common\notify\b2b;


class TelegramIntermodeB2BNotification
{
    const INTERMODE_GROUP_ID = '-4617032601';

    /**
     * @param $data NewInboundOrderMsgDTO
	 * @return boolean
	 */
    public static function sendMessageIfNewInboundOrder($data)
    {
        $message  = 'Новый заказ: '.$data->orderNumber."\n";
		$message .= 'товаров: '.$data->expectedProductQty."\n";
		$message .= 'комментарий: '.$data->comment."\n";

        return TelegramIntermodeB2BManager::sendMessage(self::INTERMODE_GROUP_ID,$message);
    }

    /**
     * @param $data NewOutboundOrderMsgDTO
	 * @return boolean
	 */
    public static function sendMessageIfNewOutboundOrder($data)
    {
        $message  = 'Новый заказ: '.$data->orderNumber."\n";
		$message .= 'товаров: '.$data->expectedProductQty."\n";
		$message .= 'магазин: '.$data->storeName."\n";
		$message .= 'комментарий: '.$data->comment."\n";

        return TelegramIntermodeB2BManager::sendMessage(self::INTERMODE_GROUP_ID,$message);
    }

    /**
     * @param $data CompleteOutboundOrderMsgDTO
	 * @return boolean
	 */
    public static function sendMessageIfCompletedOutboundOrder($data)
    {
        $message  = 'Заказ статус "Completed": '.$data->orderNumber."\n";
		$message .= 'API Response message: '.$data->logMessage."\n";

        return TelegramIntermodeB2BManager::sendMessage(self::INTERMODE_GROUP_ID,$message);
    }
}