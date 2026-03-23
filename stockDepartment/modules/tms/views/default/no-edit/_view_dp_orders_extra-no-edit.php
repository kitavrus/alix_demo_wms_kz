<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 23.09.14
 * Time: 16:17
 */

use yii\helpers\Html;
use app\modules\transportLogistics\transportLogistics;


/* @var $this yii\web\View */
/* @var $model  common\modules\transportLogistics\models\TlDeliveryProposal */


$row = '';
$row .= '<tr>';
$row .= '<td colspan="4"> <h4>' . Yii::t('transportLogistics/titles', 'Additional') . '</h4></td>';
$row .= '<td style="width: 155px">';
$row .= '</td>';
$row .= '</tr>';
if ($unforeseenExpenses = $model->getExtras()->all()) {

    foreach ($unforeseenExpenses as $oe) {
        $row .= '<tr>';
//        $row .= '<td></td>';
        $row .= '<td colspan="2">';
        $row .=  $oe->name;
        $row .= '</td>';

        $row .= '<td >';
        $row .=  $oe->number_places;
        $row .= '</td>';
        $row .= '<td >';
        $row .=  $oe->comment;
        $row .= '</td>';

        $row .= '<td>';
        $row .= '';
        $row .= '</td>';
        $row .= '</tr>';

    }



}

$row .= '<tr>';
$row .= '<td colspan="9" style="border-left: 1px solid #ffffff!important; border-right: 1px solid #ffffff!important; background-color: #ffffff;">';
$row .= '&nbsp;';
$row .= '</td>';
$row .= '</tr>';

echo $row;