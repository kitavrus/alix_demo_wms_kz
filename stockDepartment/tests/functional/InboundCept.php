<?php

use stockDepartment\tests\_pages\LoginPage;

$I = new TestGuy($scenario);
$I->wantTo('inbound testing');

$loginPage = LoginPage::openBy($I);
$I->see('Авторизоваться', '.panel-title');
$I->amGoingTo('try to login with correct credentials');
$loginPage->login('test01', 'test01');

$I->expectTo('see project index page');
$I->see('NMDX WMS', '.navbar-brand');
$I->canSeeInCurrentUrl('outbound/default/operation-report');
$I->seeLink('Выход(test01)','user/logout');

$I->amGoingTo('go to Warehouse Distribution');
$I->seeLink('Распределение','/warehouseDistribution/default/index');
$I->click('Распределение', 'a');
//$I->amOnPage('/warehouseDistribution/default/index');
$I->seeInTitle('Распределение');
$I->amGoingTo('select DeFacto');
//$I->seeElement('#main-form-client-id');
//$I->seeElement('select[name=client_id]');
//$I->selectOption('select', 'DeFacto');
//$I->selectOption('select[name=client_id]', 'DeFacto');
//$I->dontSeeOptionIsSelected('select[name=client_id]', 'DeFacto');
$I->seeInCurrentUrl('warehouseDistribution/default/index');
//sleep(1);
$I->seeElement('select[name=client_id]');
//\yii\helpers\VarDumper::dump($I->grabValueFrom());
$I->selectOption('select[name=client_id]', "DeFacto");
//$I->selectOption('#main-form-client-id', "DeFacto");
//$I->seeOptionIsSelected('client_id','');
//$I->seeOptionIsSelected('select[name=client_id]','');
//$I->selectOption('select[name=client_id]', 2);
//$I->selectOption('select[name=client_id]', 2);
//$I->click('#main-form-client-id option[value="2"]');
//$I->amOnPage('/warehouseDistribution/default/route-form?id=2');
//$I->seeInCurrentUrl('warehouseDistribution/default/route-form?id=2');
//$I->amOnPage('/warehouseDistribution/default/route-form?id=2');

//$I->selectOption('option[value="1"]', 'DeFacto');
//$I->selectOption('#main-form-client-id', 'DeFacto');
//$I->selectOption('#main-form-client-id', 'DeFacto');
//$I->see('Распределение || DeFacto', 'title');



