<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 27.01.2017
 * Time: 9:26
 */

namespace common\managers;


class TelegramDefactoNotification
{
    const DEFACTO_CHAT_ID = '-210057119';

    public static function sendInboundMessageIfNewOrder($data)
    {
        $partyNumber =  isset($data['partyNumber'])?$data['partyNumber']:'';
        $orderNumber =  isset($data['orderNumber'])?$data['orderNumber']:'';
        $message  = 'Новая приходная накладная: '.$partyNumber." ".$orderNumber."\n";

        return TelegramManager::sendMessage(self::DEFACTO_CHAT_ID,$message);
    }

    public static function sendInboundMessageIfPreparedOrder($data)
    {
        $partyNumber =  isset($data['partyNumber'])?$data['partyNumber']:'';
        $orderNumber =  isset($data['orderNumber'])?$data['orderNumber']:'';
        $message  = 'Приходная накладная готова для загрузки: '.$partyNumber." ".$orderNumber."\n";

        return TelegramManager::sendMessage(self::DEFACTO_CHAT_ID,$message);
    }

    public static function sendInboundMessage($data)
    {
        $partyNumber =  isset($data['partyNumber'])?$data['partyNumber']:'';
        $orderNumber =  isset($data['orderNumber'])?$data['orderNumber']:'';
        $boxQty      =  isset($data['boxQty'])?$data['boxQty']:'';

        $message  = 'Приходная накладная: '.$partyNumber." ".$orderNumber."\n";
        $message .= 'кол-во коробов: '.$boxQty."\n";

        return TelegramManager::sendMessage(self::DEFACTO_CHAT_ID,$message);
    }

    public static function sendOutboundMessageIfNewOrder($data)
    {
        $partyNumber =  isset($data['partyNumber'])?$data['partyNumber']:'';
        $message  = 'Новая сборка: '.$partyNumber."\n";

        return TelegramManager::sendMessage(self::DEFACTO_CHAT_ID,$message);
    }



    public static function sendOutboundMessage($data)
    {
        $partyNumber =  isset($data['partyNumber'])?$data['partyNumber']:'';
        $lotQty =  isset($data['lotQty'])?$data['lotQty']:'';

        $message  = 'Сборка: '.$partyNumber."\n";
        $message  .= 'кол-во лотов: '.$lotQty."\n";

        return TelegramManager::sendMessage(self::DEFACTO_CHAT_ID,$message);
    }
	
	public static function sendQtyBoxOnMezaninMessage($message)
    {
        //$message =  "Это тест! : "."\n";
        //$message .=  "кол-во возвратных коробов на мезанине : ".$data->returnBoxCount."\n";
        //$message .=  "кол-во обычных коробов на мезанине : ".$data->simpleBoxCount."\n";

        return TelegramManager::sendMessage(self::DEFACTO_CHAT_ID,$message);
    }
	
}