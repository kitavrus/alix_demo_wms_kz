<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 25.11.2019
 * Time: 10:40
 */

namespace common\b2b\domains\employee\service;

use common\b2b\domains\employee\repository\EmployeeRepository;

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