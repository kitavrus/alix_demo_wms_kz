<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 08.10.15
 * Time: 10:15
 */

use common\modules\transportLogistics\components\TLHelper;
use common\modules\crossDock\models\CrossDockItems;
use common\modules\crossDock\models\CrossDock;
use common\modules\store\models\Store;
use common\modules\crossDock\models\CrossDockItemProducts;

////Yii::$app->get('tcpdf');;;
$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetFont('arial', 'b', 8); //ok
$pdf->SetMargins(10, 5, 10);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->AddPage();

$html = '';
$html .= '<table width="90%" style="padding:2px">
            <tr>
                <td width="100%"><h1>Поиск по содержимому короба</h1></td>
            </tr>
             <tr>
                <td width="30%">Дата: ' . date('Y.m.d') . '</td>
                <td width="30%">Шк товара: ' . $productBarcode . '</td>
            </tr>
        </table><br>';
$pdf->writeHTML($html, true, false, true, false, 'L');
$pdf->ln(5);

$html = '<table style="padding:2px" width="100%"  border="1" align="left" >
        <tr>
            <th>Магазин</th>
<th>Штрих код короба</th>
<th>Штрих код товара</th>
<th>Количество / Отсканировали</th>
</tr>';
 foreach($data as $value) {
        $html .= ' <tr >';
            $html .= '<td style="background-color: #ff9393 !important;">';

            $cdItem = CrossDockItems::findOne($value->id);
            $storeName = '';
            if($cdItem) {
                $cd = CrossDock::findOne($cdItem->cross_dock_id);
                $store = Store::findOne($cd->to_point_id);
                $storeName = $store->getPointTitleByPattern('{shopping_center_name} / {shop_code} / {city_name}');
            }
            $html .= $storeName;

            $html .= '</td>';
            $html .= '<td style="background-color: #ff9393 !important;">';

            $title = '';
            if($cdItem) {
                $title = $cdItem->box_barcode;
            }
            $html .=  $title;

            $html .= ' </td>';
            $html .= '<td style="background-color: #ff9393 !important;"></td>';
            $html .= '<td style="background-color: #ff9393 !important;"></td>';
        $html .= '</tr>';
         $boxes = CrossDockItemProducts::find()->andWhere(['cross_dock_item_id'=>$value->id])->all();
         if($boxes) {
            foreach($boxes as $box) {
                $html .= '<tr>';
                $html .= '<td>-</td>';
                $html .= '<td>-</td>';
                $html .= '<td '.  (isset($scannedProductsWhere[$box->product_barcode]) ? 'style="background-color: #ff9393 !important;"' : '') .'>'. $box->product_barcode .'</td>';
                $html .= '<td>'. $box->expected_qty . ' / '.(isset($scannedProductsWhere[$box->product_barcode]) ? $scannedProductsWhere[$box->product_barcode] : ' 0') .'</td>';
                $html .= '</tr>';
             }
         }
 }
$html .= '</table>';

$pdf->writeHTML($html, true, false, true, false, 'C');
$pdf->lastPage();
$pdf->Output(time() . '-product-by-box.pdf', 'D');
die;