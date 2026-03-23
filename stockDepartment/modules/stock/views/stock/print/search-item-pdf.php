<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 30.01.15
 * Time: 15:43
 */
//use common\modules\stock\models\Stock;
//use common\modules\outbound\models\OutboundPickingLists;
//use common\modules\outbound\models\OutboundOrder;
//use common\modules\outbound\models\OutboundOrderItem;
use yii\helpers\ArrayHelper;

////Yii::$app->get('tcpdf');;;
///======================================================================
//$pdf->SetCreator(PDF_CREATOR);
//$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetAuthor('nmdx.com');
$pdf->SetTitle('nmdx.com');
$pdf->SetSubject('nmdx.com');
$pdf->SetKeywords('nmdx.com');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(10, 10, 10, true);

//set auto page breaks
$pdf->SetAutoPageBreak(true, 5);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');

$pdf->AddPage('P', 'A4', true);
$pdf->SetFont('arial', 'B', 7);


$html = '';
$countPages = 0;
$page = 0;
$pages = 0;
$countItem = 0;
$batchCount = 30;

if($count = $query->count()) {
    $pages = ceil($count / $batchCount);
    $page = 1;
    foreach($query->batch($batchCount) as $values) {

        $html .= '<table width="100%" cellspacing="0" cellpadding="4" border="1">' .
            '   <tr align="center" valign="middle" >' .
            '      <th width="10%" align="center" valign="middle" border="1"><strong>' . Yii::t('forms', 'Quantity') . '</strong></th>' .
            '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('stock/forms', 'Product barcode') . '</strong></th>' .
            '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('stock/forms', 'Primary address') . '</strong></th>' .
            '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('stock/forms', 'Secondary address') . '</strong></th>' .
            '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('stock/forms', 'Product model') . '</strong></th>' .
            '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('stock/forms', 'Status') . '</strong></th>' .
            '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('stock/forms', 'Status availability') . '</strong></th>' .
            '   </tr>';

        foreach($values as $value) {

            $pbr = $value['product_barcode'];
            $codePart1 = substr($pbr, 0, 8);
            $codePart4 = substr($pbr, 8, 5);

            $pbrFormatText = $codePart1 . ' <b style="font-size: 3mm; font-weight: bold; ">' . $codePart4 . '</b>';

            $html .= '<tr align="center" valign="middle">' .
                '<td align="center" valign="middle" border="1">' . $value['qty'] . '</td>' .
                '<td align="center" valign="middle" border="1">' . $pbrFormatText . '</td>' .
                '<td align="left" valign="middle" border="1">'   . $value['primary_address'] . '</td>' .
                '<td align="center" valign="middle" border="1">' . $value['secondary_address'] . '</td>' .
                '<td align="center" valign="middle" border="1">' . $value['product_model'] . '</td>' .
                '<td align="center" valign="middle" border="1">' . ArrayHelper::getValue($statusArray, $value['status']) . '</td>' .
                '<td align="center" valign="middle" border="1">' . ArrayHelper::getValue($availabilityStatusArray, $value['status_availability']) . '</td>' .
                '</tr>';
            $countItem++;
        }
        $html .= '</table>';
        $pdf->writeHTML($html);

        $pdf->Cell(0, 0, $page.' из '.$pages, 0, 0, 'R');
        $pdf->Ln(2);
        if($count > $countItem) {
            $pdf->AddPage('P', 'A4', true);
        }
        $html = '';
        $page++;
    }
}

//    $pdf->writeHTML($html);
//        $pdf->AddPage('P', 'A4', true);
//}

$pdf->lastPage();
$pdf->Output(date("d-m-Y-H-i-s") . '-pick-list.pdf', 'D');
Yii::$app->end();