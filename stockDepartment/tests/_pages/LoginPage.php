<?php

namespace stockDepartment\tests\_pages;

use yii\codeception\BasePage;

class LoginPage extends BasePage
{
    public $route = 'user/login';


    /**
     * @param string $username
     * @param string $password
     */
    public function login($username, $password)
    {
        $this->actor->fillField('#login-form-login', $username);
        $this->actor->fillField('#login-form-password', $password);
        $this->actor->click('button');
    }

    /**
     * @param string $username
     * @param string $password
     */
    public function logout($username)
    {
        $this->actor->click('Выход('.$username.')');
        $this->actor->resetCookie('PHPSESSID');
    }
}
