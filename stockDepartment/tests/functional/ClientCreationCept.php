<?php
use common\tests\_pages\LoginPage;

$I = new TestGuy($scenario);
$I->am("Backend user");
$I->wantTo('check client creation process with correct insertion to DB table');
$login = 'testclient'.rand(1,9999);
$email = $login.'@test.com';

$I->amGoingTo('login');
$loginPage = LoginPage::openBy($I);
$loginPage->login("Ferze", '9202012');

$I->amGoingTo('client module');
$I->amOnPage('client/default/index');
$I->seeInCurrentUrl('client/default/index');

$I->amGoingTo('client creation page');
$I->click('Добавление нового клиента', '.btn');
$I->seeInCurrentUrl('client/default/create');

$I->amGoingTo('add new client');
$I->fillField('#client-username', $login);
$I->fillField('#client-legal_company_name', "Dummy client company");
$I->fillField('#client-title', "Fresh created ".$login);
$I->fillField('#client-email', $email);
$I->fillField('#client-password', "password");
$I->selectOption('#client-status', 1);
$I->click('Создать', 'button');

$I->expect('client was created and i am going to view page, also 3 AR records was created: User, Client, ClientEmployee');
$I->seeInCurrentUrl('client/default/view');
$I->seeRecord('common\modules\client\models\Client', array('username' => $login, 'email'=>$email));
$I->seeRecord('common\modules\client\models\ClientEmployees', array('username' => $login, 'email'=>$email));
$I->seeRecord('common\modules\user\models\User', array('username' => $login, 'email'=>$email));
