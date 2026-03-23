<?php
use common\modules\product\models\ProductBarcodes;

$html = '<h2>Содержимое короба №'.$box_barcode.'</h2>';
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



    ////Yii::$app->get('tcpdf');;;
    $pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('nmdx.com');
    $pdf->SetTitle('Product labels');
    $pdf->SetSubject('Product labels');
    $pdf->SetKeywords('nmdx.com, product, label');

    // remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    //set margins
    $pdf->SetMargins(10, 10, 10, true);

    //set auto page breaks
    //$pdf->SetAutoPageBreak(false, 0);
    $pdf->SetAutoPageBreak(true, 5);

    //set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');
    $pdf->SetFont('arial', 'B', 10);
    $pdf->AddPage('P', 'A4', true);
    $pdf->writeHTML($html);

    $pdf->Output($box_barcode . '-boxes-content.pdf', 'D');
    die;



