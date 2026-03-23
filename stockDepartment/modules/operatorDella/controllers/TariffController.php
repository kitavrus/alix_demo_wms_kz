<?php

namespace app\modules\operatorDella\controllers;

use bossDepartment\modules\store\models\Store;
use common\modules\billing\models\TlDeliveryProposalBilling;
use common\modules\city\models\City;
use common\modules\client\models\Client;
use Yii;
use yii\helpers\VarDumper;
use yii\web\Controller;
use app\modules\operatorDella\models\DeliveryCalculatorForm;


class TariffController extends Controller
{
    /**
     * This action show form, for calculation estimated
     * delivery cost
     */
    public function actionCalculator()
    {
       $model = new DeliveryCalculatorForm();
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $model->load(Yii::$app->request->post());
                if($model->validate()){
                    $data = $model->calculateDeliveryCost();
                    if(!empty($data)) {
                        return [
                            'message' => 'Success',
                            'data'=>$data,
                        ];
                    }
                } else {
                    throw new \yii\web\HttpException(500, 'Required parameters missing');
                }
            Yii::$app->end();
        }

       return  $this->render('calculator', ['model'=>$model]);
    }

    /**
     * This action show form, for calculation estimated
     * delivery cost
     */
    public function actionPreCalculatePrice()
    {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $from_route_id = Yii::$app->request->post('from_route_id');
            $to_route_id = Yii::$app->request->post('to_route_id');
            $client_id = Yii::$app->request->post('client_id');
            $weight = Yii::$app->request->post('weight');
            $volume = Yii::$app->request->post('volume');
            $delivery_type = Yii::$app->request->post('delivery_type');

            if($route_from = Store::findOne($from_route_id)){
                $from_city_id = $route_from->city_id;
            }

            if($route_to = Store::findOne($to_route_id)){
                $to_city_id = $route_to->city_id;
            }

            if(isset($to_city_id) && isset($from_city_id)){
                $model = new DeliveryCalculatorForm();
                $model->client_id = $client_id;
                $model->city_to = $to_city_id;
                $model->city_from = $from_city_id;
                $model->weight = $weight;
                $model->volume = $volume;
                $model->delivery_type = $delivery_type;

                if($model->validate()){
                    $data = $model->calculateDeliveryCost();
                    if(!empty($data)){
                        return [
                            'message' => 'Success',
                            'data'=>$data,
                        ];
                    }
                } else {
//                    VarDumper::dump($model->getErrors(),10,true);
//                    die;
                    throw new \yii\web\HttpException(500, 'Required parameters missing');
                }
            }


                Yii::$app->end();
            }

    }

    /**
     * This action show form, for calculation estimated
     * delivery cost
     */
    public function actionExportPdf()
    {
        $data =[];
        $defaultCityTariiff = [];
       $tariffs = TlDeliveryProposalBilling::find()->andWhere([
           'tariff_type' => TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_DEFAULT,
           'rule_type' => TlDeliveryProposalBilling::RULE_TYPE_CITY_BY_WEIGHT_VOLUME_INDEX,
           'delivery_type' => TlDeliveryProposalBilling::DELIVERY_TYPE_WAREHOUSE_WAREHOUSE,
       ])
           ->all();

        if($tariffs){
            foreach ($tariffs as $k => $tariff){
                $dd = TlDeliveryProposalBilling::find()->andWhere([
                    'tariff_type' => TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_DEFAULT,
                    'rule_type' => TlDeliveryProposalBilling::RULE_TYPE_CITY_BY_WEIGHT_VOLUME_INDEX,
                    'delivery_type' => TlDeliveryProposalBilling::DELIVERY_TYPE_DOOR_DOOR,
                    'from_city_id' => $tariff->from_city_id,
                    'to_city_id' => $tariff->to_city_id,
                ])
                ->one();
                $to = $tariff->cityTo;
                $data[$k]=[
                    'to' => $to ? $to->name : '-не задано-',
                    'delivery_term' => $tariff->delivery_term,
                    'type_wh' => Yii::$app->formatter->asCurrency($tariff->price_invoice_kg_with_vat),
                    'type_dd' => $dd ? Yii::$app->formatter->asCurrency($dd->price_invoice_kg_with_vat) : '-',
                ];
            }
        }
        if($dcityTariff = TlDeliveryProposalBilling::find()->andWhere([
            'tariff_type' => TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_DEFAULT,
            'rule_type' => TlDeliveryProposalBilling::RULE_TYPE_BY_CONDITION,
            'from_city_id' => City::DEFAULT_CITY,
            'to_city_id' => City::DEFAULT_CITY,
        ])->one()){
            if($conditions = $dcityTariff->conditions){
                foreach($conditions as $condition){
                    $defaultCityTariiff[] = $condition->getConditionTitle();
                }
            }

        }


        return  $this->render('print/print-tariff-pdf', ['data'=>$data, 'defaultCity' => $defaultCityTariiff]);

    }

}
