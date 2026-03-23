<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 03.08.2019
 * Time: 13:19
 */

namespace common\ecommerce\defacto\employee\repository;


use common\ecommerce\entities\EcommerceEmployee;

class EmployeeRepository
{
    /**
     * Is employee
     * @param string $barcode
     * @return boolean
      * */
    public static function isEmployee($barcode)
    {
        $barcode = trim($barcode);
        return EcommerceEmployee::find()->andWhere(['barcode'=>$barcode])->exists();
    }

    public static function getEmployeeByBarcode($barcode)
    {
        return EcommerceEmployee::find()->andWhere([
            'barcode' => $barcode
        ])->one();
    }
}