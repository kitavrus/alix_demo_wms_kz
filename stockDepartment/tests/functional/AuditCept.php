<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 08.01.2015
 * Time: 16:14
 */
use common\tests\_pages\LoginPage;

$I = new TestGuy($scenario);
$I->wantTo('audit table test');

$value = 'changedmail'.rand(1,255).'@test.com';
//login
$loginPage = LoginPage::openBy($I);
$I->amGoingTo('try to login with correct credentials');
$loginPage->login('Ferze', '9202012');
$I->expectTo('see that user is logged');
$I->seeLink('Выход(Ferze)');

$I->amGoingTo('update some record');
$I->amOnPage('store/default/update?id=1');

$I->amGoingTo('save old value');
$old_value = $I->grabValueFrom("#store-email");
$I->fillField('#store-email', $value);
$I->click('Сохранить изменения', '.btn-primary');

$I->amGoingTo('check audit record');
$I->canSeeInCurrentUrl('store/default/view');
$I->seeRecord('common\modules\store\models\StoreAudit', array('parent_id' => '1', 'field_name'=>'email', 'before_value_text'=>$old_value, 'after_value_text'=>$value));

