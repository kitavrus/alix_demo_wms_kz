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
use common\modules\stock\models\Stock;

class iHelper {

    /*
     * Return true if client or false if user
     * @return boolean
     * */
//    public static function isClient()
//    {
//        $r = false;
//        if(!Yii::$app->user->isGuest) {
//            if('client' == Yii::$app->db->getSchema()->getRawTableName(Yii::$app->user->getIdentity()->tableName())) {
//            if($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {
//                VarDumper::dump($userModel,10,true);
//                die('');
//               switch($userModel->user_type) {
//                   case User::USER_TYPE_CLIENT:
//                       break;
//                   case User::USER_TYPE_STORE_MANAGER:
//                       break;
//               }
//
//
//                $r = true;
//            }
//        }
//        return $r;
//    }


    /*
     * Склоняет форму слова в зависимости от числа
     * @param int $count число
     * @param int $form1 форма 1
     * @param int $form2 форма 2
     * @param int $form3 форма 3
     * @return string
     **/
    public static function formatTextAfterNumber($count, $form1='день', $form2='дня', $form3='дней')
    {
        $count = abs($count) % 100;
        $lcount = $count % 10;
        if ($count >= 11 && $count <= 19) return($count.' '.$form3);
        if ($lcount >= 2 && $lcount <= 4) return($count.' '.$form2);
        if ($lcount == 1) return($count.' '.$form1);

        return $count.' '.$form3;
    }

    /*
     * Высчитывает разницу между датами в рабочих днях
     * @param mixed $start дата нaчала
     * @param mixed $end дата конца
     * @return int
     **/
    public static function calculateWorkingDayInterval($startDate, $endDate)
    {

            $start = new \DateTime($startDate);
            $end = new \DateTime($endDate);

            $interval = $start->diff($end);
            $workingDays = 0;
            for($i=0; $i<=$interval->d; $i++){
                $start->modify('+1 day');
                $weekday = $start->format('w');

                if($weekday != 0 && $weekday != 6){ // 0 for Sunday and 6 for Saturday
                    $workingDays++;
                }

            }

            return $workingDays;

        }

    /*
   * Высчитывает разницу между датами в рабочих днях
   * @param mixed $start дата нaчала
   * @param mixed $end дата конца
   * @return int
   **/
    public static function getStockGridColor($status)
    {

        switch($status) {
            case Stock::STATUS_OUTBOUND_FULL_RESERVED: //#FFA54F
                $class = 'color-tan';
                break;
            case Stock::STATUS_OUTBOUND_PART_RESERVED: //#FFA500
                $class = 'color-orange';
                break;
            case Stock::STATUS_OUTBOUND_PICKING: //#FFF68F
                $class = 'color-khaki';
                break;
            case Stock::STATUS_OUTBOUND_PICKED: //#CAFF70
                $class = 'color-dark-olive-green';
                break;
            case Stock::STATUS_OUTBOUND_SCANNING: //#87CEFA
                $class = 'color-light-sky-blue';
                break;
            case Stock::STATUS_OUTBOUND_SCANNED: //#1E90FF
                $class = 'color-dodger-blue';
                break;
            case Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST: //#FFFFE0
                $class = 'color-light-yellow';
                break;
            case Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API: //#EE82EE
                $class = 'color-violet ';
                break;
            case Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL: //#FF6A6A
            case Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL: //#FF6A6A
                $class = 'color-indian-red';
                break;
            case Stock::STATUS_OUTBOUND_COMPLETE: //#C6E2FF
                $class = 'color-slate-gray';
                break;
            default:
                $class = '';
                break;

        }
        return $class;
    }

    public static function transliterate($string){
        $translit = [

            'а' => 'a',   'б' => 'b',   'в' => 'v',

            'г' => 'g',   'д' => 'd',   'е' => 'e',

            'ё' => 'yo',   'ж' => 'zh',  'з' => 'z',

            'и' => 'i',   'й' => 'j',   'к' => 'k',

            'л' => 'l',   'м' => 'm',   'н' => 'n',

            'о' => 'o',   'п' => 'p',   'р' => 'r',

            'с' => 's',   'т' => 't',   'у' => 'u',

            'ф' => 'f',   'х' => 'x',   'ц' => 'c',

            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'shh',

            'ь' => '\'',  'ы' => 'y',   'ъ' => '\'\'',

            'э' => 'e\'',   'ю' => 'yu',  'я' => 'ya',


            'А' => 'A',   'Б' => 'B',   'В' => 'V',

            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',

            'Ё' => 'YO',   'Ж' => 'Zh',  'З' => 'Z',

            'И' => 'I',   'Й' => 'J',   'К' => 'K',

            'Л' => 'L',   'М' => 'M',   'Н' => 'N',

            'О' => 'O',   'П' => 'P',   'Р' => 'R',

            'С' => 'S',   'Т' => 'T',   'У' => 'U',

            'Ф' => 'F',   'Х' => 'X',   'Ц' => 'C',

            'Ч' => 'CH',  'Ш' => 'SH',  'Щ' => 'SHH',

            'Ь' => '\'',  'Ы' => 'Y\'',   'Ъ' => '\'\'',

            'Э' => 'E\'',   'Ю' => 'YU',  'Я' => 'YA'
            ];

        $string = str_replace([' ', '-'], '_', trim($string));

        return strtr($string, $translit);

    }
}