<?php

namespace common\tests\_pages;

use yii\codeception\BasePage;

class LoginPage extends BasePage
{
    public $route = 'user/login';
    public static $usernameField = '#login-form-login';
    public static $passwordField = '#login-form-password';


    /**
     * @param string $username
     * @param string $password
     */
    public function login($username, $password)
    {
        $this->actor->fillField(self::$usernameField, $username);
        $this->actor->fillField(self::$passwordField, $password);
        $this->actor->click('Авторизоваться', '.btn');
        $this->actor->expectTo('see that user is logged');
        $this->actor->seeLink('Выход('.$username.')');
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
