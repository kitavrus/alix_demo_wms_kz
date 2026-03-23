<?php
namespace app\modules\intermode\controllers\ecommerce\employee\domain\service;

use app\modules\intermode\controllers\ecommerce\employee\domain\repository\EmployeeRepository;

class EmployeeService
{
    private $repository;

    /**
     * EmployeeService constructor.
     */
    public function __construct()
    {
        $this->repository = new EmployeeRepository();
    }

    /**
     * Is employee
     * @param string $barcode
     * @return boolean
     * */
    public function isEmployee($barcode)
    {
        return EmployeeRepository::isEmployee($barcode);
    }
}