<?php
namespace stockDepartment\controllers;

ini_set('soap.wsdl_cache_enabled', 0);

use Yii;
use common\models\LoginForm;
use stockDepartment\models\PasswordResetRequestForm;
use stockDepartment\models\ResetPasswordForm;
use stockDepartment\models\SignupForm;
use stockDepartment\models\ContactForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use stockDepartment\components\Controller;


/**
 * MieleAPI controller
 */
class MieleController extends Controller // miele.com
{
   public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;

        return true;
//        return parent::beforeAction($action);
    }

    public function actions()
    {
        return [
            'hello' => [
//                'class' => 'subdee\soapserver\SoapAction',
                'class' => 'mongosoft\soapserver\Action',
                'serviceOptions'=>[
                    'disableWsdlMode'=>false,
                    'soapVersion'=>'1.1',
                    'encoding'=>'UTF-8'
                ],
            ],
        ];
    }

    /**
     * @param string $name Your name
     * @return string
     * @soap
     */
    public function getHello($name)
    {
        return 'Hello ' . $name;
    }

    /**
     * @param array[] $idList Get inbound orders
     * @return string
     * @soap
     */
    public function GetInboundOrders($idList)
    {
        return '';//'Hello ' . $name;
    }

    /**
     * @param array[] $params Send inbound order
     * @return boolean
     * @soap
     */
    public function SendInboundOrder($params)
    {
        $tmp = [];
        foreach ($params as $k=>$value) {
            $localKey = $k;
//            $localKey = mb_convert_encoding($k, "UTF-8", mb_detect_encoding($k,"auto",true));
            $tmp[$localKey] = $value;
        }

        file_put_contents('SendInboundOrder-DEMO-SOAP-METHOD.log',"\n".date('Y-m-d H:m:s')."\n".print_r($tmp,true),FILE_APPEND);
        return true;
    }


}
