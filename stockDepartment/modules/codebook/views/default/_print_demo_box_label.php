<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 03.08.14
 * Time: 12:24
 */
use stockDepartment\modules\order\models\OrderProcess;
use common\modules\store\models\Store;

//$store = Store::findOne($store_id);
//if(!empty($items)) {
    $boxNumber = 'b000123123';
    $boxBarcode = 'b000123123';
    $storeName = 'MART 2';
    $storeCity = 'Almaty';
    $boxCount = 10;
    $recipientName = 'Эрмек';
    $recipientPhone = '701 123-12-13';
    $storeStreet = 'ул. Гоголя';
    $storeStreetNumber = '12';

    ////Yii::$app->get('tcpdf');;;

//$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf = new TCPDF( 'P', 'mm', 'A4', true, 'UTF-8');

// set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('nomadex.com');
    $pdf->SetTitle('Product labels');
    $pdf->SetSubject('Product labels');
    $pdf->SetKeywords('nomadex.com, product, label');

    // remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    //set margins
    $pdf->SetMargins(2, 2, 2, true);

    //set auto page breaks
    $pdf->SetAutoPageBreak(false, 0);

    //set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // ---------------------------------------------------------

    $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');

//    $ids = [];
//    $boxItems = [];
//    $boxCount = '';
//    foreach($items as $k=>$p) {
//        if(!isset($boxItems[$p['box_barcode']])) {
//            $boxItems[$p['box_barcode']] = $p['box_barcode'];
//        }
//        $ids[] = $p['id'];
//    }

//    $boxCount = count($boxItems);
    $i = 1;
//    foreach($boxItems as $barcode) {
        $pdf->AddPage('L', 'NOMADEX70X100', true);
        $style = array(
            'position' => 'C',
            'align' => 'C',
            'stretch' => true,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'padding' => 0,
            'hpadding' => 0,
            'vpadding' => 0.5,
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
//            'font' => 'helvetica',
            'font' => 'arial',
            'fontsize' => 12,
            'stretchtext' => 4
        );

        $pdf->write1DBarcode($boxBarcode, 'C128', '', '', '', 13, 0.5, $style, 'N');
        $pdf->SetFont('arial', 'b', 14);

        $pdf->MultiCell(0,0, $storeName , 0, 'L', false, 1, '', '16', true, 0, false, true, 12.2, 'T', true);
        $pdf->SetFont('arial', 'b', 14);
        $pdf->MultiCell(0,0, $storeCity . " г.", 0, 'L', false, 1, '', '25', true, 0, false, true, 12.2, 'T', true);
//        $pdf->MultiCell(0,0, $store->country->name . " г. " .$store->city->name. " ул. " .$store->street. " д. " .$store->house, 0, 'C', false, 1, '', '17', true, 0, false, true, 12.2, 'T', true);
        $pdf->MultiCell(0,0, $recipientName . ' / ' . $recipientPhone . ' / ' . $storeStreet . ' № ' . $storeStreetNumber , 0, 'L', false, 1, '', '32', true, 0, false, true, 12.2, 'T', true);
        $pdf->SetFont('arial', 'b', 16);
        $pdf->MultiCell(0,0, $i." из ".$boxCount, 0, 'C', false, 1, '', '50', true, 0, false, true, 12.2, 'T', true);
//        $i++;
//    }

//    if(!empty($ids) && is_array($ids)) {
//        OrderProcess::updateAll(['status'=>OrderProcess::STATUS_PRINTED_BOX_LABELS],['id'=>$ids]);
//    }

    $pdf->Output($boxBarcode . '-box-label.pdf', 'D');
    Yii::$app->end();
//} else {
//    echo '<h1>Для указанного магазина '.$store->name.' Нет коробов с нераспечатанными этикетками</h1>';
//}