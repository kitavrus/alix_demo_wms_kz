<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 20.10.14
 * Time: 19:17
 */
/* @var $this yii\web\View */

$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetFont('arial', 'b', 8); //ok
$pdf->SetMargins(10, 5);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetAutoPageBreak(true);
$this->renderAjax('_print-ttn-v2.php', [
    'pdf' => $pdf,
    'model' => $model,
    'outboundOrderItems'=>$outboundOrderItems,
    'endPointAddress'=>$endPointAddress,
    'endPointCompanyName'=>$endPointCompanyName,
    'dateTime'=>$dateTime,
    'ttnNumber'=>$ttnNumber,
    'clientName'=>$clientName,
    'passedUserName'=>$passedUserName,
    'positionUser'=>$positionUser,
    'managersNamesTo'=>$managersNamesTo,
]);

$model->delivery_date = time();
$model->status = \common\modules\transportLogistics\models\TlDeliveryProposal::STATUS_DONE;
$model->save(false);

$pdf->lastPage();
$pdf->Output($model->id. '-ttn.pdf', 'D');
die;