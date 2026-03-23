<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 25.11.2019
 * Time: 10:40
 */

namespace common\ecommerce\defacto\employee\service;

use common\ecommerce\defacto\employee\repository\EmployeeRepository;

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