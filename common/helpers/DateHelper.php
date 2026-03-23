<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 19.09.14
 * Time: 15:15
 */

namespace common\helpers;

use Yii;
//use stockClient\modules\user\models\User;
use yii\helpers\VarDumper;

class DateHelper {

    /*
     * Возвращает текущую метку времени
     * @return mixed
     **/
    public static function getTimestamp()
    {
        $date = new \DateTime();

        return $date->getTimestamp();
    }


    /*
     * Форматирует дату с KZ часовым поясом в
     * timestamp по GMT 00:00
     * @return mixed
     **/
    public static function formatFormDate($date)
    {
        $date = new \DateTime($date);
        $date->modify('-6 hour');

        return $date->getTimestamp();
    }

    /*
     * Форматирует дату для часового пояса Defacto (TR)
     * @param string date
     * @return timestamp
     **/
    public static function formatDefactoDate($date)
    {
        $date = new \DateTime($date);
        $date->modify('-2 hour');

        return $date->getTimestamp();
    }

}