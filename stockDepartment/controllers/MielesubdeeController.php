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

class СписокИдентификаторов {
    public $ID;
}

class Запись {

}


class ЗаявкаНаПриемку {
    /**
     * @var string
     */
    public $Идентификатор ;
    public $НомерДокументаПрообраза;
    public $СтрочноеПредставлениеДокументаПрообраза;
    public $ЗонаПриемки;
    public $ТребуетсяЭтикетирование;
    public $Дата;
    public $Статус;
    public $ОтОсновногоПоставщика;
    public $Комментарий;
    public $МастерДанныеНоменклатура;
    public $Спецификация;

    public function __construct()
    {
    }
}


class ЗаявкаНаПриемкуСписок {

    public $Запись;

    /**
     * ЗаявкаНаПриемкуСписок constructor.
     */
    public function __construct()
    {
        $this->Запись = new ЗаявкаНаПриемку();
    }

}

/**
 * MieleAPI controller
 */
class MielesubdeeController extends Controller // miele.com
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
        ini_set('soap.wsdl_cache_enabled', 0);
        return [
            'hello' => [
                'class' => 'subdee\soapserver\SoapAction',
                'serviceOptions'=>[
//                    'disableWsdlMode'=>false,
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
     * @param СписокИдентификаторов $idList
     * @return ЗаявкаНаПриемкуСписок
     * @soap
     */
    public function GetInboundOrders($idList)
    {
        return new ЗаявкаНаПриемкуСписок();
    }
}
