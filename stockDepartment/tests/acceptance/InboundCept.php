<?php

use stockDepartment\tests\_pages\LoginPage;

$I = new WebGuy($scenario);
$I->wantTo('inbound testing');

$loginPage = LoginPage::openBy($I);
$I->see('Авторизоваться', '.panel-title');
$I->amGoingTo('try to login with correct credentials');
$loginPage->login('test01', 'test01');

$I->expectTo('see project index page');
$I->see('NMDX WMS', '.navbar-brand');
$I->canSeeInCurrentUrl('outbound/default/operation-report');

$I->amGoingTo('go to Warehouse Distribution');
$I->amOnPage('/warehouseDistribution/default/index');
$I->see('Распределение', 'h1');

$I->amGoingTo('select DeFacto');
$I->selectOption('select', 'DeFacto');
$I->see('Распределение || DeFacto', 'title');



