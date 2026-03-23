<?php

namespace app\modules\intermode\controllers\common\notify\b2b;


use yii\base\Exception;

class TelegramIntermodeB2BManager
{
    const TOKEN = '7656729927:AAFnIwcE4j9skHSi2be_jZnUjDGrtpRsF2k';
    const URL = 'https://api.telegram.org';

    public static function sendMessage($chatId, $text)
    {
        try {
            file_get_contents(static::makeUrl($chatId, $text, 'sendMessage'));
            file_put_contents('TelegramIntermodeB2BManager.log',$chatId.';'.$text.';'."\n",FILE_APPEND);
        } catch (Exception $e) {
			file_put_contents('TelegramIntermodeB2BManager.log',$chatId.';'.$text.';'.print_r($e,true)."\n",FILE_APPEND);
            return false;
        }
        return true;
    }

    protected static function makeUrl($chatId, $text, $method)
    {
        return self::URL . '/bot' . self::TOKEN . '/' . $method . '?chat_id=' .$chatId.'&text='.urlencode($text);
    }
}