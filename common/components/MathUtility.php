<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 22.12.14
 * Time: 16:14
 */

namespace common\components;

use yii;

class MathUtility {

    /*
     * Prepared numbers for calculated
     * @param string|integer|float $value value in bytes to be formatted.
     * @param integer $decimals the number of digits after the decimal point
     * @param mix|float
     * */
    public static function prepare($n, $decimals = 2)
    {
        $f = Yii::$app->formatter;
        $f->decimalSeparator = '.';
        $f->thousandSeparator = '';

        return $f->asDecimal($n,$decimals);
    }
}