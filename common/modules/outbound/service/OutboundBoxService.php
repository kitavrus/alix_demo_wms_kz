<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 14.06.2018
 * Time: 8:41
 */

namespace common\modules\outbound\service;

use common\modules\outbound\models\OutboundBox;
use stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2Manager;
use yii\helpers\Json;

class OutboundBoxService
{
    const LC_BARCODE_DEFACTO = 'LC_BARCODE_DEFACTO';
    const WAYBILL_NUMBER_DEFACTO = 'WAYBILL_NUMBER_DEFACTO';

    public static function getLcBarcodeFromDefacto($qty) {

        $apiManager = new DeFactoSoapAPIV2Manager();
        $result = $apiManager->CreateLcBarcode($qty);
        if($result['HasError']) {
            echo $result['ErrorMessage'];
            file_put_contents("CreateLcBarcode-Error.log",print_r($result,true)."\n",FILE_APPEND);
            die();
            return 0;
        }
		
        if(count($result['Data']) == 1) {
            return [$result['Data']];
        }

       return $result['Data'];
    }

    public static function checkExist($boxBarcode) {
        return OutboundBox::find()->andWhere(['our_box'=>$boxBarcode])->orWhere(['client_box'=>$boxBarcode])->exists();
    }

    public static function getAllByBarcode($boxBarcode) {
        return OutboundBox::find()->andWhere(['our_box'=>$boxBarcode])->all();
    }

    public static function getByBarcode($boxBarcode) {
        return OutboundBox::find()->andWhere(['our_box'=>$boxBarcode])->orWhere(['client_box'=>$boxBarcode])->limit(1)->one();
    }

    public function getOurBoxByBarcode($clientBoxBarcode) {
         if($box = $this->getByBarcode($clientBoxBarcode)){
             return $box->our_box;
         }
        return '';
    }

    public static function isOurBoxByBarcode($ourBoxBarcode) {
        return OutboundBox::find()->andWhere(['our_box'=>$ourBoxBarcode])->exists();
    }

    public static function isClientBoxByBarcode($clientBoxBarcode) {
        return OutboundBox::find()->andWhere(['client_box'=>$clientBoxBarcode])->exists();
    }

    public function getClientBoxByBarcode($ourBoxBarcode) {
        if($box = $this->getByBarcode($ourBoxBarcode)){
            return $box->client_box;
        }

        return $ourBoxBarcode;
    }

    public static function create($our_box,$client_box) {
        $s = new OutboundBox();
        $s->our_box = $our_box;
        $s->client_box = $client_box;
		
		//echo $client_box."<br>";
        $s->save(false);
    }

    public static function addLcAndWaybillByBoxBarcode($boxBarcode,$lc,$waybill) {
        $attribute = [
          self::LC_BARCODE_DEFACTO=>$lc,
          self::WAYBILL_NUMBER_DEFACTO=>$waybill
        ];
        OutboundBox::updateAll(['client_extra_json'=>Json::encode($attribute)],'our_box = :BoxBarcode OR client_box = :BoxBarcode',[":BoxBarcode"=>$boxBarcode]);
    }

    public function getLcAndWaybillByBoxBarcode($boxBarcode) {
        $box = $this->getByBarcode($boxBarcode);
        if($box) {
            return Json::decode($box->client_extra_json);
        }

        return [
            self::LC_BARCODE_DEFACTO=>'',
            self::WAYBILL_NUMBER_DEFACTO=>''
        ];
    }

}