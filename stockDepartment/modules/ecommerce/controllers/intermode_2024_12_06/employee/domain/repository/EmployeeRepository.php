<?php
namespace app\modules\ecommerce\controllers\intermode\employee\domain\repository;


use app\modules\ecommerce\controllers\intermode\employee\domain\entities\EcommerceEmployee;

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