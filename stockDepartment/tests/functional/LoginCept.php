<?php

use stockDepartment\tests\_pages\LoginPage;

$I = new TestGuy($scenario);
$I->wantTo('authorization page testing');

$loginPage = LoginPage::openBy($I);

$I->wantTo('authorization page testing');

$I->expectTo('see that app is work');
$I->see('Авторизоваться', '.panel-title');

//$I->amGoingTo('try to submit empty form');
//$loginPage->login('', '');
//$I->expectTo('see validation errors');
//$I->see('Необходимо заполнить «Логин».', '.help-block');
//$I->see('Необходимо заполнить «Пароль».', '.help-block');
//
//$I->amGoingTo('try to login with wrong credentials');
//$I->expectTo('see validations errors');
//$loginPage->login('admin', 'wrong');
//$I->expectTo('see validations errors');
//$I->see('Неправильный логин или пароль', '.help-block');

$I->amGoingTo('try to login with correct credentials');
$loginPage->login('test01', 'test01');
//sleep(2);
$I->expectTo('see project index page');
$I->see('NMDX WMS', '.navbar-brand');
$I->seeInCurrentUrl('outbound/default/operation-report');
$I->seeLink('Выход(test01)','user/logout');

//$I->amGoingTo('try to logout');
//$loginPage->logout('test01');
//$I->expectTo('see that user is logout');
//sleep(2);
//$I->seeInCurrentUrl('user/login');
