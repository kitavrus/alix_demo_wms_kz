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

////Yii::$app->get('tcpdf');;;

$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//$pdf->SetFont('dejavusans', '', 8);
$pdf->SetFont('arial', 'b', 8);
$pdf->SetMargins(10, 5, 10);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

//for ($i = 1; $i <= $to; $i++) {
//    $this->renderAjax('_print_ttn.php', ['pdf' => $pdf, 'model' => $model,'to'=>$to,'userName'=>$userName]);
    $this->renderAjax('_print_ttn.php', ['pdf' => $pdf, 'model' => $model,'userName'=>$userName,'managersNamesTo'=>$managersNamesTo]);
//}

$pdf->lastPage();

$pdf->Output(time() . '-ttn.pdf', 'D');
Yii::$app->end();