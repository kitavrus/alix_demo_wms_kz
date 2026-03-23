<?php

namespace common\tests\_pages;

use yii\codeception\BasePage;

class EmployeeRegisterPage extends BasePage
{
    public $route = '/employee/default/index';
    public static $URL = '/employee/default/index';



    /**
     * @param string $username
     * @param string $password
     * @param string $email
     * @param string $manager_type
     * @param string $status
     */
    public function registerEmployee($username, $password, $email, $manager_type, $status)
    {
        $this->actor->fillField('#employees-username', $username);
        $this->actor->fillField('#employees-password', $password);
        $this->actor->fillField('#employees-email', $email);
        $this->actor->selectOption('#employees-manager_type', $manager_type);
        $this->actor->selectOption('#employees-status', $status);
        $this->actor->click('Создать', 'button');
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $email
     */
    public function changeEmployeeInfo($username, $password, $email)
    {
        $this->actor->fillField('#employees-username', $username);
        $this->actor->fillField('#employees-password', $password);
        $this->actor->fillField('#employees-email', $email);
        $this->actor->click('Сохранить изменения', '.btn-primary');
    }



}
