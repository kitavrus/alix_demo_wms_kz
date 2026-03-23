<?php

namespace stockDepartment\modules\alix\controllers\common\notify;


use yii\base\Exception;

class TelegramIntermodeB2CManager
{
    const TOKEN = '8113670405:AAG2RennUDtcaf8rl6i_5sUMF0zblHv2KsY';
    const URL = 'https://api.telegram.org';

    public static function sendMessage($chatId, $text)
    {
        try {
            file_get_contents(static::makeUrl($chatId, $text, 'sendMessage'));
            file_put_contents('TelegramIntermodeB2CManager.log',$chatId.';'.$text.';'."\n",FILE_APPEND);
        } catch (Exception $e) {
			file_put_contents('TelegramIntermodeB2CManager.log',$chatId.';'.$text.';'.print_r($e,true)."\n",FILE_APPEND);
            return false;
        }
        return true;
    }

    protected static function makeUrl($chatId, $text, $method)
    {
        return self::URL . '/bot' . self::TOKEN . '/' . $method . '?chat_id=' .$chatId.'&text='.urlencode($text);
    }
}