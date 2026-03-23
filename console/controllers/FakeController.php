<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 8/5/14
 * Time: 6:17 PM
 */

namespace console\controllers;

use common\models\ActiveRecord;
use common\modules\billing\models\TlDeliveryProposalBilling;
use common\modules\billing\models\TlDeliveryProposalBillingConditions;
use common\modules\client\models\Client;
use common\modules\client\models\ClientEmployees;
use common\modules\employees\models\Employees;
use common\modules\store\models\Store;
use common\modules\transportLogistics\models\TlAgents;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteCars;
use common\modules\transportLogistics\models\TlDeliveryRoutes;
use common\modules\user\models\User;
use Yii;
use yii\console\Controller;
use Faker\Factory as Faker;
use yii\base\Event;

class FakeController extends Controller
{
    public function actionGenerateFakeCostValues()
    { // fake\generate-fake-cost-values
        return 0;
        $faker = Faker::create('ru_RU');
        $fakerEn_US = Faker::create();
        $min = 10;
        $max = '1000.000';
        $maxDecimals = 3;
        Event::off(ActiveRecord::className(), ActiveRecord::EVENT_AFTER_UPDATE);
        Event::off(ActiveRecord::className(), ActiveRecord::EVENT_BEFORE_UPDATE);
        Event::off(TlDeliveryProposal::className(), TlDeliveryProposal::EVENT_RECALCULATE);


//        $countDP = TlDeliveryProposal::find()->count();
//        $countRC = TlDeliveryProposalRouteCars::find()->count();
//        $countDR = TlDeliveryRoutes::find()->count();

//        TlDeliveryProposal::deleteAll('id < :id', [':id'=> $countDP - 300]);
//        TlDeliveryProposalRouteCars::deleteAll('id < :id', [':id'=> $countRC - 300]);
//        TlDeliveryRoutes::deleteAll('id < :id', [':id'=> $countDR - 300]);

        $deliveryProposals = TlDeliveryProposal::find()->all();

        echo 'Delivery Proposal fake data fill START. Please wait... '. "\n";
        foreach ($deliveryProposals as $dp){
            $dp->detachBehaviors();
            $dp->car_price_invoice = $faker->randomFloat($maxDecimals, $min, $max);
            $dp->car_price_invoice_with_vat = $faker->randomFloat($maxDecimals, $min, $max);
            $dp->price_invoice = $faker->randomFloat($maxDecimals, $min, $max);
            $dp->price_invoice_with_vat = $faker->randomFloat($maxDecimals, $min, $max);
            $dp->price_expenses_cache = $faker->randomFloat($maxDecimals, $min, $max);
            $dp->price_expenses_with_vat = $faker->randomFloat($maxDecimals, $min, $max);
            $dp->price_our_profit = $faker->randomFloat($maxDecimals, $min, $max);
//            $dp->save(false);
            echo "*";
        }
        echo "\n".'Delivery Proposal fake data fill END'. "\n";

        $billings = TlDeliveryProposalBilling::find()->all();
        echo 'Delivery Proposal Billing fake data fill START. Please wait... '. "\n";
        foreach ($billings as $b){
            $b->detachBehaviors();
            $b->price_invoice = $faker->randomFloat($maxDecimals, $min, $max);
            $b->price_invoice_with_vat = $faker->randomFloat($maxDecimals, $min, $max);
            $b->price_invoice_kg = $faker->randomFloat($maxDecimals, $min, $max);
            $b->price_invoice_kg_with_vat = $faker->randomFloat($maxDecimals, $min, $max);
            $b->price_invoice_mc = $faker->randomFloat($maxDecimals, $min, $max);
            $b->price_invoice_mc_with_vat = $faker->randomFloat($maxDecimals, $min, $max);
//            $b->save(false);
            echo "*";
        }
        $billingCondition = TlDeliveryProposalBillingConditions::find()->all();
        foreach ($billingCondition as $b){
            $b->detachBehaviors();
            $b->price_invoice = $faker->randomFloat($maxDecimals, $min, $max);
            $b->price_invoice_with_vat = $faker->randomFloat($maxDecimals, $min, $max);
//            $b->save(false);
            echo "*";
        }
        echo "\n".'Delivery Proposal Billing fake data fill END'. "\n";

        $deliveryCars = TlDeliveryProposalRouteCars::find()->all();
        echo "\n".'Delivery Proposal Route Car fake data fill START. Please wait... '. "\n";
        foreach ($deliveryCars as $dc){
            $dc->detachBehaviors();
            $dc->price_invoice = $faker->randomFloat($maxDecimals, $min, $max);
            $dc->price_invoice_with_vat = $faker->randomFloat($maxDecimals, $min, $max);
//            $dc->save(false);
            echo "*";
        }
        echo "\n".'Delivery Proposal Route Car fake data fill END'. "\n";

        $deliveryRoutes = TlDeliveryRoutes::find()->all();
        echo 'Delivery Routes fake data fill START. Please wait... '. "\n";
        foreach ($deliveryRoutes as $dr){
            $dr->detachBehaviors();
            $dr->price_invoice = $faker->randomFloat($maxDecimals, $min, $max);
            $dr->price_invoice_with_vat = $faker->randomFloat($maxDecimals, $min, $max);
//            $dr->save(false);
            echo "*";
        }
        echo "\n".'Delivery Routes fake data fill END'. "\n";

        echo 'Client fake data fill START. Please wait... '. "\n";
        $queryClients = Client::find()->all();

        foreach($queryClients as $queryClient) {
            $queryClient->username = $fakerEn_US->firstName.'-'.$fakerEn_US->randomDigitNotNull;
            $queryClient->full_name = $faker->firstName;
            $queryClient->legal_company_name = $faker->company;
            $queryClient->title = $queryClient->legal_company_name;
            $queryClient->first_name = $faker->firstName;
            $queryClient->middle_name = $faker->middleNameMale();
            $queryClient->last_name = $faker->lastName;
            $queryClient->phone = $faker->phoneNumber;
            $queryClient->phone_mobile = $faker->phoneNumber;
            $queryClient->email = $faker->email;
//            $queryClient->save(false);
            echo "*";
        }

        echo "\n".'Client Employees fake data fill START. Please wait... '. "\n";
        $queryClientEmployees = ClientEmployees::find()->all();

        foreach($queryClientEmployees as $queryClientEmployee) {
            $queryClientEmployee->username = $fakerEn_US->firstName.'-'.$fakerEn_US->randomDigitNotNull;
            $queryClientEmployee->full_name = $queryClientEmployee->username;
            $queryClientEmployee->first_name = $faker->firstName;
            $queryClientEmployee->middle_name = $faker->middleNameMale();
            $queryClientEmployee->last_name = $faker->lastName;
            $queryClientEmployee->phone = $faker->phoneNumber;
            $queryClientEmployee->phone_mobile = $faker->phoneNumber;
            $queryClientEmployee->email = $faker->email;
//            $queryClientEmployee->save(false);
            echo "*";
        }

        echo "\n".'Employees fake data fill START. Please wait... '. "\n";
        $queryEmployees = Employees::find()->all();

        foreach($queryEmployees as $queryEmployee) {
            $queryEmployee->username = $fakerEn_US->firstName.'-'.$fakerEn_US->randomDigitNotNull;
            $queryEmployee->barcode = $faker->ean13();
            $queryEmployee->title = $queryEmployee->username;
            $queryEmployee->first_name = $faker->firstName;
            $queryEmployee->middle_name = $faker->middleNameMale();
            $queryEmployee->last_name = $faker->lastName;
            $queryEmployee->phone = $faker->phoneNumber;
            $queryEmployee->phone_mobile = $faker->phoneNumber;
            $queryEmployee->email = $faker->email;
//            $queryEmployee->save(false);
            echo "*";
        }

        echo "\n".'User fake data fill START. Please wait... '. "\n";
        $queryUsers = User::find()->all();

        foreach($queryUsers as $queryUser) {
            $queryUser->username = $fakerEn_US->firstName.'-'.$fakerEn_US->randomDigitNotNull.''.$fakerEn_US->randomDigit;
            $queryUser->email = $faker->email;
//            $queryUser->save(false);
            echo "*";
        }

        echo "\n".'Agents fake data fill START. Please wait... '. "\n";
        $queryAgents = TlAgents::find()->all();

        foreach($queryAgents as $queryAgent) {
            $queryAgent->name = $fakerEn_US->company;
            $queryAgent->title = $queryAgent->username;
            $queryAgent->contact_first_name = $faker->firstName;
            $queryAgent->contact_middle_name = $faker->middleNameMale();
            $queryAgent->contact_last_name = $faker->lastName;
            $queryAgent->phone = $faker->phoneNumber;
            $queryAgent->phone_mobile = $faker->phoneNumber;
//            $queryAgent->save(false);
            echo "*";
        }

        echo "\n".'Store fake data fill START. Please wait... '. "\n";
        $queryStores = Store::find()->all();

        foreach($queryStores as $queryStore) {
            $queryStore->name = $fakerEn_US->company;
            $queryStore->legal_point_name = $queryStore->name;
            $queryStore->title = $queryStore->username;
            $queryStore->contact_first_name = $faker->firstName;
            $queryStore->contact_middle_name = $faker->middleNameMale();
            $queryStore->contact_last_name = $faker->lastName;
            $queryStore->phone = $faker->phoneNumber;
            $queryStore->phone_mobile = $faker->phoneNumber;
            $queryStore->email = $faker->email;
            $queryStore->shopping_center_name = '';
            $queryStore->shopping_center_name_lat = '';
//            $queryStore->save(false);
            echo "*";
        }


    }

} 