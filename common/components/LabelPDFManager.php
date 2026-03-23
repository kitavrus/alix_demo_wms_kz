<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 06.03.15
 * Time: 16:43
 */

namespace common\components;

use kartik\helpers\Html;
use Yii;
use yii\helpers\VarDumper;

class LabelPDFManager {

    /*
     * Box label
     * @param \TCPDF $pdf
     * @param array $params
     * @return PDF
     * */
    public static function OutboundBoxLabelDefacto($pdf,$params)
    {
        /*@var \TCPDF $pdf */
        $codePart1 = substr($params['boxBarcode'], 0, 2);
        $codePart2 = substr($params['boxBarcode'], 2, 4);
        $codePart3 = substr($params['boxBarcode'], 6, 4);
        $codePart4 = substr($params['boxBarcode'], 10, 4);

        $codeFormatText = $codePart1.''.$codePart2.'<b>'.$codePart3.''.$codePart4.'</b>';

        $recipientTitle = 'Получатель';
        $senderTitle = 'Отправитель';
        $ttnTitle = 'ТТН № ';
        $numberPlaces = $params['currentBoxNumber'] . ' / '.$params['boxTotal'];
//        $numberPlaces = "1000" . ' / '."1000";
        $pdf->AddPage('L', 'NOMADEX70X100', true);


//        $style = array(
//            'border'=>false,
//            'padding'=>0,
//            'hpadding'=>0,
//            'vpadding'=>0.5,
//            'fgcolor'=>array(0, 0, 0),
//            'bgcolor'=>false,
////		'text'=>true,//Текст снизу
//            'text'=>false,//Текст снизу
//            'font'=>'dejavusans',
//            'fontsize'=>10,//Размер шрифта
//            'stretchtext'=>4,//Растягивание
//            'stretch'=>true,
//            'fitwidth'=>true,
//            'cellfitalign'=>'',
//            'position'=>'L',
//            'align'=>'C',
//        );
        $style = array(
            'position' => 'L',
            'align' => 'L',
            'stretch' => false,
            'fitwidth' => false,
            'cellfitalign' => '',
            'border' => false,
            'padding' => 0,
            'hpadding' => 0,
            'vpadding' => 0.5,
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => false,
            'font' => 'arial',
            'fontsize' => 11,
            'stretchtext' => 4
        );

        $params['codeBase'] = $params['boxBarcode'] = '1';
        $pdf->SetFont('arial', 'b', 12);
//        $pdf->write1DBarcode($params['codeBase'], 'C128', 0, 0, 100, 5, 0.5, $style, 'L');
        $pdf->writeHTML('<span><span style="font-weight: bold; ">Мест: </span>'.$numberPlaces.'</span></span>');

//	Вертикальный штрихкод
        $pdf->StartTransform();
        $pdf->SetXY(0,0);
        $pdf->Rotate(-90,47,47);
//        $pdf->write1DBarcode($params['codeBase'], 'C128', '', '-5', '55', 8, 1.3, $style, 'L');

        $pdf->StopTransform();

//        $pdf->SetFont('dejavusans', '', 8);
        $pdf->SetFont('arial', 'b', 8);

        $htmlBox = '<table border="1" cellspacing="0" cellpadding="0"  style="line-height: 2; width: 100%">'.
            '<tr>'
            .'<td style="text-align: center; width: 70%">'
            .'<span style="font-size: 5mm; font-weight: bold; ">'.$params['city'].'</span>'
            .'</td>'
            .'<td style="font-size: 5mm; text-align: center; width: 30%; ">'
            .$params['pointCode'].
            '</td>'
            .'</tr>';
        $htmlBox .= '</table>';
        $pdf->writeHTMLCell('83', '8', 1, 6, $htmlBox,false);


        $htmlBox = '<table border="0" cellspacing="0" cellpadding="1"  style="line-height: 1; width: 100%;" >';
        $htmlBox .= '<tr>'
            .'<td><span style="font-size: 4mm; font-weight: bold; ">'
            .$recipientTitle.':</span><br />'
            .$params['recipientText']
            .(isset($params['outboundOrderNumber']) ? '<br />Заказ: '.$params['outboundOrderNumber'] : '')
            .'</td>'
            .'</tr>';
        $htmlBox .= '</table>';
        $pdf->writeHTMLCell('81', '23', 2, 17, $htmlBox,true);


        $htmlBox = '<table border="0" cellspacing="0" cellpadding="0"  style="line-height: 1; width: 100%">';
        $htmlBox .= '<tr>'
            .'<td><span style="font-size: 4mm; font-weight: bold; ">'. $senderTitle.': </span> '
            .$params['senderText'].' <span style="font-size: 4mm; font-weight: bold; ">'.(isset($params['ourBoxBarcode']) ? '' : '').'</span>'
            .'</td>'
            .'</tr>';
        $htmlBox .= '</table>';
        $pdf->writeHTMLCell('81', '19', 2, 41, $htmlBox,true);

        $htmlBox = '<table border="0" cellspacing="0" cellpadding="0"  style="line-height: 2; width: 100%;">' .
            '<tr >'
            . '<td style="text-align: center; width: 100%" colspan="2">'
            . '<span>От ' . Yii::$app->formatter->asDate(time(),'php:d.m.Y').' </span><span><b>' .$ttnTitle. '</b> ' . $params['ttnFormatText'] . '</span>'
            . '</td>'
            . '</tr>';
        $htmlBox .= '</table>';

        $pdf->writeHTMLCell('81', '4', 2, 61, $htmlBox,false);


        //	Вертикальный штрихкод
        $pdf->StartTransform();
        $pdf->SetXY(0,0);
        $pdf->Rotate(-90,47,47);
//
        $pdf->SetFont('arial', 'b', 12);
        $codeFormatText = '-';
        $htmlBox = '<table border="0" cellspacing="0" cellpadding="0"  style="line-height: 2; width: 100%;">' .
            '<tr >'
            . '<td style="text-align: left; width: 35%" >'
            . '<span style="font-weight: bold;"></span>' . $codeFormatText. ''
            . '</td>'
            . '<td  style="text-align: left; width: 65%; "><span style="font-weight: bold; ">Мест: </span>'
            . $numberPlaces .
            '</td>'
            . '</tr>';
        $htmlBox .= '</table>';

        $pdf->writeHTMLCell('62', '4', 2,4, $htmlBox,false);

        $pdf->StopTransform();

//        $pdf->SetFont('dejavusans', '', 6);
        $pdf->SetFont('arial', 'b', 6);
        $pdf->writeHTMLCell('20', '4', 85, 65, 'NMDX.COM',false);

        return $pdf;
    }

    /*
  * Box label
  * @param object $pdf
  * @param array $params
  * @return PDF
  * */
    public static function BoxLabel($pdf,$params)
    {
        $codePart1 = substr($params['boxBarcode'], 0, 2);
        $codePart2 = substr($params['boxBarcode'], 2, 4);
        $codePart3 = substr($params['boxBarcode'], 6, 4);
        $codePart4 = substr($params['boxBarcode'], 10, 4);

        $codeFormatText = $codePart1.''.$codePart2.'<b>'.$codePart3.''.$codePart4.'</b>';

        $recipientTitle = 'Получатель';
        $senderTitle = 'Отправитель';
        $ttnTitle = 'ТТН № ';
        $numberPlaces = $params['currentBoxNumber'] . ' / '.$params['boxTotal'];
        $pdf->AddPage('L', 'NOMADEX70X100', true);


//        $style = array(
//            'border'=>false,
//            'padding'=>0,
//            'hpadding'=>0,
//            'vpadding'=>0.5,
//            'fgcolor'=>array(0, 0, 0),
//            'bgcolor'=>false,
////		'text'=>true,//Текст снизу
//            'text'=>false,//Текст снизу
//            'font'=>'dejavusans',
//            'fontsize'=>10,//Размер шрифта
//            'stretchtext'=>4,//Растягивание
//            'stretch'=>true,
//            'fitwidth'=>true,
//            'cellfitalign'=>'',
//            'position'=>'L',
//            'align'=>'C',
//        );
        $style = array(
            'position' => 'L',
            'align' => 'L',
            'stretch' => false,
            'fitwidth' => false,
            'cellfitalign' => '',
            'border' => false,
            'padding' => 0,
            'hpadding' => 0,
            'vpadding' => 0.5,
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => false,
            'font' => 'arial',
            'fontsize' => 11,
            'stretchtext' => 4
        );

        $params['codeBase'] = $params['boxBarcode'];
        $pdf->write1DBarcode($params['codeBase'], 'C128', 0, 0, 100, 5, 0.5, $style, 'L');

//	Вертикальный штрихкод
        $pdf->StartTransform();
        $pdf->SetXY(0,0);
        $pdf->Rotate(-90,47,47);
        $pdf->write1DBarcode($params['codeBase'], 'C128', '', '-5', '55', 8, 1.3, $style, 'L');
        $pdf->StopTransform();

//        $pdf->SetFont('dejavusans', '', 8);
        $pdf->SetFont('arial', 'b', 8);

        $htmlBox = '<table border="1" cellspacing="0" cellpadding="0"  style="line-height: 2; width: 100%">'.
            '<tr>'
            .'<td style="text-align: center; width: 70%">'
            .'<span style="font-size: 5mm; font-weight: bold; ">'.$params['city'].'</span>'
            .'</td>'
            .'<td style="font-size: 5mm; text-align: center; width: 30%; ">'
            .$params['pointCode'].
            '</td>'
            .'</tr>';
        $htmlBox .= '</table>';
        $pdf->writeHTMLCell('83', '8', 1, 6, $htmlBox,false);


        $htmlBox = '<table border="0" cellspacing="0" cellpadding="1"  style="line-height: 1; width: 100%;" >';
        $htmlBox .= '<tr>'
            .'<td><span style="font-size: 4mm; font-weight: bold; ">'
            .$recipientTitle.':</span><br />'
            .$params['recipientText']
            .(isset($params['outboundOrderNumber']) ? '<br />Заказ: '.$params['outboundOrderNumber'] : '')
            .'</td>'
            .'</tr>';
        $htmlBox .= '</table>';
        $pdf->writeHTMLCell('81', '23', 2, 17, $htmlBox,true);


        $htmlBox = '<table border="0" cellspacing="0" cellpadding="0"  style="line-height: 1; width: 100%">';
        $htmlBox .= '<tr>'
            .'<td><span style="font-size: 4mm; font-weight: bold; ">'. $senderTitle.': </span> '
            .$params['senderText'].' <span style="font-size: 4mm; font-weight: bold; ">'.(isset($params['ourBoxBarcode']) ? $params['ourBoxBarcode'] : '').'</span>'
            .'</td>'
            .'</tr>';
        $htmlBox .= '</table>';
        $pdf->writeHTMLCell('81', '19', 2, 41, $htmlBox,true);

        $htmlBox = '<table border="0" cellspacing="0" cellpadding="0"  style="line-height: 2; width: 100%;">' .
            '<tr >'
            . '<td style="text-align: center; width: 100%" colspan="2">'
            . '<span>От ' . Yii::$app->formatter->asDate(time(),'php:d.m.Y').' </span><span><b>' .$ttnTitle. '</b> ' . $params['ttnFormatText'] . '</span>'
            . '</td>'
            . '</tr>';
        $htmlBox .= '</table>';

        $pdf->writeHTMLCell('81', '4', 2, 61, $htmlBox,false);


        //	Вертикальный штрихкод
        $pdf->StartTransform();
        $pdf->SetXY(0,0);
        $pdf->Rotate(-90,47,47);

        $htmlBox = '<table border="0" cellspacing="0" cellpadding="0"  style="line-height: 2; width: 100%;">' .
            '<tr >'
            . '<td style="text-align: left; width: 60%" >'
            . '<span style="font-weight: bold;">Короб: </span>' . $codeFormatText. ''
            . '</td>'
            . '<td  style="text-align: left; width: 40%; "><span style="font-weight: bold; ">Мест: </span>'
            . $numberPlaces .
            '</td>'
            . '</tr>';
        $htmlBox .= '</table>';

        $pdf->writeHTMLCell('62', '4', 2,4, $htmlBox,false);

        $pdf->StopTransform();

//        $pdf->SetFont('dejavusans', '', 6);
        $pdf->SetFont('arial', 'b', 6);
        $pdf->writeHTMLCell('20', '4', 85, 65, 'NMDX.COM',false);

        return $pdf;
    }

    /*
    * Box label
    * @param object $pdf
    * @param array $params
    * @return PDF
    * */
    public static function outboundBoxLabel($pdf,$params)
    {
//        $codePart1 = substr($params['boxBarcode'], 0, 2);
//        $codePart2 = substr($params['boxBarcode'], 2, 4);
//        $codePart3 = substr($params['boxBarcode'], 6, 4);
//        $codePart4 = substr($params['boxBarcode'], 10, 4);
            $codeFormatText = $params['boxBarcode'];

        $recipientTitle = 'Получатель';
        $senderTitle = 'Отправитель';
        $ttnTitle = 'ТТН № ';
        $numberPlaces = $params['currentBoxNumber'] . ' / '.$params['boxTotal'];
        $pdf->AddPage('L', 'NOMADEX70X100', true);

//        VarDumper::dump($numberPlaces,10,true);
//        die;

        $style = array(
            'border'=>false,
            'padding'=>0,
            'hpadding'=>0,
            'vpadding'=>0.5,
            'fgcolor'=>array(0, 0, 0),
            'bgcolor'=>false,
//		'text'=>true,//Текст снизу
            'text'=>false,//Текст снизу
            'font'=>'freeserif',
            'fontsize'=>10,//Размер шрифта
            'stretchtext'=>4,//Растягивание
            'stretch'=>true,
            'fitwidth'=>true,
            'cellfitalign'=>'',
            'position'=>'L',
            'align'=>'C',
        );


        $pdf->write1DBarcode($params['codeBase'], 'C128', 0, 0, '70', 5, 1.5, $style, 'L');

//	Вертикальный штрихкод
        $pdf->StartTransform();
        $pdf->SetXY(0,0);
        $pdf->Rotate(-90,47,47);
        $pdf->write1DBarcode($params['codeBase'], 'C128', '', '-5', '55', 8, 1.3, $style, 'L');
        $pdf->StopTransform();

//        $pdf->SetFont('dejavusans', '', 8);
        $pdf->SetFont('freeserif', 'b', 8);

        $htmlBox = '<table border="1" cellspacing="0" cellpadding="0"  style="line-height: 2; width: 100%">'.
            '<tr>'
            .'<td style="text-align: center; width: 70%">'
            .'<span style="font-size: 5mm; font-weight: bold; ">'.$params['city'].'</span>'
            .'</td>'
            .'<td style="font-size: 5mm; text-align: center; width: 30%; ">'
            .$params['pointCode'].
            '</td>'
            .'</tr>';
        $htmlBox .= '</table>';
        $pdf->writeHTMLCell('83', '8', 1, 6, $htmlBox,false);


        $htmlBox = '<table border="0" cellspacing="0" cellpadding="1"  style="line-height: 1; width: 100%;" >';
        $htmlBox .= '<tr>'
            .'<td><span style="font-size: 4mm; font-weight: bold; ">'
            .$recipientTitle.':</span><br />'
            .$params['recipientText']
            .(isset($params['outboundOrderNumber']) ? '<br />Заказ: '.$params['outboundOrderNumber'] : '')
            .'</td>'
            .'</tr>';
        $htmlBox .= '</table>';
        $pdf->writeHTMLCell('81', '23', 2, 17, $htmlBox,true);


        $htmlBox = '<table border="0" cellspacing="0" cellpadding="0"  style="line-height: 1; width: 100%">';
        $htmlBox .= '<tr>'
            .'<td><span style="font-size: 4mm; font-weight: bold; ">'. $senderTitle.': </span> '
            .$params['senderText']
            .'</td>'
            .'</tr>';
        $htmlBox .= '</table>';
        $pdf->writeHTMLCell('81', '19', 2, 41, $htmlBox,true);

        $htmlBox = '<table border="0" cellspacing="0" cellpadding="0"  style="line-height: 2; width: 100%;">' .
            '<tr >'
            . '<td style="text-align: center; width: 100%" colspan="2">'
            . '<span>От ' . Yii::$app->formatter->asDate(time(),'php:d.m.Y').' </span><span><b>' .$ttnTitle. '</b> ' . $params['ttnFormatText'] . '</span>'
            . '</td>'
            . '</tr>';
        $htmlBox .= '</table>';

        $pdf->writeHTMLCell('81', '4', 2, 61, $htmlBox,false);


        //	Вертикальный штрихкод
        $pdf->StartTransform();
        $pdf->SetXY(0,0);
        $pdf->Rotate(-90,47,47);

        $htmlBox = '<table border="0" cellspacing="0" cellpadding="0"  style="line-height: 2; width: 100%;">' .
            '<tr >'
            . '<td style="text-align: left; width: 60%" >'
            . '<span style="font-weight: bold;">Заказ: </span>' . $codeFormatText
            . '</td>'
            . '<td  style="text-align: left; width: 40%; "><span style="font-weight: bold; ">Мест: </span>'
            . $numberPlaces .
            '</td>'
            . '</tr>';
        $htmlBox .= '</table>';

        $pdf->writeHTMLCell('62', '4', 2,4, $htmlBox,false);

        $pdf->StopTransform();

//        $pdf->SetFont('dejavusans', '', 6);
        $pdf->SetFont('freeserif', 'b', 6);
        $pdf->writeHTMLCell('20', '4', 85, 65, 'NMDX.COM',false);

        return $pdf;
    }

    /*
     * Box label
     * @param object $pdf
     * @param array $params
     * @return PDF
     * */
    public static function BoxContent($pdf,$params,$boxContent)
    {
        $numberPlaces = $params['currentBoxNumber'] . ' / '.$params['boxTotal'];
        $pdf->AddPage('P', 'a4', true);
        $pdf->SetFont('arial', 'b', 15);
        $pdf->writeHTMLCell('60', '1', 10, 10, 'Заказ №: '.$params['order_number'],false);
        $pdf->writeHTMLCell('60', '1', 80, 10, 'Короб №: '.$params['box_barcode'],false);
        $pdf->writeHTMLCell('60', '1', 180, 10, 'Лист: '.$numberPlaces);
        $pdf->Ln(15);
        $pdf->writeHTMLCell('80', '1', 10, 20, 'Куда: '.$params['point_to'],false);
        $pdf->SetFont('arial', 'b', 10);
        $pdf->Ln(15);
        $structure_table = '<table width="100%" cellspacing="0" cellpadding="4" border="1">' .
            '   <tr align="center" valign="middle" >' .
            '      <th width="20%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms','Product Barcode') . '</strong></th>' .
            '      <th width="20%" align="center" valign="middle" border="1"><strong>' . Yii::t('outbound/forms','Product Model') . '</strong></th>' .
            '      <th width="20%" align="center" valign="middle" border="1"><strong>' . Yii::t('product/forms','Color') . '</strong></th>' .
            '      <th width="20%" align="center" valign="middle" border="1"><strong>' . Yii::t('product/forms','Size') . '</strong></th>' .
            '      <th width="20%" align="center" valign="middle" border="1"><strong>' . Yii::t('product/forms','Product qty') . '</strong></th>' .
            '   </tr>';
        $total = 0;
        if (!empty($boxContent)) {
            foreach ($boxContent as $item) {
                $structure_table.='<tr>'.
                    '<td>'.  $item['product_barcode'].'</td>'.
                    '<td>'.  $item['model'].'</td>'.
                    '<td>'.  $item['color'].'</td>'.
                    '<td>'.  $item['size'].'</td>'.
                    '<td>'.  $item['qty'].'</td>'.
                    '</tr>';

                $total +=$item['qty'];
            }
        }
        $structure_table.= '<tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>Всего: '.$total.'</td>
    </tr>';

        $structure_table .= '</table>';
//echo $structure_table;
        $pdf->writeHTML($structure_table);
        return $pdf;
    }

    public static function BoxHtmlLabel($html, $params)
    {


        $codePart1 = substr($params['boxBarcode'], 0, 2);
        $codePart2 = substr($params['boxBarcode'], 2, 4);
        $codePart3 = substr($params['boxBarcode'], 6, 4);
        $codePart4 = substr($params['boxBarcode'], 10, 4);

        $codeFormatText = $codePart1.''.$codePart2.'<b>'.$codePart3.''.$codePart4.'</b>';

        $recipientTitle = 'Получатель';
        $senderTitle = 'Отправитель';
        $ttnTitle = 'ТТН № ';
        $numberPlaces = $params['currentBoxNumber'] . ' / '.$params['boxTotal'];
        $html .= Html::beginTag('div',['class' => "NOMADEX70X100"]);

        $html.= Html::img($params['hBarcode'], ['class'=>'h-label-barcode']);

        $html .= '<table border="1" cellspacing="0" cellpadding="0"  style="line-height: 2; width: 80%;">'.
            '<tr>'
                .'<td style="text-align: center; width: 70%">'
                .'<span style="font-size: 5mm; font-weight: bold; ">'.$params['city'].'</span>'
                .'</td>'
                .'<td style="font-size: 5mm; text-align: center; width: 30%; ">'
                .$params['pointCode'].
                '</td>'
            .'</tr>';
        $html .= '</table>';

        $html .= '<table border="1" cellspacing="0" cellpadding="1"  style="line-height: 1; width: 80%;" >';
        $html .= '<tr>'
                    .'<td><span style="font-size: 4mm; font-weight: bold; ">'
                    .$recipientTitle.':</span><br />'
                    .$params['recipientText']
                    .(isset($params['outboundOrderNumber']) ? '<br />Заказ: '.$params['outboundOrderNumber'] : '')
                    .'</td>'
                 .'</tr>';
        $html .= '</table>';

        $html .= '<table border="1" cellspacing="0" cellpadding="0"  style="line-height: 1; width: 80%">';
        $html .= '<tr>'
                    .'<td><span style="font-size: 4mm; font-weight: bold; ">'. $senderTitle.': </span> '
                    .$params['senderText']
                    .'</td>'
                .'</tr>';
        $html .= '</table>';

        $html .= '<table border="0" cellspacing="0" cellpadding="0"  style="line-height: 2; width: 80%;">' .
                '<tr >'
                    . '<td style="text-align: center; width: 100%" colspan="2">'
                    . '<span>От ' . Yii::$app->formatter->asDate(time(),'php:d.m.Y').' </span><span><b>' .$ttnTitle. '</b> ' . $params['ttnFormatText'] . '</span>'
                    . '<p class="logo"><b>NMDX.com</b></p>'
                    . '</td>'
                . '</tr>';
        $html .= '</table>';


        $html .= '<div class = "ver">'
                    . '<span class ="inside-ver" style="font-weight: bold;">Короб: </span>' . $codeFormatText. ''
                    . '<span class ="inside-ver" style="font-weight: bold; ">Мест: </span>'. $numberPlaces;
        $html .= '</div>';


        $html.= Html::img($params['vBarcode'],['class'=>'v-label-barcode']);
        $html .= Html::endTag('div');


       return $html;
        //$pdf->AddPage('L', 'NOMADEX70X100', true);


//        $style = array(
//            'border'=>false,
//            'padding'=>0,
//            'hpadding'=>0,
//            'vpadding'=>0.5,
//            'fgcolor'=>array(0, 0, 0),
//            'bgcolor'=>false,
////		'text'=>true,//Текст снизу
//            'text'=>false,//Текст снизу
//            'font'=>'dejavusans',
//            'fontsize'=>10,//Размер шрифта
//            'stretchtext'=>4,//Растягивание
//            'stretch'=>true,
//            'fitwidth'=>true,
//            'cellfitalign'=>'',
//            'position'=>'L',
//            'align'=>'C',
//        );


       // $pdf->write1DBarcode($params['codeBase'], 'C128', 0, 0, '70', 5, 1.5, $style, 'L');

//	Вертикальный штрихкод
//        $pdf->StartTransform();
//        $pdf->SetXY(0,0);
//        $pdf->Rotate(-90,47,47);
//        $pdf->write1DBarcode($params['codeBase'], 'C128', '', '-5', '55', 8, 1.3, $style, 'L');
//        $pdf->StopTransform();
//
////        $pdf->SetFont('dejavusans', '', 8);
//        $pdf->SetFont('arial', 'b', 8);

//        $htmlBox = '<table border="1" cellspacing="0" cellpadding="0"  style="line-height: 2; width: 100%">'.
//            '<tr>'
//            .'<td style="text-align: center; width: 70%">'
//            .'<span style="font-size: 5mm; font-weight: bold; ">'.$params['city'].'</span>'
//            .'</td>'
//            .'<td style="font-size: 5mm; text-align: center; width: 30%; ">'
//            .$params['pointCode'].
//            '</td>'
//            .'</tr>';
//        $htmlBox .= '</table>';
        //$pdf->writeHTMLCell('83', '8', 1, 6, $htmlBox,false);


//        $htmlBox = '<table border="0" cellspacing="0" cellpadding="1"  style="line-height: 1; width: 100%;" >';
//        $htmlBox .= '<tr>'
//            .'<td><span style="font-size: 4mm; font-weight: bold; ">'
//            .$recipientTitle.':</span><br />'
//            .$params['recipientText']
//            .(isset($params['outboundOrderNumber']) ? '<br />Заказ: '.$params['outboundOrderNumber'] : '')
//            .'</td>'
//            .'</tr>';
//        $htmlBox .= '</table>';
//        $pdf->writeHTMLCell('81', '23', 2, 17, $htmlBox,true);


//        $htmlBox = '<table border="0" cellspacing="0" cellpadding="0"  style="line-height: 1; width: 100%">';
//        $htmlBox .= '<tr>'
//            .'<td><span style="font-size: 4mm; font-weight: bold; ">'. $senderTitle.': </span> '
//            .$params['senderText']
//            .'</td>'
//            .'</tr>';
//        $htmlBox .= '</table>';
//        $pdf->writeHTMLCell('81', '19', 2, 41, $htmlBox,true);
//
//        $htmlBox = '<table border="0" cellspacing="0" cellpadding="0"  style="line-height: 2; width: 100%;">' .
//            '<tr >'
//            . '<td style="text-align: center; width: 100%" colspan="2">'
//            . '<span>От ' . Yii::$app->formatter->asDate(time(),'php:d.m.Y').' </span><span><b>' .$ttnTitle. '</b> ' . $params['ttnFormatText'] . '</span>'
//            . '</td>'
//            . '</tr>';
//        $htmlBox .= '</table>';
//
//        $pdf->writeHTMLCell('81', '4', 2, 61, $htmlBox,false);


        //	Вертикальный штрихкод
//        $pdf->StartTransform();
//        $pdf->SetXY(0,0);
//        $pdf->Rotate(-90,47,47);
//
//        $htmlBox = '<table border="0" cellspacing="0" cellpadding="0"  style="line-height: 2; width: 100%;">' .
//            '<tr >'
//            . '<td style="text-align: left; width: 60%" >'
//            . '<span style="font-weight: bold;">Короб: </span>' . $codeFormatText. ''
//            . '</td>'
//            . '<td  style="text-align: left; width: 40%; "><span style="font-weight: bold; ">Мест: </span>'
//            . $numberPlaces .
//            '</td>'
//            . '</tr>';
//        $htmlBox .= '</table>';
//
//        $pdf->writeHTMLCell('62', '4', 2,4, $htmlBox,false);
//
//        $pdf->StopTransform();
//
////        $pdf->SetFont('dejavusans', '', 6);
//        $pdf->SetFont('arial', 'b', 6);
//        $pdf->writeHTMLCell('20', '4', 85, 65, 'NMDX.COM',false);
//
//        return $pdf;
    }
}