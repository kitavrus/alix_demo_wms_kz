<?php
namespace stockDepartment\modules\other\controllers;

use stockDepartment\modules\wms\managers\miele\APIService;
use stockDepartment\modules\wms\managers\miele\Repository;
use Yii;
use common\models\LoginForm;
use stockDepartment\models\PasswordResetRequestForm;
use stockDepartment\models\ResetPasswordForm;
use stockDepartment\models\SignupForm;
use stockDepartment\models\ContactForm;
use yii\base\InvalidParamException;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use stockDepartment\components\Controller;

/**
 * MieleAPI controller
 */


class MieleApiController extends Controller // miele.com
{
    public function actionTest99()
    {

        $service = new APIService();
        $data = [];
        $order = $service->GetStock($data);
//        $order = $service->GetChangedInboundOrders();
//        $service->SendInboundOrder($order['GetChangedInboundOrdersResult']['Запись']['0']);
//        VarDumper::dump($order['GetChangedInboundOrdersResult']['Запись']['0'],10,true);
       //$r =  new Repository();
        //$r->makeTestInboundChangedOrders();
//        $result = [
//            'GetInboundOrdersResult' => [
//                'Запись' => [
////                    'МастерДанныеНоменклатура' => $this->makeMasterDataNomenclatureGetOrder($data['items']),
////                    'Спецификация' => $this->makeSpecificationGetOrder($data['items'])
//                ]
//            ]
//        ];
//        $result = [];
//        $result['GetInboundOrdersResult']['Запись'] = $this->ar1();
//        $result =  $result['GetInboundOrdersResult']['Запись']+$this->ar1();
//        VarDumper::dump($result,10,true);
//        die("-die-");
//        $id = $order->order->Идентификатор.'3';
//        $order->order->Идентификатор = $id;
//        $service->SendInboundOrder($order);
        return $this->render('index',[
            'data'=>$order
        ]);
    }

    function ar1() {
        return [
            "k1"=>boolval(""),
            "k2"=>12,
        ];
    }


    public function actionTest()
    { // other/miele-api/test
        ini_set("soap.wsdl_cache_enabled", "0");
        ini_set('soap.wsdl_cache_ttl', '0');
        $params = [
            'location'=>'http://wms20.local/miel/hello',
//            'location'=>'http://wms-demo-dev.nmdx.kz/miel/hello',
            'uri'=>'http://schemas.datacontract.org/2004/07/MieleExchangeService', // targetNamespace="http://schemas.datacontract.org/2004/07/MieleExchangeService"
            'trace' => true,
            'exceptions' => true,
            "soap_version" => SOAP_1_1,
            "cache_wsdl" =>  WSDL_CACHE_NONE,
            'encoding'=>'UTF-8'
        ];

        $client =  new \SoapClient(null,$params);

        try {
//            $obj = new \stdClass();
//            $obj->list = $this->testUpdateMATMAS();
//            $list = $this->testUpdateMATMAS();
//            VarDumper::dump($client->UpdateMATMAS(['list'=>$list]), 10, true);
//            VarDumper::dump($client->UpdateMATMAS([$list]), 10, true);
//            VarDumper::dump($client->UpdateMATMAS($list), 10, true);
//            VarDumper::dump($client->UpdateMATMAS($obj), 10, true);
        } catch (\Exception $e) {
            echo $e->getMessage();
            file_put_contents('__getLastRequest.xml',print_r($client->__getLastRequest(),true)."\n"."\n",FILE_APPEND);
            file_put_contents('__getLastResponse.xml',print_r($client->__getLastResponse(),true)."\n"."\n",FILE_APPEND);
        }

        return "\n"."-return > action > Test"."\n";
    }

    public function testUpdateMATMAS() {
          $list = new \stdClass();
//        $list->Запись = new \stdClass();
//        $list->Запись[] =
            $Запись = new \stdClass();
            $Запись->МатНомер = 1;
            $Запись->Артикул = 1;
            $Запись->Наименование = "Резинка переходник FIRAT 50";
            $Запись->ВесБрутто = 1;
            $Запись->ВесНетто = 1;
            $Запись->Объем = 1;
            $Запись->Длина = 1;
            $Запись->Ширина = 1;
            $Запись->Высота = 1;
            $Запись->EAN11 = "123123123";
            $Запись->УровеньШтабелирования = 1;
            $Запись->УчетПоФабричнымНомерам = 1;
            $Запись->УчетПоКоммерческимНомерам = 1;
            $Запись->УчетПоСрокамГодности = 1;
            $Запись->ТребуетсяЭтикетирование = 1;
            $list->Запись[] = $Запись;

        return $list;
//        return [
////          'list'=>[
//              'Запись'=> [
//                  [
//                      'МатНомер'=>"1",
//                      'Артикул'=>"1",
//                      'Наименование'=>"Резинка переходник FIRAT 50",
//                      'ВесБрутто'=>0,
//                      'ВесНетто'=>0,
//                      'Объем'=>0,
//                      'Длина'=>0,
//                      'Ширина'=>0,
//                      'Высота'=>0,
//                      'EAN11'=>"123123123",
//                      'УровеньШтабелирования'=>"",
//                      'УчетПоФабричнымНомерам'=>"",
//                      'УчетПоКоммерческимНомерам'=>"",
//                      'УчетПоСрокамГодности'=>"",
//                      'ТребуетсяЭтикетирование'=>"",
//                ],
//                  [
//                      'МатНомер'=>"1",
//                      'Артикул'=>"1",
//                      'Наименование'=>"Резинка переходник FIRAT 50",
//                      'ВесБрутто'=>0,
//                      'ВесНетто'=>0,
//                      'Объем'=>0,
//                      'Длина'=>0,
//                      'Ширина'=>0,
//                      'Высота'=>0,
//                      'EAN11'=>"123123123",
//                      'УровеньШтабелирования'=>"",
//                      'УчетПоФабричнымНомерам'=>"",
//                      'УчетПоКоммерческимНомерам'=>"",
//                      'УчетПоСрокамГодности'=>"",
//                      'ТребуетсяЭтикетирование'=>"",
//                ],
//              ]
////          ]
//        ];

    }
}