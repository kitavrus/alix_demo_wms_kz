<?php

use common\tests\_pages\LoginPage;
use common\tests\_pages\EmployeeRegisterPage;

$I = new WebGuy($scenario);
$I->wantTo('create new employee');
$login = 'test_employee'.rand(1,255);
$email = $login.'@test.com';
$loginPage = LoginPage::openBy($I);


//login
$I->amGoingTo('login');
$I->canSeeInCurrentUrl('user/login');
$loginPage->login('Ferze', '9202012');
$I->expectTo('see that user is logged');
$I->seeLink('Выход(Ferze)');

//employee module
$I->amGoingTo('employee module');
$registerPage = new EmployeeRegisterPage($I);
$I->amOnPage($registerPage::$URL);
$I->canSeeInCurrentUrl('/employee/default/index');

//employee creation page
$I->amGoingTo('employee creation page');
$I->click('Добавление нового сотрудника', '.btn-success');
$I->expectTo('see creation page');
$I->canSeeInCurrentUrl('/employee/default/create');

//employee creation page
$I->amGoingTo('create employee');
$registerPage->registerEmployee(
    $login,
    'password',
    $email,
    '2',
    '1'
);
$I->canSeeInCurrentUrl('employee/default/view');

//save update link
$I->expectTo('save update user link');
$update_url = $I->grabAttributeFrom('.btn-primary', 'href');

//logout
$I->amGoingTo('logout');
$loginPage->logout('Ferze');
$I->resetCookie('PHPSESSID');
$loginPage = LoginPage::openBy($I);
$I->canSeeInCurrentUrl('user/login');


//try to login with new employee account data
$I->amGoingTo('login with created account');
$loginPage->login($login, 'password');
$I->expectTo('see that user is logged');
$I->seeLink('Выход('.$login.')');

//try to change account data
$I->amGoingTo('change account data');
$I->amOnPage($update_url);
$I->canSeeInCurrentUrl('employee/default/update');
$registerPage->changeEmployeeInfo($login, 'changedpassword', $email);
$I->canSeeInCurrentUrl('employee/default/view');

//logout
$I->amGoingTo('logout');
$loginPage->logout($login);
$loginPage = LoginPage::openBy($I);
$I->canSeeInCurrentUrl('user/login');

//try to login with changed account data
$I->amGoingTo('login with changed password');
$loginPage->login($login, 'changedpassword');
$I->expectTo('see that user is logged');
$I->seeLink('Выход('.$login.')');



