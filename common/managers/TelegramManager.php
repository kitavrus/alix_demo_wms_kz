<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 27.01.2017
 * Time: 9:26
 */

namespace common\managers;


use yii\base\Exception;

class TelegramManager
{
    const TOKEN = '319006042:AAFIBKQdz8pOxPQ_J23puSy3zD2EJ_6Uv7A';
    const URL = 'https://api.telegram.org';

    public static function sendMessage($chatId, $text)
    {
        try {
            file_get_contents(static::makeUrl($chatId, $text, 'sendMessage'));
            file_put_contents('TelegramManager.log',$chatId.';'.$text.';'."\n",FILE_APPEND);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    protected static function makeUrl($chatId, $text, $method)
    {
        return self::URL . '/bot' . self::TOKEN . '/' . $method . '?chat_id=' .$chatId.'&text='.urlencode($text);
    }
}