<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 02.10.14
 * Time: 17:38
 */

use yii\helpers\Html;
use app\modules\transportLogistics\transportLogistics;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteTransport;

/* @var $this yii/web/View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposalRouteCars */


$row = '';
$deletedMessage =  '<span class="alert-danger">' . Yii::t('transportLogistics/custom','This proposal was deleted').' </span>';
$titleDelete = '';

if ($r = $model->getRoutes()->all()) {

    $row .= '<tr>';
    $row .= '<td colspan="11" >';

    foreach ($r as $rItem) {

        $modelDpRouteCar = TlDeliveryProposalRouteTransport::findOne(['tl_delivery_proposal_route_cars_id' => $model->id, 'tl_delivery_proposal_route_id' => $rItem->id]);



        if(!($dp = \common\modules\transportLogistics\models\TlDeliveryProposal::findOne($rItem->tl_delivery_proposal_id))) {
            $titleDelete = $deletedMessage;

        } else {
            $titleDelete = '';
        }

        $title = '<br />';

        $title .= (!empty($modelDpRouteCar->number_places) ? $modelDpRouteCar->number_places : '0') . ' ' . Yii::t('transportLogistics/custom', 'Places') . "<br />";
        $title .= (!empty($modelDpRouteCar->mc_actual) ? $modelDpRouteCar->mc_actual : '0') . ' ' . Yii::t('transportLogistics/custom', 'М3') . "<br />";
        $title .= (!empty($modelDpRouteCar->kg_actual) ? $modelDpRouteCar->kg_actual : '0') . ' ' . Yii::t('transportLogistics/custom', 'Кг') . "<br />";

        if(!empty($titleDelete)) {
            $row .= Html::tag('span',$rItem->getSmallDisplayTitle()).' '.$titleDelete;
        } else {
            $row .= Html::a($rItem->getSmallDisplayTitle(), ['/tms/default/view', 'id' => $rItem->tl_delivery_proposal_id]);
        }



        $row .= '  ' . $title;
        $row .= ' <br /> ';
    }

    $row .= '</td>';
    $row .= '</tr>';
} else {
    $row .= '<tr>';
    $row .= '<td colspan="11">';
    $row .= Yii::t('titles', 'Машина еще не перевозит ни один груз (ПУСТАЯ)');
    $row .= '</td>';
    $row .= '</tr>';
}

$row .= '<tr>';
$row .= '<td colspan="11" style="border-left: 1px solid #ffffff!important; border-right: 1px solid #ffffff!important; background-color: #ffffff;">';
$row .= '&nbsp;';
$row .= '</td>';
$row .= '</tr>';

echo $row;