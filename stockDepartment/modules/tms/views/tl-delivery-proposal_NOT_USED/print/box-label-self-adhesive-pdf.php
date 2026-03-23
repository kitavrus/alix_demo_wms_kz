<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 24.02.15
 * Time: 10:15
 */

use common\modules\client\models\ClientEmployees;
use common\modules\transportLogistics\components\TLHelper;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposal */
/* @var $codeBookModel common\modules\codebook\models\Codebook */

////Yii::$app->get('tcpdf');;;

$managersNamesTo = '';

if($routeTo = $model->routeTo) {
    // находим всех директоров магазина и отправляем им имейлы
    $clientEmployees = ClientEmployees::find()
        ->where([
            'deleted'=>0,
            'client_id'=>$model->client_id,
            'store_id'=>$routeTo->id,
            'manager_type'=>[
                ClientEmployees::TYPE_BASE_ACCOUNT,
                ClientEmployees::TYPE_DIRECTOR,
                ClientEmployees::TYPE_DIRECTOR_INTERN,
            ]
        ])
        ->all();

    foreach($clientEmployees as $item) {
        $managersNamesTo .= $item->first_name.' '.$item->last_name.' / '.$item->phone_mobile.' '.$item->phone."<br />";
    }
}

$city = $routeTo->city->name;
$pointCode = $routeTo->id;
$recipientText = $routeTo->name . ' '.(!empty($routeTo->shop_code) ? $routeTo->shop_code : '').' '. ((!empty($routeTo->shopping_center_name) && $routeTo->shopping_center_name != '-')  ? '  [ ТЦ ' . $routeTo->shopping_center_name . ' ] ' : '') . ' ' . $routeTo->street. ' '.$routeTo->house;;
$recipientText .= '<br />'.$managersNamesTo;


$routeFrom = $model->routeFrom;
$senderText = $routeFrom->city->name. ' / ' . $routeFrom->name . ' '.(!empty($routeFrom->shop_code) ? $routeFrom->shop_code : '').' '. ((!empty($routeFrom->shopping_center_name) && $routeFrom->shopping_center_name != '-')  ? '  [ ТЦ ' . $routeFrom->shopping_center_name . ' ] ' : '') . " " . $routeFrom->street . ' ' . $routeFrom->house;;

$ttn = sprintf("%014d",$model->id);

$codePart1 = substr($ttn, 0, 2);
$codePart2 = substr($ttn, 2, 4);
$codePart3 = substr($ttn, 6, 4);
$codePart4 = substr($ttn, 10, 4);
//$codePart5 = substr($ttn, 14, 4);

$ttnFormatText = $codePart1.''.$codePart2.' <b>'.$codePart3.' '.$codePart4.'</b>';
//die('-TTN-');


$boxQty = $model->number_places_actual ? $model->number_places_actual : $model->number_places;

$from = $codeBookModel->barcode;
$to = $from + $boxQty;
$pref = $codeBookModel->cod_prefix;
$codeBookModel->barcode = $to;
$codeBookModel->save(false);
$from += 1;

$boxes = [];

for ($i = $from; $i <= $to; $i++) {
    $boxes[] = $pref . sprintf("%010d", $i);
}


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


//$boxCount = count($boxes);
$boxTotal = count($boxes);

foreach ($boxes as $iBox=>$barcode) {

//    $code = $barcode;

    $currentBoxNumber = $iBox+1;
    $codeBase = \common\components\BarcodeManager::createBaseBarcode($currentBoxNumber,$barcode,$ttn,$boxTotal);

    $params = [];
    $params['city'] = $city;
    $params['pointCode'] = $pointCode;
    $params['recipientText'] = $recipientText;
    $params['senderText'] = $senderText;
    $params['currentBoxNumber'] = $currentBoxNumber;
    $params['boxTotal'] = $boxTotal;
    $params['boxBarcode'] = $barcode;
    $params['codeBase'] = $codeBase;
    $params['ttnFormatText'] = $ttnFormatText;

    $pdf = \common\components\LabelPDFManager::BoxLabel($pdf,$params);


//    $code = $barcode;
//    $codePart1 = substr($code, 0, 2);
//    $codePart2 = substr($code, 2, 4);
//    $codePart3 = substr($code, 6, 4);
//    $codePart4 = substr($code, 10, 4);
//
//    $codeFormatText = $codePart1.''.$codePart2.' <b>'.$codePart3.' '.$codePart4.'</b>';



//    $pdf->AddPage('L', 'NOMADEX70X100', true);

//    $recipientTitle = 'Получатель';
//    $senderTitle = 'Отправитель';
//    $ttnTitle = 'ТТН № ';
//    $numberCurrent = $iBox+1;
//    $numberPlaces = $numberCurrent . ' / '.$boxCount;
//    $codeBase = '001-00-020-003';

    // S: Create base code
//    $bbID = 0;
//    if($bb = \common\modules\codebook\models\BaseBarcode::find()->select('id')->orderBy(['id'=>SORT_DESC])->scalar()) {
//        $bbID = $bb;
//    }
//
//    $bb = new \common\modules\codebook\models\BaseBarcode();
//    $bb->base_barcode = sprintf("%014d",$bbID+1);
//    $bb->box_number = $numberCurrent;
//    $bb->box_barcode = $code;
//    $bb->ttn_barcode = $ttn;
//    $bb->box_total = $boxCount;
//    $bb->save(false);
//
//    $codeBase = $bb->base_barcode;
    // E: Create base code


//    $style = array(
//        'border'=>false,
//        'padding'=>0,
//        'hpadding'=>0,
//        'vpadding'=>0.5,
//        'fgcolor'=>array(0, 0, 0),
//        'bgcolor'=>false,
//        'text'=>false,//Текст снизу
//        'font'=>'dejavusans',
//        'fontsize'=>10,//Размер шрифта
//        'stretchtext'=>4,//Растягивание
//        'stretch'=>true,
//        'fitwidth'=>true,
//        'cellfitalign'=>'',
//        'position'=>'L',
//        'align'=>'C',
//    );

//    $pdf->write1DBarcode($codeBase, 'C128', 0, 0, '70', 13, 1.5, $style, 'L');

//	Вертикальный штрихкод
//    $pdf->StartTransform();
//    $pdf->SetXY(0,0);
//    $pdf->Rotate(-90,52,48);
//    $pdf->write1DBarcode($codeBase, 'C128', '', '', '55', 13, 1.3, $style, 'L');
//    $pdf->StopTransform();
//
//    $pdf->SetFont('dejavusans', '', 8);
//
//    $htmlBox = '<table border="1" cellspacing="0" cellpadding="0"  style="line-height: 2; width: 100%">'.
//        '<tr>'
//        .'<td style="text-align: center; width: 70%">'
//        .'<span style="font-size: 5mm; font-weight: bold; ">'.$city.'</span>'
//        .'</td>'
//        .'<td style="font-size: 5mm; text-align: center; width: 30%; ">'
//        .$pointCode.
//        '</td>'
//        .'</tr>';
//    $htmlBox .= '</table>';
//    $pdf->writeHTMLCell('85', '10', 1, 15, $htmlBox,false);
//
//    $htmlBox = '<table border="0" cellspacing="0" cellpadding="1"  style="line-height: 1; width: 100%;" >';
//    $htmlBox .= '<tr>'
//        .'<td><b>'
//        .$recipientTitle.':</b><br />'
//        .$recipientText
//        .'</td>'
//        .'</tr>';
//    $htmlBox .= '</table>';
//    $pdf->writeHTMLCell('83', '14', 2, 27, $htmlBox,true);
//
//
//    $htmlBox = '<table border="0" cellspacing="0" cellpadding="0"  style="line-height: 1; width: 100%">';
//    $htmlBox .= '<tr>'
//        .'<td><b>'.$senderTitle.': </b> '
//        .$senderText
//        .'</td>'
//        .'</tr>';
//    $htmlBox .= '</table>';
//    $pdf->writeHTMLCell('83', '8', 2, 43, $htmlBox,true);
//
//    $htmlBox = '<table border="0" cellspacing="0" cellpadding="0"  style="line-height: 2; width: 100%;">' .
//        '<tr >'
//        . '<td style="text-align: center; width: 100%" colspan="2">'
//        . '<span>От ' . Yii::$app->formatter->asDate(time(),'php:d.m.Y').' </span><span><b>' . $ttnTitle. '</b> ' . $ttnFormatText . '</span>'
//        . '</td>'
//        . '</tr>';
//    $htmlBox .= '</table>';

//    $pdf->writeHTMLCell('83', '4', 2, 53, $htmlBox,true);
//
//
//    $htmlBox = '<table border="0" cellspacing="0" cellpadding="0"  style="line-height: 2; width: 100%;">' .
//        '<tr >'
//        . '<td style="text-align: center; width: 60%" >'
//        . '<span style="font-weight: bold; ">Короб № </span>' . $codeFormatText. ''
//        . '</td>'
//        . '<td  style="text-align: center; width: 40%; "><span style="font-weight: bold; ">Мест: </span>'
//        . $numberPlaces .
//        '</td>'
//        . '</tr>';
//    $htmlBox .= '</table>';
//
//    $pdf->writeHTMLCell('83', '4', 2, 62, $htmlBox,true);

//    $pdf->SetFont('dejavusans', '', 6);
//    $pdf->writeHTMLCell('20', '4', 85, 65, 'NMDX.COM',false);

}

$pdf->lastPage();

$pdf->Output(time() . '-box-label.pdf', 'D');

Yii::$app->end();