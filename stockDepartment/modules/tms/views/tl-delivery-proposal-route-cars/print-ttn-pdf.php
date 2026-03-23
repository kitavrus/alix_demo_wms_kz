<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 20.10.14
 * Time: 19:17
 */
use common\modules\transportLogistics\models\TlDeliveryProposalRouteTransport;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposalRouteCars */
//use yii;

////Yii::$app->get('tcpdf');;

$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//$pdf->SetFont('dejavusans', '', 8);
$pdf->SetFont('arial', 'b', 8);
$pdf->SetMargins(10, 5, 10);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

if ($r = $model->getRoutes()->all()) {

    foreach ($r as $rItem) {
        for ($i=1; $i<=3; $i++) {
            $modelDpRouteCar = TlDeliveryProposalRouteTransport::findOne(['tl_delivery_proposal_route_cars_id' => $model->id, 'tl_delivery_proposal_route_id' => $rItem->id]);
            $this->renderAjax('_print_ttn.php', ['pdf' => $pdf, 'model' => $model,'routItem'=>$rItem,'modelDpRouteCar'=>$modelDpRouteCar]);
        }
    }
}

$pdf->lastPage();

$pdf->Output(time().'-ttn.pdf', 'D');
Yii::$app->end();