<?php
use common\ecommerce\entities\EcommerceStock as Stock;
use common\ecommerce\defacto\pickingList\repository\PickingListRepository;
use common\ecommerce\constants\StockOutboundStatus;

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
$connection = \Yii::$app->getDb();
$sumOrders = count($outboundList);
if(empty($outboundList)) {
     echo "<h1>Нет заказов для сборки, Вернитесь на страницу с листами сборки</h1>";
    return;
}

$barcodeService = new \common\ecommerce\defacto\barcodeManager\service\BarcodeService();

//\yii\helpers\VarDumper::dump($outboundList,10,true);
//die;

$keyItem = 0;
foreach ($outboundList as $outboundId => $productsInOrder) {
    $keyItem++;

    $orderNumber = $productsInOrder[0]['order']['orderNumber'];// $order->order_number;
    $orderID = $productsInOrder[0]['order']['orderID'];// $order->id;
    $clientID = $productsInOrder[0]['order']['clientID'];// $order->client_id;
    $showPriority = $productsInOrder[0]['order']['showPriority'];// $order->client_Priority;
    $showShippingCity = $productsInOrder[0]['order']['showShippingCity'];// $order->client_ShippingCity;
    $showPackMessage = $productsInOrder[0]['order']['showPackMessage'];// $order->client_PackMessage;
    $showGiftWrappingMessage = $productsInOrder[0]['order']['showGiftWrappingMessage'];// $order->client_GiftWrappingMessage;
	$createdAt = $productsInOrder[0]['order']['createdAt'];// $order->created_at;

    $clientTitle = $productsInOrder[0]['order']['clientShipmentSource'];
    //$currentDateTime = new DateTime("now", new DateTimeZone("Asia/Almaty"));
    $currentDateTime = new DateTime();
    $currentDateTime->setTimezone(new DateTimeZone("Asia/Almaty"));
    $currentDateTime->setTimestamp($createdAt);
	
    $structure_table = '';
    if ($productsInOrder) {
        $headerText = '<span style="font-size: 4mm; font-weight: bold; ">Приоритет: ' . $showPriority . '</span><br />Лист на сборку № <span style="font-size: 3mm; font-weight: bold; ">' . $orderNumber . '</span><br /><span style="font-size: 3mm; font-weight: bold; ">' . $clientTitle . ' / ' . $showShippingCity . ' </span><br /><span style="font-size: 3mm;">Pack Message: ' . $showPackMessage . ' </span><br /><span style="font-size: 3mm;">Gift Wrapping Message: ' . $showGiftWrappingMessage . ' </span><br /><span style="font-size: 3mm;">' . $currentDateTime->format("d-m-Y H:i") . ' </span>';

        $pdf->SetFont('arial', 'B', 9);
        $pdf->writeHTMLCell(110, 0, 10, 2,$headerText, 0, 0, false, true, 'L');
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

        $pdf->write1DBarcode($orderID.'-'.$orderNumber, 'C128', '', '', 270, 20, 0.4, $style, 'M'); // T M B N
        $pdf->Ln(20);


        $structure_table = '<table width="100%" cellspacing="0" cellpadding="4" border="1">' .
            '   <tr align="center" valign="middle" >' .
            '      <th width="25%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Secondary address') . '</strong></th>' .
            '      <th width="25%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Primary address') . '</strong></th>' .
            '      <th width="25%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Product Barcode') . '</strong></th>' .
            '      <th width="25%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Кол-во') . '</strong></th>' .
            '   </tr>';

        foreach ($productsInOrder as $rowOnStock) {
                $structure_table .= '<tr align="center" valign="middle">' .
                    '<td align="center" valign="middle" border="1">' . $rowOnStock['placeAddressBarcode'] . '</td>' .
                    '<td align="center" valign="middle" border="1">' . $rowOnStock['boxAddressBarcode'] . '</td>' .
                    '<td align="left" valign="middle" border="1">' .implode('<br />',$barcodeService->getTotalProductsByProductBarcode($rowOnStock['productBarcode'])). '</td>' .
                    '<td align="left" valign="middle" border="1">' . $rowOnStock['productQty'] . '</td>' .
                    '</tr>';
        }

        Stock::updateAll([
            'status_outbound' => StockOutboundStatus::PRINTED_PICKING_LIST
        ],
            [
                'client_id' => $clientID,
                'outbound_id' => PickingListRepository::prepareIDsHelper($orderID)
            ]);

        $structure_table .= '</table>';
        $pdf->writeHTML($structure_table);
    }

    if($keyItem  < $sumOrders) {
        $pdf->AddPage('P', 'A4', true);
    }
}

$pdf->lastPage();
$pdf->Output($clientTitle.'-'.$orderNumber . '-pick-list.pdf', 'D');
Yii::$app->end();
