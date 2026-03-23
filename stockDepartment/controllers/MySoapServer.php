<?php
namespace stockDepartment\controllers;
use yii\base\Action;
use yii\web\Response;
ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 0);
ini_set('default_socket_timeout', 60 * 60 * 24);
class MySoapServer extends Action
{
    public function run()
    {
//        die("<definitions>1</definitions>");
//        header('Content-Type: text/xml;charset=utf-8');
//        \Yii::$app->response->format = Response::FORMAT_RAW;
        $server = new \SoapServer('my-yii-new.wsdl',[
//        $server = new \SoapServer('my-yii-new-test.wsdl',[
            'uri'=>'EDIService',
            'soapVersion'=>SOAP_1_1,
            'cache_wsdl'=>WSDL_CACHE_NONE,
            //'features'=>SOAP_USE_XSI_ARRAY_TYPE,
            'encoding'=>'UTF-8',
            'connection_timeout'=>60 * 60 * 24,
//            'use' => SOAP_LITERAL,
//            'style' => SOAP_RPC
        ]);
//        $server->setClass("\\stockDepartment\\controllers\\MielController");
        //new
        file_put_contents('EDIService-SERVER.xml',print_r($_SERVER,true)."\n"."\n",FILE_APPEND);
        file_put_contents('EDIService-POST.xml',print_r($_POST,true)."\n"."\n",FILE_APPEND);
        file_put_contents('EDIService-GET.xml',print_r($_GET,true)."\n"."\n",FILE_APPEND);
        file_put_contents('EDIService-REQUEST.xml',print_r($_REQUEST,true)."\n"."\n",FILE_APPEND);
        $f = file("php://input");
        file_put_contents('EDIService-PHP-INPUT.xml',print_r($f,true)."\n"."\n",FILE_APPEND);

        $server->setClass("\\stockDepartment\\controllers\\MySoapServerHandler");

//        ob_start();
        $server->handle();
//        $wsdl = ob_get_contents();
//        ob_end_clean();
        //$wsdl = file_get_contents('my-yii.wsdl');
//        file_put_contents('__MySoapServerResponse.xml',print_r($wsdl,true)."\n"."\n",FILE_APPEND);

//        header('Content-Type: text/xml;charset=utf-8');
//        header('Content-Length: ' . (function_exists('mb_strlen') ? mb_strlen($wsdl, '8bit') : strlen($wsdl)));
//        echo $wsdl;
//        die;
//        return $wsdl;
    }
}