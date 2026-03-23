<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.07.2017
 * Time: 9:17
 */

namespace common\ecommerce\defacto\outbound\validation;


use common\ecommerce\defacto\employee\service\EmployeeService;
use common\ecommerce\entities\EcommerceEmployee;

class ValidationPartReReserved
{
    private $validationOutbound;
    private $employeeService;

    public function __construct() {
        $this->validationOutbound = new ValidationOutbound();
        $this->employeeService = new EmployeeService();
    }

    public function getOrderByPickList($pickList) {
        return $this->validationOutbound->getOrderByPickList($pickList);
    }

    public function getPickListByBarcode($pickListBarcode) {
        return $this->validationOutbound->getPickListByBarcode($pickListBarcode);
    }

    public function isValidPickingList($pickList) {
        return  $this->validationOutbound->isValidPickingList($pickList);
    }

    public function isNotDoneOrder($pickList) {
        return  $this->validationOutbound->isNotDoneOrder($pickList);
    }

    public function isEmployeeBarcode($employeeBarcode) {
        return  $this->employeeService->isEmployee($employeeBarcode);
    }
}