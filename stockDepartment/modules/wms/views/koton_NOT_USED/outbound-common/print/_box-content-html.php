<?php
use common\modules\product\models\ProductBarcodes;
use yii\helpers\Html;

$html = Html::beginTag('div', ['class'=>'a4']);

$html .= '<h2>Содержимое короба №'.$box_barcode.'</h2>';
//$html .= '<p><b>Заказ № '.$orderNumber.'</b></p>';
$html .='<p><b>Куда:</b>'.$toPoint.'</p>';
//\yii\helpers\VarDumper::dump($items, 10, true); die;
$html .= '<table width="100%" cellspacing="0" cellpadding="4" border="1">' .
    '   <tr align="center" valign="middle" >' .
    '      <th width="20%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms','Product Barcode') . '</strong></th>' .
    '      <th width="20%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms','Product Model') . '</strong></th>' .
    '      <th width="20%" align="center" valign="middle" border="1"><strong>' . Yii::t('product/forms','Color') . '</strong></th>' .
    '      <th width="20%" align="center" valign="middle" border="1"><strong>' . Yii::t('product/forms','Size') . '</strong></th>' .
    '      <th width="20%" align="center" valign="middle" border="1"><strong>' . Yii::t('product/forms','Product qty') . '</strong></th>' .
    '   </tr>';

    if ($items) {
        $total = 0;
        foreach ($items as $row) {
            $total += $row['product_qty'];
            $productModel = '';
            $productSize = '';
            $productColor = '';
            if ($pb = ProductBarcodes::find()->andWhere(['client_id' => $clientID, 'barcode' => $row['product_barcode']])->one()) {
                    if ($product = $pb->product) {
                        $productModel = $product->model;
                        $productSize = $product->size;
                        $productColor = $product->color;
                    }
            }
            $html.='<tr>'.
                '<td>'.  $row['product_barcode'].'</td>'.
                '<td>'.  $productModel.'</td>'.
                '<td>'.  $productColor.'</td>'.
                '<td>'.  $productSize.'</td>'.
                '<td>'.  $row['product_qty'].'</td>'.
                '</tr>';
        }
        $html .= '</table>';
        $html.= '<p><b>Всего: </b>'.$total.'</p>';
    }

$html .= Html::endTag('div');
echo $html;



