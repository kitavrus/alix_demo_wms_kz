<?php

/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 31.05.2016
 * Time: 13:34
 */
namespace common\formatter;

use Yii;
use yii\i18n\Formatter;

class MeFormatter extends Formatter
{
    /*
     * Formatting phone
     * @param string $phone
     * @param string $country
     * @param boolean $prefix
     * @return string formatted Phone
     * */
    public function asPhone ($phone, $country = null, $prefix = true)
    {
        switch ((is_null($country) ? $this->locale : $country)) {
            case 'ru': {
                return $this->asPhoneRu($phone, $prefix);
            }
            case 'en':
            default: {
                return $this->asPhoneRu($phone, $prefix);
            }
        }
    }
    /*
     *
     * */
    private function asPhoneRu ($phone, $prefix)
    {
        $phone_clear = $this->asPhoneClear($phone);
        switch (intval(strlen($phone_clear))) {
            case 11: {
                $phone = '' . $phone_clear;
                break;
            }
            case 10: {
                $phone = '8' . $phone_clear;
                break;
            }
            default: {
                return '';
            }
        }
        $n = strval($phone);
        return ($prefix ? '+7' : '') . ' (' . $n[1] . $n[2] . $n[3] . ') ' . $n[4] . $n[5] . $n[6] . '-' . $n[7] . $n[8] . '-' . $n[9] . $n[10];
    }
    /*
     *
     * */
    public function asPhoneClear ($phone_raw, $prefix = false)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone_raw);
        if (!$prefix && (strlen($phone) > 10)) {
            $phone = substr($phone, 1);
        }
        return $phone;
    }
}