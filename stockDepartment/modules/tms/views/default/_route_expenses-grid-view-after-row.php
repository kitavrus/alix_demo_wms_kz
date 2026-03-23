<?php

use yii\helpers\Html;
use app\modules\transportLogistics\transportLogistics;
use common\models\ActiveRecord;
use common\modules\transportLogistics\components\TLHelper;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteTransport;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;


/* @var $this yii\web\View
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposal */
/* @var $form yii\widgets\ActiveForm */

$row = '';

// Машина которая едет по этому маршруту
$freeCar = '';
if(TLHelper::isFreeCarByCity($model->id)) {
    $freeCar = Html::a(Html::tag('span',' Свободные машины по маршрутам',['class' => 'glyphicon glyphicon-share-alt']), ['select-route-car', 'route_id' => $model->id], ['class' => 'btn btn-danger','title'=>Yii::t('transportLogistics/buttons', 'Select'),'style'=>"float:right;"]);
}

$row .= '<tr>';
$row .= '<td colspan="8"> <h4>' . Yii::t('transportLogistics/titles', 'Transports')  . $freeCar . '</h4></td>';
$row .= '<td>' . Html::a(Html::tag('span','',['class' => 'glyphicon glyphicon-plus-sign']), ['add-new-route-car', 'route_id' => $model->id], ['class' => 'btn btn-primary','title'=>Yii::t('transportLogistics/buttons', 'Add'),'style'=>"float:left;"]);

$row .= '</td>';
$row .= '</tr>';

if($carItems = $model->getCarItems()->all()) {
    foreach ($carItems as $item) {

        $modelDpRouteCar = TlDeliveryProposalRouteTransport::findOne(['tl_delivery_proposal_route_cars_id'=>$item->id,'tl_delivery_proposal_route_id'=>$model->id]);

        $title = '';
        $dangerTrClass = '';

        //S: Сделать отдельной функцией

        if( !empty($modelDpRouteCar->number_places_actual) ) {
            $title .= (!empty($modelDpRouteCar->number_places_actual) ? $modelDpRouteCar->number_places_actual : '0') . ' ' . Yii::t('transportLogistics/titles', 'Amount of places') . "<br />";
            $title .= (!empty($modelDpRouteCar->mc_actual) ? Yii::$app->formatter->asDecimal($modelDpRouteCar->mc_actual) : '0') . ' ' . Yii::t('transportLogistics/titles', 'М3') . "<br />";
            $title .= (!empty($modelDpRouteCar->kg_actual) ? Yii::$app->formatter->asDecimal($modelDpRouteCar->kg_actual) : '0') . ' ' . Yii::t('transportLogistics/titles', 'Kg') . "<br />";
        } else {
            $dangerTrClass = 'bg-danger';
            $title = Yii::t('transportLogistics/titles', 'You must specify the amount of places in the car') . "<br />";
        }
        //E : Сделать отдельной функцией

        $row .= '<tr class="'.$dangerTrClass.'">';
        $row .= '<td >';
        if($car = $item->car) {
            $row .= $car->getDisplayTitle();
        } else {
            $row .= '-';
        }
        if((int)$item->price_invoice){
            $row .= ' / ' . Yii::$app->formatter->asCurrency($item->price_invoice);
        }
        if((int)$item->price_invoice_with_vat){
            $row .= ' / ' . Yii::$app->formatter->asCurrency($item->price_invoice_with_vat).' (c НДС)';
        }
        //$row .= ' / ' . Yii::$app->formatter->asCurrency($item->price_invoice);
        $row .= '</td>';
        $row .= '<td>';
        $row .= $title;
        $row .= '</td>';

        $row .= '<td colspan="6">';

        if($r = $item->getRoutes()->all()) {
            foreach($r as $rItem) {
                if ( !in_array($rItem->id,[$model->id]) ) {

                    $row .= $rItem->getSmallDisplayTitle();

                    //S: Сделать отдельной функцией
                    $title = ' / ';
                    $modelDpRouteCar = TlDeliveryProposalRouteTransport::findOne(['tl_delivery_proposal_route_cars_id'=>$item->id,'tl_delivery_proposal_route_id'=>$rItem->id]);
                    $title .= (!empty($modelDpRouteCar->number_places) ? $modelDpRouteCar->number_places : '0') . ' ' . Yii::t('transportLogistics/custom', 'Places') . " / ";
                    $title .= (!empty($modelDpRouteCar->mc_actual) ? Yii::$app->formatter->asDecimal($modelDpRouteCar->mc_actual) : '0') . ' ' . Yii::t('transportLogistics/titles', 'М3') . " / ";
                    $title .= (!empty($modelDpRouteCar->kg_actual) ? Yii::$app->formatter->asDecimal($modelDpRouteCar->kg_actual) : '0') . ' ' . Yii::t('transportLogistics/titles', 'Kg');
                    //E : Сделать отдельной функцией

                    $row .= $title;
                    $row .= ' <br /> ';
                }
            }
        }

        $row .= '</td>';
        $row .= '<td>' . Html::a(Html::tag('span','',['class' => 'glyphicon glyphicon-pencil', 'data-confirm' => 'Вы точно хотите удалить транспорт?']), ['update-route-car', 'id' => $item->id,'route_id'=>$model->id], ['class' => '']);
        //$row .= ' ' . Html::a(Html::tag('span','',['class' => 'glyphicon glyphicon-trash']), ['delete-route-car', 'id' => $item->id,'route_id'=>$model->id], ['class' => '', 'style' => '-float:right;']);
        $row .= '<a data-pjax="0" data-method="post" data-confirm="Вы уверены, что хотите удалить этот элемент?" aria-label="Удалить" title="Удалить" href="/tms/default/delete-route-car?id='.$item->id.'&route_id='.$model->id.'"><span class="glyphicon glyphicon-trash"></span></a>';
        $row .= '</td>';
        $row .= '</tr>';
    }
}

$row .= '<tr>';
$row .= '<td colspan="8"> <h4>' . Yii::t('transportLogistics/titles', 'Expenses') . '</h4></td>';
$row .= '<td>' . Html::a(Html::tag('span','',['class' => 'glyphicon glyphicon-plus-sign']), ['add-route-unforeseen-expenses', 'route_id' => $model->id], ['class' => 'btn btn-primary','title'=>Yii::t('transportLogistics/buttons', 'Add')]);
$row .= '</td>';
$row .= '</tr>';


// Расходы
if ($unforeseenExpenses = $model->getTlDeliveryProposalRouteUnforeseenExpenses()->all()) {

    foreach ($unforeseenExpenses as $ue) {
        $row .= '<tr>';
        $row .= '<td colspan="2">';
        $row .= $ue->name.' ( '.$ue->getTypeValue().' ) ';
        $row .= '</td>';

        $row .= '<td>';
        $row .= Yii::$app->formatter->asCurrency($ue->price_cache);
        $row .= '</td>';

        $row .= '<td>';
        $row .= Yii::$app->formatter->asCurrency($ue->price_with_vat);
        $row .= '</td>';

        $row .= '<td >';
        $row .= ActiveRecord::getPaymentMethodArray($ue->cash_no);
        $row .= '</td>';
        $row .= '<td >';
        $row .= $ue->getWhoPayValue();
        $row .= '</td>';
        $row .= '<td colspan="2">';
        $row .= $ue->comment;
        $row .= '</td>';


        $row .= '<td>' . Html::a(Html::tag('span','',['class' => 'glyphicon glyphicon-pencil']), ['update-route-unforeseen-expenses', 'id' => $ue->id], ['class' => '']);
        $row .= ' ' . Html::a(Html::tag('span','',['class' => 'glyphicon glyphicon-trash']), ['delete-route-unforeseen-expenses', 'id' => $ue->id], ['class' => '', 'style' => '-float:right;']);
        $row .= '</td>';
        $row .= '</tr>';
    }
}


// Заказы
//if ($orders = $model->getDeliveryProposalRouteOrders()->all()) {
//
//    $row .= '<tr>';
//    $row .= '<td colspan="9"><h4>' . Yii::t('transportLogistics/forms', 'Заказы-1') . '</h4></td>';
//    $row .= '</tr>';
//
//    foreach ($orders as $order) {
//        $row .= '<tr>';
//        $row .= '<td colspan="2">' . $order->client->title . '</td>';
//        $row .= '<td>' . TlDeliveryProposalOrders::getOrderTypeValue($order->order_type) . '</td>';
//        $row .= '<td colspan="6">';
//        $row .= '' . $order->order_number;
//        $row .= '</td>';
//        $row .= '</tr>';
//    }
//}

$row .= '<tr>';
$row .= '<td colspan="9" style="border-left: 1px solid #ffffff!important; border-right: 1px solid #ffffff!important; background-color: #ffffff;">';
$row .= '&nbsp;';
$row .= '</td>';
$row .= '</tr>';

echo $row;