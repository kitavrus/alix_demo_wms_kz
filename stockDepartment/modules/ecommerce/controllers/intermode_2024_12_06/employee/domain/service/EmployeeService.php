<?php
namespace app\modules\ecommerce\controllers\intermode\employee\domain\service;
namespace app\modules\ecommerce\controllers\intermode\employee\domain\repository\EmployeeRepository;

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