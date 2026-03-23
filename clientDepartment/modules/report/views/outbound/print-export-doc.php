<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 08.11.2016
 * Time: 9:52
 */
////Yii::$app->get('tcpdf');;

//$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//$pdf->SetFont('arial', '', 8); //ok
//$pdf->SetMargins(10, 5, 10);
//$pdf->setPrintHeader(false);
//$pdf->setPrintFooter(false);
//// set default monospaced font
//$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
//S: BODY


foreach ($dataInBoxes as $partyNumber => $shopNames) {
    foreach ($shopNames as $shopName => $boxes) {
        $pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetFont('arial', '', 8); //ok
        $pdf->SetMargins(10, 5, 10);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
// set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $orderOnOnePage = 2;

        foreach ($boxes as $LcBarcode => $dataInBox) {

            if ($orderOnOnePage % 2 == 0) {
                $pdf->AddPage();
            } else {
                $pdf->SetAbsY(150);
            } // 2 3 4 5


            file_put_contents("dataInBoxesTTT.log",print_r($dataInBox,true)."\n",FILE_APPEND);

            $html = '';
            $grossWeight = 0;
            $netWeight = 0;
            $cartonNumber = $dataInBox['box']['boxNumber'];
            $madeIn = implode(',',$dataInBox['box']['madeIn']);
            $importer = $dataInBox['box']['importer'];

            $html .= '<table width="100%" style="padding:2px" >
            <tr>
                <td width="15%" style="font-weight: bold">Importer:</td>
                <td width="85%">' . $importer . '</td>
            </tr>
        </table>';

//            $pdf->writeHTML($html, true, false, true, false, '');
//
//            $html = '';
            $html .= '<table width="100%" style="padding:2px" >
            <tr>
                <td width="15%" style="font-weight: bold">Exporter:</td>
                <td width="85%">Defacto Perakende Ticaret A.Ş./Turkey</td>
            </tr>
        </table>';

//            $pdf->writeHTML($html, true, false, true, false, '');
//
//            $html = '';
            $html .= '<table width="100%" style="padding:2px" >
            <tr>
                <td width="15%" style="font-weight: bold">Made In:</td>
                <td width="85%">' . trim($madeIn, ',') . '</td>
            </tr>
        </table>';
//            $pdf->writeHTML($html, true, false, true, false, '');
//
//            $html = '';
            $html .= '<table width="100%" style="padding:2px" >
            <tr>
                <td width="15%" style="font-weight: bold">Trade Mark:</td>
                <td width="15%">DeFacto</td>
                <td width="70%" align="right">' . $LcBarcode . '</td>
            </tr>
        </table>';
            $pdf->writeHTML($html, true, false, true, false, '');
            $pdf->ln(1);

            $html = '';
            $html .= '<table width="100%" style="padding:2px" >
            <tr>
                <td width="12%" style="font-weight: bold">Product</td>
                <td width="14%" style="font-weight: bold">Color</td>
                <td width="12%" style="font-weight: bold">Classification</td>
                <td width="8%"  style="font-weight: bold">Qty</td>
                <td width="40%" style="font-weight: bold">Size Breakdown</td>
                <td width="7%"  style="font-weight: bold">GrossW</td>
                <td width="7%"  style="font-weight: bold">NetW</td>
            </tr>';

            foreach ($dataInBox['lots'] as $item) {

                $grossWeight += $item['grossW'];
                $netWeight += $item['netW'];

                $html .= '
            <tr>
                <td width="12%" style="border: 0.4px solid black">' . $item['product'] . '</td>
                <td width="14%" style="border: 0.4px solid black">' . $item['color'] . '</td>
                <td width="12%" style="border: 0.4px solid black">' . $item['classification'] . '</td>
                <td width="8%" style="border: 0.4px solid black">' . $item['qty'] . '</td>
                <td width="40%" style="border: 0.4px solid black">' . $item['sizeBreakdown'] . '</td>
                <td width="7%" style="border: 0.4px solid black">' . '0' . '</td>
                <td width="7%" style="border: 0.4px solid black">' . '0' . '</td>
            </tr>';
            }

            $html .= '</table>';

            $pdf->writeHTML($html, true, false, true, false, '');
            $pdf->ln(1);

            $html = '';
            $html .= '<table width="100%" style="padding:2px" >
            <tr>
                <td width="20%" style="font-weight: bold">Gross Weight:</td>
                <td width="20%">' . $grossWeight . '</td>
                <td width="60%" align="right" style="font-weight: bold">Carton Number: ' . $cartonNumber . '</td>
            </tr>
        </table>';
//            $pdf->writeHTML($html, true, false, true, false, '');
//
//            $html = '';
            $html .= '<table width="100%" style="padding:2px" >
            <tr>
                <td width="20%" style="font-weight: bold">Net Weight:</td>
                <td width="80%">' . $netWeight . '</td>
            </tr>
        </table>';

            $pdf->writeHTML($html, true, false, true, false, '');

            $html = '';
            $html .= '<table width="100%" style="padding:2px" >
            <tr>
                <td width="100%">' . $mappingOurBobBarcodeToDeFacto[$LcBarcode].' / '.$partyNumber.' / '.$shopName. '</td>
            </tr>
        </table>';

            $pdf->writeHTML($html, true, false, true, false, '');

            $orderOnOnePage += 1;
        }

        $pdf->lastPage();
        $dirPath = 'uploads/import-export/'.date('Ymd').'/'.$partyNumber;
        $fileName = $partyNumber.'-'.$shopName.'-import-export.pdf';
        \yii\helpers\BaseFileHelper::createDirectory($dirPath);
        $fullPath = $dirPath.'/'.$fileName;
        $pdf->Output($fullPath, 'F');
        unset($pdf);
    }
}

echo "DONE".time();
//E: BODY
//$pdf->lastPage();

//$pdf->Output(time() . '-box-export-doc.pdf', 'D');
die;
