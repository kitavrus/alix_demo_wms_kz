<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 20.10.14
 * Time: 19:17
 */

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposal */

$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetFont('arial', 'b', 8); //ok
$pdf->SetMargins(10, 5, 10);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Use Case: Begin
$qtyTTN = 3;
$routeDirectionService = new \common\modules\city\RouteDirection\service\Service(new \common\modules\city\RouteDirection\repository\Repository());
if($routeDirectionService->isAlmatyStore($model->route_to) || $routeDirectionService->isSouthStore($model->route_to)) {
    $qtyTTN = 2;
}

if($routeDirectionService->isSouthStore($model->route_to)) {
    $qtyTTN = 4;
}
// Use Case: End

for ($i = 1; $i <= $qtyTTN; $i++) {
    $this->renderAjax('_print_ttn.php', ['pdf' => $pdf, 'model' => $model,'managersNamesTo'=>$managersNamesTo]);
}
$this->renderAjax('_print_ttn_driver.php', ['pdf' => $pdf, 'model' => $model,'managersNamesTo'=>$managersNamesTo]);

$pdf->lastPage();

$pdf->Output(time() . '-ttn.pdf', 'D');
die;
//Yii::$app->end();