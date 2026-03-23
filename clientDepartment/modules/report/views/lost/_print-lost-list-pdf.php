<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 30.01.15
 * Time: 15:43
 */
use common\modules\stock\models\Stock;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;

////Yii::$app->get('tcpdf');;
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

if (!empty($ids)) {

    $countOrderI = 0;

//    \yii\helpers\VarDumper::dump($items,10,true);

        $countOrderI++;

        $itemsProcessQuery = Stock::find()
//            ->select('product_barcode, primary_address, secondary_address, product_model')
            ->select('GROUP_CONCAT(id) as ids, product_barcode, primary_address, secondary_address, product_model, count(*) as items ')
            ->where([
                'in', 'id', $ids,
            ])
            ->groupBy('product_barcode, primary_address')
            ->orderBy([
//                'product_barcode'=>SORT_DESC,
                'secondary_address'=>SORT_DESC,
                'primary_address'=>SORT_DESC,
            ])
            ->asArray();

        $structure_table = '';
        $countItem = 0;
        $batchCount = 34;

        if($count = $itemsProcessQuery->count()) {

            $pages = ceil($count / $batchCount);
            $page = 1;

            foreach($itemsProcessQuery->batch($batchCount) as $values) {
                //\yii\helpers\VarDumper::dump($values, 10, true); die;

                $structure_table = '<table width="100%" cellspacing="0" cellpadding="4" border="1">' .
                    '   <tr align="center" valign="middle" >' .
                    '      <th width="25%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Secondary address') . '</strong></th>' .
                    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Primary address') . '</strong></th>' .
                    '      <th width="30%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Product Barcode') . '</strong></th>' .
                    '      <th width="20%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Product Model') . '</strong></th>' .
                    '      <th width="10%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Expected Qty') . '</strong></th>' .
                    '   </tr>';

                $clientTitle = '';
                $shopToTitle = '';

                $pdf->SetFont('arial', 'B', 10);


                $style = array(
                    'position' => 'R',
                    'align' => 'R',
                    'stretch' => true,
                    'fitwidth' => true,
                    'cellfitalign' => '',
                    'border' => false,
                    'hpadding' => 'auto',
                    'vpadding' => 'auto',
                    'fgcolor' => array(0, 0, 0),
                    'bgcolor' => false, //array(255,255,255),
                    'text' => true,
                    'font' => 'helvetica',
                    'fontsize' => 8,
                    'stretchtext' => 4
                );

                $pdf->writeHTMLCell(120, 0, 10, 6, '<h1>'.Yii::t('outbound/titles', 'Search list').'</h1>' , 0, 0, false, true, 'C');
                $pdf->Ln(15);

                $countProduct = 0;
                foreach($values as $value) {
                    $structure_table .= '<tr align="center" valign="middle">'.
                        '<td align="center" valign="middle" border="1">'.$value['secondary_address'].'</td>'.
                        '<td align="center" valign="middle" border="1">'.$value['primary_address'].'</td>'.
                        '<td align="left" valign="middle" border="1">'.$value['product_barcode'].'</td>'.
                        '<td align="center" valign="middle" border="1">'.$value['product_model'].'</td>'.
                        '<td align="center" valign="middle" border="1">'.$value['items'].'</td>'.
                        '</tr>';

                    $countItem++;
//                    $countProduct += 1;
                    $countProduct += $value['items'];
                }

                $structure_table .= '</table>';
                $pdf->writeHTML($structure_table);

                $pdf->Cell(0, 0, $page.' из '.$pages, 0, 0, 'R');
                $pdf->Ln(2);

                $structure_table = '';
                $countProduct = 0;
                $page++;
                $pdf->AddPage('P', 'A4', true);
            }

        }



}

$pdf->lastPage();
$pdf->Output(date("d-m-Y-H-i-s") . '-pick-list.pdf', 'D');
Yii::$app->end();