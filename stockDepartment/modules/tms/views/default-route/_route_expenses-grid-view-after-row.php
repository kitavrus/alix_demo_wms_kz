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
$row .= '<tr>';
$row .= '<td colspan="4"> <h4>' . Yii::t('transportLogistics/titles', 'Expenses') . '</h4></td>';
$row .= '<td>' . Html::a(Html::tag('span','',['class' => 'glyphicon glyphicon-plus-sign']), ['add-route-unforeseen-expenses', 'sub_route_id' => $model->id], ['class' => 'btn btn-primary','title'=>Yii::t('transportLogistics/buttons', 'Add')]);
$row .= '</td>';
$row .= '</tr>';


// Расходы
if ($unforeseenExpenses = $model->getTlDeliveryProposalRouteUnforeseenExpenses()->all()) {

    foreach ($unforeseenExpenses as $ue) {
        $row .= '<tr>';
        $row .= '<td colspan="2">';
        $row .= $ue->name.' ( '.$ue->getTypeValue().' ) '.' ( '.$ue->comment.' ) ';
        $row .= '</td>';

        $row .= '<td>';
        $row .= Yii::$app->formatter->asCurrency($ue->price_cache);
        $row .= ' / ';
        $row .= Yii::$app->formatter->asCurrency($ue->price_with_vat);
        $row .= '</td>';

        $row .= '<td >';
        $row .= ActiveRecord::getPaymentMethodArray($ue->cash_no);
        $row .= ' / ';
        $row .= $ue->getWhoPayValue();
        $row .= '</td>';

        $row .= '<td>' . Html::a(Html::tag('span','',['class' => 'glyphicon glyphicon-pencil']), ['update-route-unforeseen-expenses', 'id' => $ue->id], ['class' => '']);
        $row .= ' ' . Html::a(Html::tag('span','',['class' => 'glyphicon glyphicon-trash']), ['delete-route-unforeseen-expenses', 'id' => $ue->id], ['class' => '', 'style' => '-float:right;']);
        $row .= '</td>';
        $row .= '</tr>';
    }
}

$row .= '<tr>';
$row .= '<td colspan="4" style="border-left: 1px solid #ffffff!important; border-right: 1px solid #ffffff!important; background-color: #ffffff;">';
$row .= '&nbsp;';
$row .= '</td>';
$row .= '</tr>';
echo $row;