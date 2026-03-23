<?php
use common\ecommerce\entities\EcommerceStock as Stock;
use common\ecommerce\defacto\pickingList\repository\PickingListRepository;
//use common\ecommerce\constants\StockOutboundStatus;

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

$sumBox = count($transferList);
if(empty($transferList)) {
     echo "<h1>Нет коробов для сборки, Вернитесь на страницу с листами сборки</h1>";
    return;
}

$keyItem = 0;
foreach ($transferList as  $ourBox=>$productInBox) {
    $keyItem++;

    $orderNumber = $orderInfo->order->client_BatchId;// $order->order_number;
    $orderID = $orderInfo->order->id;//$productsInOrder[0]['order']['orderID'];// $order->id;
    $clientID = '';//$productsInOrder[0]['order']['clientID'];// $order->client_id;
    $showPriority = '';//$productsInOrder[0]['order']['showPriority'];// $order->client_Priority;
    $showShippingCity = '';//$productsInOrder[0]['order']['showShippingCity'];// $order->client_ShippingCity;
    $showPackMessage = '';//$productsInOrder[0]['order']['showPackMessage'];// $order->client_PackMessage;
    $showGiftWrappingMessage = '';//$productsInOrder[0]['order']['showGiftWrappingMessage'];// $order->client_GiftWrappingMessage;
    $createdAt = 'xx';// $order->created_at;

    $clientTitle = 'ECom Defacto';
    $currentDateTime = new DateTime();
    $currentDateTime->setTimezone(new DateTimeZone("Asia/Almaty"));
//    $currentDateTime->setTimestamp($createdAt);

    $structure_table = '';
    if ($sumBox) {
        $headerText = '<span style="font-size: 4mm; font-weight: bold; "></span><br />Лист на сборку № <span style="font-size: 3mm; font-weight: bold; ">' . $orderNumber . '</span><br /><span style="font-size: 3mm; font-weight: bold; ">' . $clientTitle . ' / ' . $showShippingCity . ' </span><br /><span style="font-size: 3mm;">Pack Message: ' . $showPackMessage . ' </span><br /><span style="font-size: 3mm;">Gift Wrapping Message: ' . $showGiftWrappingMessage . ' </span><br /><span style="font-size: 3mm;">' . $currentDateTime->format("d-m-Y H:i") . ' </span>';

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
            '      <th width-="25%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Место') . '</strong></th>' .
            '      <th width-="25%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Короб') . '</strong></th>' .
            '      <th width-="25%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Шк товара') . '</strong></th>' .
            '      <th width-="25%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Кол-во') . '</strong></th>' .
            '      <th width-="25%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Сезон') . '</strong></th>' .
            '      <th width-="25%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms', 'Проблемный') . '</strong></th>' .
            '   </tr>';

        foreach ($productInBox as $productInfo) {
                $bgColor =  $productInfo['moveToOtherBox'] == "ДА" ? 'style="background-color:yellow;"' : '';
                $structure_table .= '<tr align="center" valign="middle">' .
                    '<td align="center" valign="middle" border="1" '.$bgColor.' >' . $productInfo['place_address_barcode'] . '</td>' .
                    '<td align="center" valign="middle" border="1" '.$bgColor.' >' . $productInfo['box_address_barcode'] . '</td>' .
                    '<td align="center" valign="middle" border="1" '.$bgColor.' >' . $productInfo['product_barcode'] . '</td>' .
                    '<td align="center" valign="middle" border="1" '.$bgColor.' >' . $productInfo['productQty'] . '</td>' .
                    '<td align="center" valign="middle" border="1" '.$bgColor.' >' . $productInfo['product_season_full'] . '</td>' .
                    '<td align="center" valign="middle" border="1" '.$bgColor.' >' . $productInfo['moveToOtherBox'] . '</td>' .
                    '</tr>';

//            'isExistProductBarcode' => 'YES'
//            'expectedProductQty' => '1'
//            'sorting' => 0
//            'isProblemBox' => 'NO'
        }

        $structure_table .= '</table>';
        $pdf->writeHTML($structure_table);
    }

    if($keyItem  < $sumBox) {
        $pdf->AddPage('P', 'A4', true);
    }
}

$pdf->lastPage();
$pdf->Output($clientTitle.'-'.$orderNumber . '-pick-list.pdf', 'D');
Yii::$app->end();
