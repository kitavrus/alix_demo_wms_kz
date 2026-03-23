<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 03.11.14
 * Time: 09:34
 */
use common\modules\client\models\ClientEmployees;
use common\modules\transportLogistics\components\TLHelper;
use clientDepartment\modules\client\components\ClientManager;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposal */
/* @var $codeBookModel common\modules\codebook\models\Codebook */

////Yii::$app->get('tcpdf');;;

$managersNamesTo = '';
$storeTitleTo = '';
$addCustomerPhone = $model->getExtraFieldValueByName('customer_phone_2');
$addRecipientPhone = $model->getExtraFieldValueByName('recipient_phone_2');

if(is_object($model->routeTo)){
    $sender = $model->routeTo;
    $managersNamesTo .= $sender->contact_full_name.' / '. $sender->phone_mobile.'/'.$addRecipientPhone."<br />";
    $storeTitleTo = $sender->title.', '.$sender->floor.' этаж';
}

// From Store
$managersNamesFrom = '';
$storeTitleFrom = '';
$weight = Yii::$app->formatter->asDecimal($model->kg_actual, 2);
if(is_object($model->routeFrom)) {
    $recipient = $model->routeFrom;
    $managersNamesFrom .= $recipient->contact_full_name.' / '.$recipient->phone_mobile.'/'.$addCustomerPhone."<br />";
    $storeTitleFrom = $recipient->title.', '.$recipient->floor.' этаж';
}

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

$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//$pdf->SetFont('dejavusans', '', 8);
$pdf->SetFont('arial', 'b', 8);
$pdf->SetMargins(10, 5, 10);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->setFooterMargin(0);

$config_array = array(
    1=>array(
        'coordinates'=> array(
            'x'=>10,
            'y'=>5,
        ),
        'position'=> 'L',
        'multicells'=>array(
            1=>array(
                'x'=>10,
                'y'=>30,
            ),
            2=>array(
                'x'=>10,
                'y'=>45,
            ),
            3=>array(
                'x'=>5,
                'y'=>85,
            ),
        ),
        'rotate'=>array(
            'angle'=>-90,
            'x'=>71,
            'y'=>67,
        ),

    ),
    2=>array(
        'coordinates'=> array(
            'x'=>150,
            'y'=>5,
        ),
        'position'=> 'R',
        'multicells'=>array(
            1=>array(
                'x'=>190,
                'y'=>30,
            ),
            2=>array(
                'x'=>190,
                'y'=>45,
            ),
            3=>array(
                'x'=>185,
                'y'=>85,
            ),
        ),
        'rotate'=>array(
            'angle'=>90,
            'x'=>222,
            'y'=>71,
        ),
    ),
    3=>array(
        'coordinates'=> array(
            'x'=>0,
            'y'=>110,
        ),
        'position'=> 'L',
        'multicells'=>array(
            1=>array(
                'x'=>10,
                'y'=>135,
            ),
            2=>array(
                'x'=>10,
                'y'=>150,
            ),
            3=>array(
                'x'=>5,
                'y'=>180,
            ),
        ),
        'rotate'=>array(
            'angle'=>-90,
            'x'=>19,
            'y'=>120,
        ),
    ),
    4=>array(
        'coordinates'=> array(
            'x'=>150,
            'y'=>110,
        ),
        'position'=> 'R',
        'multicells'=>array(
            1=>array(
                'x'=>190,
                'y'=>135,
            ),
            2=>array(
                'x'=>190,
                'y'=>150,
            ),
            3=>array(
                'x'=>185,
                'y'=>180,
            ),
        ),
        'rotate'=>array(
            'angle'=>90,
            'x'=>275,
            'y'=>123,
        ),
    ),
);

$style = array(
    'border'=>false,
    'padding'=>0,
    'hpadding'=>0,
    'vpadding'=>0.5,
    'fgcolor'=>array(0, 0, 0),
    'bgcolor'=>false,
    'text'=>true,//Текст снизу
    'font'=>'dejavusans',
    'fontsize'=>15,//Размер шрифта
    'stretchtext'=>4,//Растягивание
    'stretch'=>true,
    'fitwidth'=>true,
    'cellfitalign'=>'',
);

$boxCount = count($boxes);
$ttn = sprintf("%014d",$model->id);
$i = 1;
$num = 1;

foreach ($boxes as $barcode) {

    if($i == 1){ //Если первый то добавляем страницу и рисуем линии
        $pdf->AddPage();
        $pdf->Line(145,0,145,210);//вертикальная
        $pdf->Line(0,105,297,105);//горизонтальная
    }

    // S: Create base code
    $ttn = sprintf("%014d",$model->id);
    $bbID = 0;
    if($bb = \common\modules\codebook\models\BaseBarcode::find()->select('id')->orderBy(['id'=>SORT_DESC])->scalar()) {
        $bbID = $bb;
    }

    $bb = new \common\modules\codebook\models\BaseBarcode();
    $bb->base_barcode = sprintf("%014d",$bbID+1);
    $bb->box_number = $boxCount;
    $bb->box_barcode = $barcode;
    $bb->ttn_barcode = $ttn;
    $bb->box_total = $boxCount;
    $bb->save(false);

    $codeBase = $bb->box_barcode;
    // E: Create base code




    $pdf->SetXY($config_array[$i]['coordinates']['x'],$config_array[$i]['coordinates']['y']);
    $style['position'] = $config_array[$i]['position'];
    $style['align'] = $config_array[$i]['position'];

    $pdf->write1DBarcode($codeBase, 'C128', '', '', '100', 25, 1.5, $style, 'C');
//    $pdf->SetFont('dejavusans', '', 10);
    $pdf->SetFont('arial', 'b', 10);


    $pdf->MultiCell(100,0,
        "Отправитель :"
        ."<br />"
        . $storeTitleFrom
        . "<br />"
        . $managersNamesFrom
        . "<br />"
        . "Получатель :"
        . "<br />"
        . $storeTitleTo
        . "<br />"
        .$managersNamesTo
        . "<br />"
        .'ТТН №'
        .$ttn
        . "<br />"
        ."Общий вес: "
        . $weight. " кг",
        0, 'L', false, 1,
        $config_array[$i]['multicells'][1]['x'],
        $config_array[$i]['multicells'][1]['y'],
        true, 0, true, true, 12.2, 'T', true);


//    $pdf->SetFont('dejavusans', '', 10);
//    $pdf->SetFont('arial', 'b', 10);
//    $pdf->MultiCell(100,0,
//
//        0, 'L', false, 1,
//        $config_array[$i]['multicells'][2]['x'],
//        $config_array[$i]['multicells'][2]['y']+25,
//        true, 0, true, true, 12.2, 'T', true);

    //$pdf->SetFont('arial', 'b', 14);
    $pdf->MultiCell(100,0,
       '<h1>' . $num . " из " . $boxCount. '</h1>',
        0, 'C', false, 1,
        $config_array[$i]['multicells'][3]['x'],
        $config_array[$i]['multicells'][3]['y'],
        true, 0, true, true, 12.2, 'T', false);

    //Вертикальный штрихкод
    $pdf->StartTransform();
    $pdf->SetXY(0,0);
    $pdf->Rotate(
        $config_array[$i]['rotate']['angle'],
        $config_array[$i]['rotate']['x'],
        $config_array[$i]['rotate']['y']
    );
    $pdf->write1DBarcode($codeBase, 'C128', '', '', '90', 25, 1.3, $style, 'C');
    $pdf->StopTransform();


    if($i == 4){
        $i = 1;
    } else {
        $i++;
    }
    $num++;
}

$pdf->lastPage();

$pdf->Output(time() . '-box-label.pdf', 'D');

Yii::$app->end();