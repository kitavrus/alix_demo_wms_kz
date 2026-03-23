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
$pdf->SetFont('dejavusans', '', 8);
$pdf->SetMargins(10, 5, 10);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
//$pdf->AddPage();

if ($routes = $model->getProposalRoutes()->all()) {
    foreach ($routes as $routeDpItem) {
            if($routeDpItem->route_from == $model->route_from && $routeDpItem->route_to==$model->route_to)  {
//                \yii\helpers\VarDumper::dump($routeDpItem,10,true);

                if ($carItems = $routeDpItem->getCarItems()->all()) {
                    foreach ($carItems as $item) {
//                        \yii\helpers\VarDumper::dump($item,10,true);

//                        \yii\helpers\VarDumper::dump($modelDpRouteCar,10,true);

                        for ($i = 1; $i <= 3; $i++) {
//                            $modelDpRouteCar = TlDeliveryProposalRouteTransport::findOne(['tl_delivery_proposal_route_cars_id' => $model->id, 'tl_delivery_proposal_route_id' => $rItem->id]);
                            $modelDpRouteCar = TlDeliveryProposalRouteTransport::findOne(['tl_delivery_proposal_id'=>$model->id,'tl_delivery_proposal_route_cars_id'=>$item->id,'tl_delivery_proposal_route_id'=>$routeDpItem->id]);
                            $this->renderAjax('_print_ttn.php', ['pdf' => $pdf, 'model' => $item, 'routItem' => $routeDpItem, 'modelDpRouteCar' => $modelDpRouteCar]);
                        }
                    }
                }
            }
//        if ($carItems = $routeDpItem->getCarItems()->all()) {
//            foreach ($carItems as $item) {

//            for ($i = 1; $i <= 3; $i++) {
//                $modelDpRouteCar = TlDeliveryProposalRouteTransport::findOne(['tl_delivery_proposal_route_cars_id' => $model->id, 'tl_delivery_proposal_route_id' => $rItem->id]);
//                $this->renderAjax('_print_ttn.php', ['pdf' => $pdf, 'model' => $model, 'routItem' => $rItem, 'modelDpRouteCar' => $modelDpRouteCar]);
//            }

//            }
//        }
//        \yii\helpers\VarDumper::dump($routeDpItem,10,true);
    }
}

$pdf->lastPage();

$pdf->Output(time() . '-ttn.pdf', 'D');
Yii::$app->end();