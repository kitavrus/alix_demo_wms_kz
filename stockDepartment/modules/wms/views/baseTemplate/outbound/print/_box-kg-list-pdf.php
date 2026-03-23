<?php
//use common\modules\product\models\ProductBarcodes;
use common\components\BarcodeManager;
//  город
$html = '<h2>Заказ №'.$orderNumberTitle.'</h2>';
$html .='<p><b>Куда:</b>'.$toPoint.'</p>';
$html .= '<table width="100%" cellspacing="0" cellpadding="4" border="1">' .
    '   <tr align="center" valign="middle" >' .
    '      <th width="10%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms','№') . '</strong></th>' .
    '      <th width="35%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/titles','BOX_BARCODE') . '</strong></th>' .
    '      <th width="35%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/titles','BOX_SIZE') . '</strong></th>' .
    '      <th width="20%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/titles','BOX_KG') . '</strong></th>' .
    '   </tr>';

    if ($stockItems) {
        $i = 0;
        $totalKg = 0;
        foreach ($stockItems as $row) {
            $totalKg += $row['box_kg'];
            $html.='<tr>'.
                '<td>' . ++$i . '</td>'.
                '<td>' . $row['box_barcode'] . '</td>'.
                '<td>' . BarcodeManager::mapM3ToBoxSize($row['box_size_barcode']) . '</td>'.
                '<td>' . $row['box_kg'] . '</td>'.
                '</tr>';
        }
        $html .= '</table>';
        $html.= '<p><b>Общее вес: </b>'.$totalKg.'</p><br />';
        $html.= '<p><b>Общее кол-во коробов: </b>'.$i.'</p><br />';
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

    $pdf->Output(time() . '-boxes-kg.pdf', 'D');
    die;