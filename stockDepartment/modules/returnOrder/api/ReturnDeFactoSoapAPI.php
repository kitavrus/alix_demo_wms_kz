<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 27.03.15
 * Time: 08:53
 */

namespace stockDepartment\modules\returnOrder\api;


use yii\helpers\VarDumper;

class ReturnDeFactoSoapAPI {

    /*
     * @var string url
     * */
    public $url = 'http://service.defacto.com.tr/Depo/KzkDepoNew/KzkDCIadeIslemleri.asmx?WSDL';
    /*
     * @var soap client
     * */
    public $_client;

    /*
     *
     * */
    public function connect()
    {
        return $this->_client = new \SoapClient($this->url,
            [
                'trace' => 1,
                "exceptions" => 1,
                "soap_version" => SOAP_1_1,
            ]
        );
    }

    /*
     * Call function GetKoli , IadeKabul , etc
     * @param string $method Call function name
     * @param array $params Data params for call function
     * @return array [errors, response]
     * */
    public function sendRequest($method,$params)
    {
        if(empty($this->_client)) {
            $this->_client = $this->connect();
        }

        $response = [];
        $errors = [];
        $return = [];

        try {
            $response = $this->_client->$method($params);
        } catch (\SoapFault $exception) {
            $errors ['LastRequest'] = $this->_client->__getLastRequest();
            $errors ['Exception'] = $exception;
            $errors ['ExceptionMessage'] = $exception->getMessage();
         }

        $return['errors'] = $errors;
        $return['response'] = $response;

        return $return;
    }
}