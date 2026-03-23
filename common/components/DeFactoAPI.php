<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 23.02.15
 * Time: 15:45
 */

namespace common\components;


class DeFactoAPI {


    //////////////
    ////////////// PROCESS_IMPORT
    ////////////// TODO Загрузить приходные накладные

    /*
     * Загузка приходной накладной по номерку который мы получаем по почте
     *
     * */
    function getUrunKabulBilgileri ($invoice)
    {
        $client = new \SoapClient('http://service.defacto.com.tr/depo/KzkDepo/KzkDcDepoOperations.asmx?WSDL',
            array('trace'=>1,
                "exceptions" => 1,
                "soap_version" => SOAP_1_1,
            )
        );

        $params = array(
            'request' => array(
                'ForeignInvoice' => '',
                'CrossDockType' => '',
                'BarkodId' => '',
                'Barkod' => '',
                'Miktar' => '',
                'DepoId' => '',
                'IrsaliyeNo' => '',
                'RezerveId' => '',
                'KoliId' => '',
                'KoliKargo' => '',
                'KoliDesi'  => '',
                'PartiNo'  => '',
                'UserName'=>'',
                'Password'=> '',
                'YurtDisiIrsaliyeNo' => $invoice
            )
        );
        try {
            $response = $client->UrunKabulBilgileri($params);
            //var_dump($response);

            $result = $response->UrunKabulBilgileriResult->KZKDCUrunKabulBilgileriDto;
            $str = "BelgeId,IrsaliyeSeri,IrsaliyeNo,Barkod,BarkodId,DepoId,KarsiDepoId,KoliId,KoliKapatmaBarkod,CrossDock,UrunId,KisaKod,Miktar\r\n";
//        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_DATABASE);
//        if ($mysqli->connect_errno) {
//            echo "Could not connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
//        }

            foreach($result as $res) {
//              public 'BelgeId' => int 345674
//              public 'IrsaliyeSeri' => string 'D  ' (length=3)
//              public 'IrsaliyeNo' => int 22759
//              public 'Barkod' => string '9000001541336' (length=13)
//              public 'BarkodId' => int 1253891
//              public 'DepoId' => int 100
//              public 'KarsiDepoId' => int 509
//              public 'KoliId' => int 1
//              public 'KoliKapatmaBarkod' => string '000000217376' (length=12)
//              public 'CrossDock' => string 'P' (length=1)
//              public 'UrunId' => int 1265946
//              public 'KisaKod' => string 'C2065AI' (length=7)
//              public 'Miktar' => float 1
                $str .= $res->BelgeId.','.trim($res->IrsaliyeSeri).','.$res->IrsaliyeNo.','.$res->Barkod.','.$res->BarkodId.','.$res->DepoId.','
                    .$res->KarsiDepoId.','.$res->KoliId.','.$res->KoliKapatmaBarkod.','.$res->CrossDock.','.$res->UrunId.','
                    .$res->KisaKod.','.$res->Miktar."\r\n";




//            $resp = $mysqli->query("INSERT INTO turunkabulbilgileri(BelgeId,IrsaliyeSeri,IrsaliyeNo,Barkod,BarkodId,DepoId,
//                KarsiDepoId,KoliId,KoliKapatmaBarkod,CrossDock,UrunId,KisaKod,Miktar,
//                YurtDisiIrsaliyeNo, MiktarFact, insertDate, confirmDate)
//            VALUES(
//            '".$res->BelgeId."',
//            '".trim($res->IrsaliyeSeri)."',
//            '".$res->IrsaliyeNo."',
//            '".$res->Barkod."',
//            '".$res->BarkodId."',
//            '".$res->DepoId."',
//            '".$res->KarsiDepoId."',
//            '".$res->KoliId."',
//            '".$res->KoliKapatmaBarkod."',
//            '".$res->CrossDock."',
//            '".$res->UrunId."',
//            '".$res->KisaKod."',
//            '".$res->Miktar."',
//
//
//            '".$invoice."',
//            '0',
//            '".date('Y-m-d H:i:s')."',
//            ''


//            )


//            ");



            }
//        $mysqli->close();

            $filename = 'dump_'.time().'.csv';
            $handle = fopen('csv/'.$filename, "w");

            fwrite($handle, $str);
            fclose($handle);

            return $filename;
        }

        catch (\SoapFault $exception) {
            echo "<strong>Error: </strong>\n";
            echo '<pre>';
            print_r($exception);
            echo '</pre>';
        }

        return true;
    }


    //////////////
    ////////////// process_confirm
    ////////////// TODO Подтвердить приходные накладные

    /*
     *
     *
     * */
    function getUrunOnKabulTamamlandi($YurtDisiIrsaliyeNo) {

        echo 'Запуск UrunOnKabulTamamlandi<br/>';

        $client = new \SoapClient('http://service.defacto.com.tr/depo/KzkDepo/KzkDcDepoOperations.asmx?WSDL',
            array('trace'=>1,
                "exceptions" => 1,
                "soap_version" => SOAP_1_1,
            )
        );

        $params = array(
            'request' => array(
                'ForeignInvoice' => '',
                'UserName'=>'',
                'Password'=> '',
                'CrossDockType' => '',
                'Barkod' => '',
                'BarkodId' => '',
                'Miktar' => '',
                'DepoId' => '',
                'IrsaliyeNo' => '',
                'IrsaliyeSeri' => '',
                'RezerveId' => '',
                'KoliId' => '',
                'KoliKargo' => '',
                'KoliDesi'  => '',
                'PartiNo'  => '',
                'YurtDisiIrsaliyeNo' => $YurtDisiIrsaliyeNo
            )
        );

        try {
            $response = $client->UrunOnKabulTamamlandi($params);
            $result = $response->UrunOnKabulTamamlandiResult;
            return $result;

        }
        catch (\SoapFault $exception) {
            echo "<strong>Error: </strong>\n";
            echo '<pre>';
            print_r($exception);
            echo '</pre>';
        }
        return true;
    }

    /*
     *
     *
     * */
    public function urunOnKabul($array) {

        echo "Запуск urunOnKabul <br/>";
        $YurtDisiIrsaliyeNo = $array[0];
        $Barkod = $array[1];
        $CrossDockType = $array[2];
        $Miktar = $array[3];

        $client = new \SoapClient('http://service.defacto.com.tr/depo/KzkDepo/KzkDcDepoOperations.asmx?WSDL',
            array('trace'=>1,
                "exceptions" => 1,
                "soap_version" => SOAP_1_1,
            )
        );

        $params = array(
            'request' => array(

                'UserName'=>'',
                'Password'=> '',
                'YurtDisiIrsaliyeNo' => $YurtDisiIrsaliyeNo,
                'CrossDockType' => $CrossDockType,
                'Barkod' => $Barkod,
                'Miktar' => $Miktar,
            )
        );

        try {
            $response = $client->UrunOnKabul($params);

//        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_DATABASE);
//        if ($mysqli->connect_errno) {
//            echo "Could not connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
//        }
            return true;

        }
        catch (\SoapFault $exception) {
            echo "<strong>Error: </strong>\n";
            echo '<pre>';
            print_r($exception);
            echo '</pre>';
        }

        return true;
    }

    //////////////
    ////////////// process_reserve_dagitim
    ////////////// TODO Данные по резервированным товарам

    /*
     *
     *
     * */
    function getReserveDagitim($array) {

        $reserve_dagitim_list = array();

        foreach ($array as $a) {
            $reserve_id = $a[0];
            $barkod = $a[1];
            $miktar = $a[2];
            $irsaliye_no = $a[3];
            $koli_id = $a[4];
            $koli_desi = $a[5];
            $tmp = array(
                'IrsaliyeNo' => $irsaliye_no,
                'IrsaliyeSeri' => 'E',
                'RezerveId' => $reserve_id,
                'KoliId' => $koli_id,
                'Barkod' => $barkod,
                'KoliKargoEtiketId'=> 1,
                'Miktar' => $miktar,
                'KoliDesi'  => $koli_desi,
            );
            $reserve_dagitim_list['RezerveDagitimDto'][] = $tmp;

        }

        $params = array(
            'request' => array(
                'UserName'=>'',
                'Password'=> '',
                'RezerveDagitimListe' => $reserve_dagitim_list,
            )
        );
//    $params = array_to_objecttree($params);
//    var_dump($params);
//    die();

        $client = new \SoapClient('http://service.defacto.com.tr/depo/KzkDepo/KzkDcDepoOperations.asmx?WSDL',
            array('trace'=>1,
                "exceptions" => 1,
                "soap_version" => SOAP_1_1,
            )
        );
        try {
//        $result = 'OK';
            $response = $client->RezerveDagitim($params);
            $result = $response->RezerveDagitimResult;
            echo 'RESULT: ';
            echo $result.'<br>';
//
//        echo "REQUEST:\n" ;
//        echo'<pre>';
//        echo htmlspecialchars($client->__getLastRequest());
//        echo'</pre>';


        }
        catch (\SoapFault $exception) {
            echo "<strong>Error: </strong>\n";
            echo '<pre>';
            print_r($exception);
            echo '</pre>';
        }
    }

    //////////////
    ////////////// process_reserved_list
    ////////////// TODO Подтверждение по резервированным товарам

    /*
     *@param integer $part_num
     *
     * */
    public function process_reserved_list($part_num)
    {
        $client = new \SoapClient('http://service.defacto.com.tr/depo/KzkDepo/KzkDcDepoOperations.asmx?WSDL',
            array('trace'=>1,
                "exceptions" => 1,
                "soap_version" => SOAP_1_1,
            )
        );

        $YurtDisiIrsaliyeNo = '';
//        $PartiNo = (int) $_POST['part_num'];
        $PartiNo = (int) $part_num;
        $ReserveId = '';
        $params = array(
            'request' => array(
                'ForeignInvoice' => '',
                'UserName'=>'',
                'Password'=> '',
                'CrossDockType' => '',
                'Barkod' => '',
                'BarkodId' => '',
                'Miktar' => '',
                'DepoId' => '',
                'IrsaliyeNo' => '',
                'IrsaliyeSeri' => '',
                'RezerveId' => '',
                'KoliId' => '',
                'KoliKargo' => '',
                'KoliDesi'  => '',
                'PartiNo'  => $PartiNo,
                'YurtDisiIrsaliyeNo' => ''
            )
        );

        try {

            $response = $client->RezerveDetayList($params);
            $result = $response->RezerveDetayListResult->KZKDCRezerveDetayBilgileriDto;

            $row_data = array();
            $csv_str = "RezerveId,CariId,CariYerId,Ad,BarkodId,Barkod,Miktar,PartiNo,PartiOnayTarih\r\n";
            foreach($result as $res) {
                $row_data[] = "('".$res->RezerveId."', '".$res->CariId."', '".$res->CariYerId."', '".$res->Ad."',
        '".$res->BarkodId."', '".$res->Barkod."', '".$res->Miktar."', '".$res->PartiNo."', '".$res->PartiOnayTarih."',
        '0', '".date('Y-m-d H:i:s')."')";
                $csv_str .= $res->RezerveId.','.$res->CariId.','.$res->CariYerId.','.$res->Ad.','.$res->BarkodId.','.$res->Barkod.','
                    .$res->Miktar.','.$res->PartiNo.','.$res->PartiOnayTarih."\r\n";
            }

            $filename = 'dump_reservelist_'.time().'.csv';
            $handle = fopen('csv/'.$filename, "w");

            fwrite($handle, $csv_str);
            fclose($handle);
            echo '<a href="/csv/'.$filename.'">Скачать csv-файл</a>';

        }

        catch (\SoapFault $exception) {
            echo "<strong>Error: </strong>\n";
            echo '<pre>';
            print_r($exception);
            echo '</pre>';
        }



        return true;
    }





}