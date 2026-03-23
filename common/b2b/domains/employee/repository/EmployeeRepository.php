<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 03.08.2019
 * Time: 13:19
 */

namespace common\b2b\domains\employee\repository;

use common\modules\employees\models\Employees;

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
        return Employees::find()->andWhere(['barcode'=>$barcode])->exists();
    }

    public static function getEmployeeByBarcode($barcode)
    {
        return Employees::find()->andWhere([
            'barcode' => $barcode
        ])->one();
    }

    public function getById($id)
    {
        return Employees::find()->andWhere(['id' => $id])->one();
    }
}