<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 8/5/14
 * Time: 6:17 PM
 */

namespace console\controllers;

use app\modules\inbound\inbound;
use common\modules\billing\components\BillingManager;
use common\modules\billing\models\TlDeliveryProposalBilling;
use common\modules\client\models\Client;
use common\modules\client\models\ClientEmployees;
use common\modules\outbound\models\OutboundOrder;
use common\modules\stock\models\Stock;
use common\modules\store\models\Store;
use common\modules\store\models\StoreReviews;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteCars;
use common\modules\transportLogistics\models\TlDeliveryRoutes;
use Yii;
use yii\console\Controller;
use yii\db\Schema;
use yii\helpers\VarDumper;
use common\helpers\DateHelper;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteUnforeseenExpenses;

class DateController extends Controller
{
    /**
     * Выполняет модификацию даты для таблицы
     * OutboundOrders
     *
     */
    public function actionChangeOutboundOrders()
    {
         return 0;
        echo 'change-outbound-order-dates start' . "\n";
        $i=0;
        $records = OutboundOrder::find()->all();

        foreach ($records as $record){
            echo '--------------Current date change start record ID '.$record->id.'-----------------'."\n";
            if(!empty($record->data_created_on_client)){
                echo 'Old value client created = '.$record->data_created_on_client."\n";
                //Отнимаем 6 часов от даты клиента и конвертируем ее в метку времени
                $newDateClient = new \DateTime($record->data_created_on_client);
                $newDateClient->modify('-6 hours');
                $record->data_created_on_client = $newDateClient->getTimestamp();
                $record->save(false);
                echo 'New value client created = '.$record->data_created_on_client."\n";
            }

            if(!empty($record->date_delivered)){
                echo 'Old value date delivered = '.$record->date_delivered."\n";
                //отнимаем 6 часов от даты доставки и конвертируем ее в метку времени
                $newDateDelivered = new \DateTime($record->date_delivered);
                $newDateDelivered->modify('-6 hours');
                $record->date_delivered = $newDateDelivered->getTimestamp();
                $record->save(false);
                echo 'New value date delivered = '.$record->date_delivered."\n";
            }
            $i++;
            echo "\n";
            echo '--------------Current date change end record ID '.$record->id.'-----------------'."\n";
            echo "\n";
            echo "\n";
            echo "\n";
        }
        //Меняем тип ячеек с датами с VARCHAR на INT
        echo "Start change columns type";
        echo "\n";
        $db = Yii::$app->db;
        $db->createCommand("ALTER TABLE `outbound_orders` CHANGE `data_created_on_client` `data_created_on_client` INT( 11 ) NULL DEFAULT NULL COMMENT 'Client order creation ts'")
            ->execute();
        $db->createCommand("ALTER TABLE `outbound_orders` CHANGE `date_delivered` `date_delivered` INT( 11 ) NULL DEFAULT NULL COMMENT 'Delivery ts'")
            ->execute();
        $db->createCommand("ALTER TABLE `outbound_orders` CHANGE `packing_date` `packing_date` INT( 11 ) NULL DEFAULT NULL COMMENT 'Print label ts'")
            ->execute();
        $db->createCommand("ALTER TABLE `outbound_orders` CHANGE `date_left_warehouse` `date_left_warehouse` INT( 11 ) NULL DEFAULT NULL COMMENT 'Print TTN ts'")
            ->execute();
        echo "End change columns type";
        echo "\n";

        echo "Start replace 0 to NULL";
        echo "\n";
        //заменяем 0 na null
        if($records = OutboundOrder::find()->all()){
            foreach ($records as $record){
                if(empty($record->data_created_on_client)){
                    $record->data_created_on_client = NULL;
                }
                if(empty($record->date_delivered)){
                    $record->date_delivered = NULL;
                }
                if(empty($record->packing_date)){
                    $record->packing_date = NULL;
                }
                if(empty($record->date_left_warehouse)){
                    $record->date_left_warehouse = NULL;
                }
                $record->save(false);
            }
        }
        echo "End replace 0 to NULL";
        echo "\n";

        echo $i . ' records was changed' . "\n";
        echo 'change-outbound-order-dates end' . "\n";
        return 0;
    }

    /**
     * Выполняет модификацию даты для таблицы
     * StoreReviews
     */
    public function actionChangeStoreReviews()
    {
        return 0;
        echo 'change-store-reviews-dates start' . "\n";
        $db = Yii::$app->db;
        $i=0;
        $itemp=0;
        //добавляем столбик для временного хранения даты
        $db->createCommand()->addColumn('store_reviews', 'temp_delivery_date', Schema::TYPE_INTEGER)
            ->execute();
        $records = StoreReviews::find()->all();


        foreach ($records as $record){
            //Конвертируем datetime в timestamp
            if(!empty($record->delivery_datetime)){
                echo '--------------Current date save start record ID '.$record->id.'-----------------'."\n";
                //отнимаем 6 часов от даты доставки и конвертируем ее в метку времени
                $newDateDelivered = new \DateTime($record->delivery_datetime);
                $newDateDelivered->modify('-6 hours');
                //Сохраняем timestamp во временную ячейку
                $record->temp_delivery_date = $newDateDelivered->getTimestamp();
                $record->save(false);
                $itemp++;
                echo 'Was: '.$record->delivery_datetime."\n";
                echo 'Converted and saved to: '.$record->temp_delivery_date."\n";
                echo '--------------Current date save end record ID '.$record->id.'-----------------'."\n";
            }
        }
        echo "Start change columns type";
        echo "\n";
        //меняем тип ячейки с datetime на int
        $db->createCommand("ALTER TABLE `store_reviews` CHANGE `delivery_datetime` `delivery_datetime` INT( 11 ) NULL DEFAULT NULL COMMENT 'Delivery date ts'")
            ->execute();
        echo "End change columns type";
        echo "\n";

        //переписываем значение из временной ячейки в измененную
        $records = StoreReviews::find()->all();
        foreach ($records as $record){

            if(!empty($record->delivery_datetime)){
                $record->delivery_datetime = $record->temp_delivery_date;
                $record->save(false);
                $i++;
            } elseif (empty($record->delivery_datetime)){
                $record->delivery_datetime = NULL;
                $record->save(false);
            }
        }


        echo "Drop temp column";
        echo "\n";
        //удаляем временную ячейку
        $db->createCommand()->dropColumn('store_reviews', 'temp_delivery_date')
            ->execute();

        echo $itemp . ' records was saved to temp column' . "\n";
        echo $i . ' final value records was saved' . "\n";
        echo 'change-store-reviews-dates end' . "\n";
        return 0;
    }

    /**
     * Выполняет модификацию даты для таблицы
     * StoreReviews
     */
    public function actionChangeDeliveryProposals()
    {
        return 0;
        echo 'change-delivery-proposal-dates start' . "\n";
        $db = Yii::$app->db;

        //добавляем столбики для временного хранения даты delivery_date expected_delivery_date  shipped_datetime  accepted_datetime
        $db->createCommand()->addColumn('tl_delivery_proposals', 'temp_delivery_date', Schema::TYPE_INTEGER)
            ->execute();
        $db->createCommand()->addColumn('tl_delivery_proposals', 'temp_expected_delivery_date', Schema::TYPE_INTEGER)
            ->execute();
        $db->createCommand()->addColumn('tl_delivery_proposals', 'temp_shipped_datetime', Schema::TYPE_INTEGER)
            ->execute();
        $db->createCommand()->addColumn('tl_delivery_proposals', 'temp_accepted_datetime', Schema::TYPE_INTEGER)
            ->execute();
        $records = TlDeliveryProposal::find()->all();


        foreach ($records as $record){
            //Конвертируем delivery date в timestamp
            if(!empty($record->delivery_date)){
                echo '--------------Current date save start record ID '.$record->id.'-----------------'."\n";
                //отнимаем 6 часов от даты доставки и конвертируем ее в метку времени
                $newDateDelivered = new \DateTime($record->delivery_date);
                $newDateDelivered->modify('-6 hours');
                //Сохраняем timestamp во временную ячейку
                $record->temp_delivery_date = $newDateDelivered->getTimestamp();

                echo 'delivery_date was: '.$record->delivery_date."\n";
                echo 'delivery_date converted and saved to: '.$record->temp_delivery_date."\n";
                echo '--------------Current date save end record ID '.$record->id.'-----------------'."\n";
            }
            //Конвертируем expected_delivery_date в timestamp
            if(!empty($record->expected_delivery_date)){
                echo '--------------Current date save start record ID '.$record->id.'-----------------'."\n";
                //отнимаем 6 часов от даты доставки и конвертируем ее в метку времени
                $newExDateDelivered = new \DateTime($record->expected_delivery_date);
                $newExDateDelivered->modify('-6 hours');
                //Сохраняем timestamp во временную ячейку
                $record->temp_expected_delivery_date = $newExDateDelivered->getTimestamp();

                echo 'expected_delivery_date was: '.$record->expected_delivery_date."\n";
                echo 'expected_delivery_date converted and saved to: '.$record->temp_expected_delivery_date."\n";
                echo '--------------Current date save end record ID '.$record->id.'-----------------'."\n";
            }
            //Конвертируем shipped_datetime в timestamp
            if(!empty($record->shipped_datetime)){
                echo '--------------Current date save start record ID '.$record->id.'-----------------'."\n";
                //отнимаем 6 часов от даты доставки и конвертируем ее в метку времени
                $newShippedDateDelivered = new \DateTime($record->shipped_datetime);
                $newShippedDateDelivered->modify('-6 hours');
                //Сохраняем timestamp во временную ячейку
                $record->temp_shipped_datetime = $newShippedDateDelivered->getTimestamp();

                echo 'shipped_datetime was: '.$record->shipped_datetime."\n";
                echo 'shipped_datetime converted and saved to: '.$record->temp_shipped_datetime."\n";
                echo '--------------Current date save end record ID '.$record->id.'-----------------'."\n";
            }
            //Конвертируем delivery date в timestamp
            if(!empty($record->accepted_datetime)){
                echo '--------------Current date save start record ID '.$record->id.'-----------------'."\n";
                //отнимаем 6 часов от даты доставки и конвертируем ее в метку времени
                $newAccDateDelivered = new \DateTime($record->accepted_datetime);
                $newAccDateDelivered->modify('-6 hours');
                //Сохраняем timestamp во временную ячейку
                $record->temp_accepted_datetime = $newAccDateDelivered->getTimestamp();

                echo 'accepted_datetime was: '.$record->accepted_datetime."\n";
                echo 'accepted_datetime converted and saved to: '.$record->temp_accepted_datetime."\n";
                echo '--------------Current date save end record ID '.$record->id.'-----------------'."\n";
            }

            $record->save(false);
        }
        echo "Start change columns type";
        echo "\n";
        //меняем тип ячейки с datetime на int
        $db->createCommand("ALTER TABLE `tl_delivery_proposals` CHANGE `delivery_date` `delivery_date` INT( 11 ) NULL DEFAULT NULL COMMENT 'Delivery date ts'")
            ->execute();
        $db->createCommand("ALTER TABLE `tl_delivery_proposals` CHANGE `expected_delivery_date` `expected_delivery_date` INT( 11 ) NULL DEFAULT NULL COMMENT 'Expected delivery date ts'")
            ->execute();
        $db->createCommand("ALTER TABLE `tl_delivery_proposals` CHANGE `shipped_datetime` `shipped_datetime` INT( 11 ) NULL DEFAULT NULL COMMENT 'Shipped date ts'")
            ->execute();
        $db->createCommand("ALTER TABLE `tl_delivery_proposals` CHANGE `accepted_datetime` `accepted_datetime` INT( 11 ) NULL DEFAULT NULL COMMENT 'Accepted date ts'")
            ->execute();
        echo "End change columns type";
        echo "\n";

        echo "Start write date from temp column. Please wait....";
        echo "\n";
        //переписываем значение из временной ячейки в измененную
        $records = TlDeliveryProposal::find()->all();
            foreach ($records as $record){
                    $flag = 0;
                if(!empty($record->delivery_date)){
                    $record->delivery_date = $record->temp_delivery_date;
                    $flag = 1;
                } elseif (empty($record->delivery_date)){
                    $record->delivery_date = NULL;
                    $flag = 1;
                }
                if(!empty($record->expected_delivery_date)){
                    $record->expected_delivery_date = $record->temp_expected_delivery_date;
                    $flag = 1;
                } elseif (empty($record->expected_delivery_date)){
                    $record->expected_delivery_date = NULL;
                    $flag = 1;
                }
                if(!empty($record->shipped_datetime)){
                    $record->shipped_datetime = $record->temp_shipped_datetime;
                    $flag = 1;
                } elseif (empty($record->shipped_datetime)){
                    $record->shipped_datetime = NULL;
                    $flag = 1;
                }
                if(!empty($record->accepted_datetime)){
                    $record->accepted_datetime = $record->temp_accepted_datetime;
                    $flag = 1;
                } elseif (empty($record->accepted_datetime)){
                    $record->accepted_datetime = NULL;
                    $flag = 1;

                }
                if($flag){
                    $record->save(false);
                }

        }
        echo "End write date from temp column";
        echo "\n";


        echo "Drop temp column";
        echo "\n";
        //удаляем временную ячейку
        $db->createCommand()->dropColumn('tl_delivery_proposals', 'temp_delivery_date')
            ->execute();
        $db->createCommand()->dropColumn('tl_delivery_proposals', 'temp_expected_delivery_date')
            ->execute();
        $db->createCommand()->dropColumn('tl_delivery_proposals', 'temp_shipped_datetime')
            ->execute();
        $db->createCommand()->dropColumn('tl_delivery_proposals', 'temp_accepted_datetime')
            ->execute();

        echo 'change-delivery-proposal-dates end' . "\n";
        return 0;
    }

    /**
     * Выполняет модификацию даты для таблицы
     * StoreReviews
     */
    public function actionChangeDeliveryProposalRoutes()
    {
        return 0;
        echo 'change-delivery-proposal-routes-dates start' . "\n";
        $db = Yii::$app->db;

        //добавляем столбики для временного хранения даты delivery_date expected_delivery_date  shipped_datetime  accepted_datetime
        $db->createCommand()->addColumn('tl_delivery_proposal_routes', 'temp_delivery_date', Schema::TYPE_INTEGER)
            ->execute();
        $db->createCommand()->addColumn('tl_delivery_proposal_routes', 'temp_shipped_datetime', Schema::TYPE_INTEGER)
            ->execute();
        $db->createCommand()->addColumn('tl_delivery_proposal_routes', 'temp_accepted_datetime', Schema::TYPE_INTEGER)
            ->execute();
        $records = TlDeliveryRoutes::find()->all();


        foreach ($records as $record){
            //Конвертируем delivery date в timestamp
            if(!empty($record->delivery_date)){
                echo '--------------Current date save start record ID '.$record->id.'-----------------'."\n";
                //отнимаем 6 часов от даты доставки и конвертируем ее в метку времени
                $newDateDelivered = new \DateTime($record->delivery_date);
                $newDateDelivered->modify('-6 hours');
                //Сохраняем timestamp во временную ячейку
                $record->temp_delivery_date = $newDateDelivered->getTimestamp();

                echo 'delivery_date was: '.$record->delivery_date."\n";
                echo 'delivery_date converted and saved to: '.$record->temp_delivery_date."\n";
                echo '--------------Current date save end record ID '.$record->id.'-----------------'."\n";
            }

            //Конвертируем shipped_datetime в timestamp
            if(!empty($record->shipped_datetime)){
                echo '--------------Current date save start record ID '.$record->id.'-----------------'."\n";
                //отнимаем 6 часов от даты доставки и конвертируем ее в метку времени
                $newShippedDateDelivered = new \DateTime($record->shipped_datetime);
                $newShippedDateDelivered->modify('-6 hours');
                //Сохраняем timestamp во временную ячейку
                $record->temp_shipped_datetime = $newShippedDateDelivered->getTimestamp();

                echo 'shipped_datetime was: '.$record->shipped_datetime."\n";
                echo 'shipped_datetime converted and saved to: '.$record->temp_shipped_datetime."\n";
                echo '--------------Current date save end record ID '.$record->id.'-----------------'."\n";
            }
            //Конвертируем delivery date в timestamp
            if(!empty($record->accepted_datetime)){
                echo '--------------Current date save start record ID '.$record->id.'-----------------'."\n";
                //отнимаем 6 часов от даты доставки и конвертируем ее в метку времени
                $newAccDateDelivered = new \DateTime($record->accepted_datetime);
                $newAccDateDelivered->modify('-6 hours');
                //Сохраняем timestamp во временную ячейку
                $record->temp_accepted_datetime = $newAccDateDelivered->getTimestamp();

                echo 'accepted_datetime was: '.$record->accepted_datetime."\n";
                echo 'accepted_datetime converted and saved to: '.$record->temp_accepted_datetime."\n";
                echo '--------------Current date save end record ID '.$record->id.'-----------------'."\n";
            }

            $record->save(false);
        }
        echo "Start change columns type";
        echo "\n";
        //меняем тип ячейки с datetime на int
        $db->createCommand("ALTER TABLE `tl_delivery_proposal_routes` CHANGE `delivery_date` `delivery_date` INT( 11 ) NULL DEFAULT NULL COMMENT 'Delivery date ts'")
            ->execute();
        $db->createCommand("ALTER TABLE `tl_delivery_proposal_routes` CHANGE `shipped_datetime` `shipped_datetime` INT( 11 ) NULL DEFAULT NULL COMMENT 'Shipped date ts'")
            ->execute();
        $db->createCommand("ALTER TABLE `tl_delivery_proposal_routes` CHANGE `accepted_datetime` `accepted_datetime` INT( 11 ) NULL DEFAULT NULL COMMENT 'Accepted date ts'")
            ->execute();
        echo "End change columns type";
        echo "\n";

        echo "Start write date from temp column. Please wait....";
        echo "\n";
        //переписываем значение из временной ячейки в измененную
        $records = TlDeliveryRoutes::find()->all();
        foreach ($records as $record){
            $flag = 0;
            if(!empty($record->delivery_date)){
                $record->delivery_date = $record->temp_delivery_date;
                $flag = 1;
            } elseif (empty($record->delivery_date)){
                $record->delivery_date = NULL;
                $flag = 1;
            }
            if(!empty($record->shipped_datetime)){
                $record->shipped_datetime = $record->temp_shipped_datetime;
                $flag = 1;
            } elseif (empty($record->shipped_datetime)){
                $record->shipped_datetime = NULL;
                $flag = 1;
            }
            if(!empty($record->accepted_datetime)){
                $record->accepted_datetime = $record->temp_accepted_datetime;
                $flag = 1;
            } elseif (empty($record->accepted_datetime)){
                $record->accepted_datetime = NULL;
                $flag = 1;

            }
            if($flag){
                $record->save(false);
            }

        }
        echo "End write date from temp column";
        echo "\n";


        echo "Drop temp column";
        echo "\n";
        //удаляем временную ячейку
        $db->createCommand()->dropColumn('tl_delivery_proposal_routes', 'temp_delivery_date')
            ->execute();
        $db->createCommand()->dropColumn('tl_delivery_proposal_routes', 'temp_shipped_datetime')
            ->execute();
        $db->createCommand()->dropColumn('tl_delivery_proposal_routes', 'temp_accepted_datetime')
            ->execute();

        echo 'change-delivery-proposal-routes-dates end' . "\n";
        return 0;
    }

    /**
     * Выполняет модификацию даты для таблицы
     * StoreReviews
     */
    public function actionChangeDeliveryProposalRouteCar()
    {
        return 0;
        echo 'change-delivery-route-car start' . "\n";
        $db = Yii::$app->db;

        //добавляем столбики для временного хранения даты delivery_date expected_delivery_date  shipped_datetime  accepted_datetime
        $db->createCommand()->addColumn('tl_delivery_proposal_route_cars', 'temp_delivery_date', Schema::TYPE_INTEGER)
            ->execute();
        $db->createCommand()->addColumn('tl_delivery_proposal_route_cars', 'temp_shipped_datetime', Schema::TYPE_INTEGER)
            ->execute();
        $db->createCommand()->addColumn('tl_delivery_proposal_route_cars', 'temp_accepted_datetime', Schema::TYPE_INTEGER)
            ->execute();
        $records = TlDeliveryProposalRouteCars::find()->all();


        foreach ($records as $record){
            //Конвертируем delivery date в timestamp
            if(!empty($record->delivery_date)){
                echo '--------------Current date save start record ID '.$record->id.'-----------------'."\n";
                //отнимаем 6 часов от даты доставки и конвертируем ее в метку времени
                $newDateDelivered = new \DateTime($record->delivery_date);
                $newDateDelivered->modify('-6 hours');
                //Сохраняем timestamp во временную ячейку
                $record->temp_delivery_date = $newDateDelivered->getTimestamp();

                echo 'delivery_date was: '.$record->delivery_date."\n";
                echo 'delivery_date converted and saved to: '.$record->temp_delivery_date."\n";
                echo '--------------Current date save end record ID '.$record->id.'-----------------'."\n";
            }

            //Конвертируем shipped_datetime в timestamp
            if(!empty($record->shipped_datetime)){
                echo '--------------Current date save start record ID '.$record->id.'-----------------'."\n";
                //отнимаем 6 часов от даты доставки и конвертируем ее в метку времени
                $newShippedDateDelivered = new \DateTime($record->shipped_datetime);
                $newShippedDateDelivered->modify('-6 hours');
                //Сохраняем timestamp во временную ячейку
                $record->temp_shipped_datetime = $newShippedDateDelivered->getTimestamp();

                echo 'shipped_datetime was: '.$record->shipped_datetime."\n";
                echo 'shipped_datetime converted and saved to: '.$record->temp_shipped_datetime."\n";
                echo '--------------Current date save end record ID '.$record->id.'-----------------'."\n";
            }
            //Конвертируем delivery date в timestamp
            if(!empty($record->accepted_datetime)){
                echo '--------------Current date save start record ID '.$record->id.'-----------------'."\n";
                //отнимаем 6 часов от даты доставки и конвертируем ее в метку времени
                $newAccDateDelivered = new \DateTime($record->accepted_datetime);
                $newAccDateDelivered->modify('-6 hours');
                //Сохраняем timestamp во временную ячейку
                $record->temp_accepted_datetime = $newAccDateDelivered->getTimestamp();

                echo 'accepted_datetime was: '.$record->accepted_datetime."\n";
                echo 'accepted_datetime converted and saved to: '.$record->temp_accepted_datetime."\n";
                echo '--------------Current date save end record ID '.$record->id.'-----------------'."\n";
            }

            $record->save(false);
        }
        echo "Start change columns type";
        echo "\n";
        //меняем тип ячейки с datetime на int
        $db->createCommand("ALTER TABLE `tl_delivery_proposal_route_cars` CHANGE `delivery_date` `delivery_date` INT( 11 ) NULL DEFAULT NULL COMMENT 'Delivery date ts'")
            ->execute();
        $db->createCommand("ALTER TABLE `tl_delivery_proposal_route_cars` CHANGE `shipped_datetime` `shipped_datetime` INT( 11 ) NULL DEFAULT NULL COMMENT 'Shipped date ts'")
            ->execute();
        $db->createCommand("ALTER TABLE `tl_delivery_proposal_route_cars` CHANGE `accepted_datetime` `accepted_datetime` INT( 11 ) NULL DEFAULT NULL COMMENT 'Accepted date ts'")
            ->execute();
        echo "End change columns type";
        echo "\n";

        echo "Start write date from temp column. Please wait....";
        echo "\n";
        //переписываем значение из временной ячейки в измененную
        $records = TlDeliveryProposalRouteCars::find()->all();
        foreach ($records as $record){
            $flag = 0;
            if(!empty($record->delivery_date)){
                $record->delivery_date = $record->temp_delivery_date;
                $flag = 1;
            } elseif (empty($record->delivery_date)){
                $record->delivery_date = NULL;
                $flag = 1;
            }
            if(!empty($record->shipped_datetime)){
                $record->shipped_datetime = $record->temp_shipped_datetime;
                $flag = 1;
            } elseif (empty($record->shipped_datetime)){
                $record->shipped_datetime = NULL;
                $flag = 1;
            }
            if(!empty($record->accepted_datetime)){
                $record->accepted_datetime = $record->temp_accepted_datetime;
                $flag = 1;
            } elseif (empty($record->accepted_datetime)){
                $record->accepted_datetime = NULL;
                $flag = 1;

            }
            if($flag){
                $record->save(false);
            }

        }
        echo "End write date from temp column";
        echo "\n";


        echo "Drop temp column";
        echo "\n";
        //удаляем временную ячейку
        $db->createCommand()->dropColumn('tl_delivery_proposal_route_cars', 'temp_delivery_date')
            ->execute();
        $db->createCommand()->dropColumn('tl_delivery_proposal_route_cars', 'temp_shipped_datetime')
            ->execute();
        $db->createCommand()->dropColumn('tl_delivery_proposal_route_cars', 'temp_accepted_datetime')
            ->execute();

        echo 'change-delivery-route-car end' . "\n";
        return 0;
    }

    /**
     * Выполняет модификацию даты для таблицы
     * StoreReviews
     */
    public function actionChangeDeliveryProposalUnfExp()
    {
        return 0;
        echo 'change-delivery-unforseen-expences start' . "\n";
        $db = Yii::$app->db;

        //добавляем столбики для временного хранения даты delivery_date expected_delivery_date  shipped_datetime  accepted_datetime
        $db->createCommand()->addColumn('tl_delivery_proposal_route_unforeseen_expenses', 'temp_delivery_date', Schema::TYPE_INTEGER)
            ->execute();

        $records = TlDeliveryProposalRouteUnforeseenExpenses::find()->all();


        foreach ($records as $record){
            //Конвертируем delivery date в timestamp
            if(!empty($record->delivery_date)){
                echo '--------------Current date save start record ID '.$record->id.'-----------------'."\n";
                //отнимаем 6 часов от даты доставки и конвертируем ее в метку времени
                $newDateDelivered = new \DateTime($record->delivery_date);
                $newDateDelivered->modify('-6 hours');
                //Сохраняем timestamp во временную ячейку
                $record->temp_delivery_date = $newDateDelivered->getTimestamp();

                echo 'delivery_date was: '.$record->delivery_date."\n";
                echo 'delivery_date converted and saved to: '.$record->temp_delivery_date."\n";
                echo '--------------Current date save end record ID '.$record->id.'-----------------'."\n";
            }

            $record->save(false);
        }
        echo "Start change columns type";
        echo "\n";
        //меняем тип ячейки с datetime на int
        $db->createCommand("ALTER TABLE `tl_delivery_proposal_route_unforeseen_expenses` CHANGE `delivery_date` `delivery_date` INT( 11 ) NULL DEFAULT NULL COMMENT 'Delivery date ts'")
            ->execute();
        echo "End change columns type";
        echo "\n";

        echo "Start write date from temp column. Please wait....";
        echo "\n";
        //переписываем значение из временной ячейки в измененную
        $records = TlDeliveryProposalRouteUnforeseenExpenses::find()->all();
        foreach ($records as $record){
            $flag = 0;
            if(!empty($record->delivery_date)){
                $record->delivery_date = $record->temp_delivery_date;
                $flag = 1;
            } elseif (empty($record->delivery_date)){
                $record->delivery_date = NULL;
                $flag = 1;
            }
            if($flag){
                $record->save(false);
            }

        }
        echo "End write date from temp column";
        echo "\n";


        echo "Drop temp column";
        echo "\n";
        //удаляем временную ячейку
        $db->createCommand()->dropColumn('tl_delivery_proposal_route_unforeseen_expenses', 'temp_delivery_date')
            ->execute();

        echo 'change-delivery-unforseen-expences end' . "\n";
        return 0;
    }

} 