<?php
namespace stockDepartment\modules\alix\controllers\ecommerce\employee\domain\repository;


use stockDepartment\modules\alix\controllers\ecommerce\employee\domain\entities\EcommerceEmployee;

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

    public function getById($id)
    {
        return EcommerceEmployee::find()->andWhere(['id' => $id])->one();
    }
}