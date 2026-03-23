<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace app\modules\returnOrder\formHtml;

use common\modules\stock\models\Stock;
use common\modules\client\models\Client;
use common\components\BarcodeManager;
use common\modules\returnOrder\models\ReturnOrderItems;
use common\modules\returnOrder\models\ReturnTmpOrders;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use stockDepartment\modules\returnOrder\entities\TmpOrder\ReturnTmpOrder;
use stockDepartment\modules\returnOrder\entities\TmpOrder\Status;
use yii\base\Model;
use Yii;
use yii\helpers\Json;
use yii\helpers\VarDumper;

class TmpOrderForm extends Model {

    public $id;
    public $ttn = '';
    public $our_box_to_stock_barcode;
    public $client_box_barcode;
    public $clientId = Client::CLIENT_DEFACTO;

    /*
    * Validate ttn
    *
    * */
    public function checkTTN($attribute, $params)
    {
        $deliveryProposalID = $this->$attribute;
        if(!TlDeliveryProposal::find()->andWhere(['id'=>$deliveryProposalID,'client_id'=>$this->clientId])->exists()) {
            $this->addError($attribute, '<b>['.$deliveryProposalID.']</b> '.'Такой ТТН нет в нашей системе');
        }
    }

    /*
    * Validate $our_box_to_stock_barcode
    * */
    public function checkOurBoxStockBarcode($attribute, $params)
    {
        $box = $this->$attribute;
        if(!BarcodeManager::isBoxOnlyOur($box)) {
            $this->addError($attribute, '<b>['.$box.']</b> '.Yii::t('return/errors','"Это не наш короб"'));
        }

        if(ReturnTmpOrders::find()->andWhere(['our_box_to_stock_barcode'=>$box,'status'=>[Status::SCANNED,Status::COMPLETE],'client_id'=>$this->clientId])->exists() ) {
            $this->addError($attribute, '<b>['.$box.']</b> '.Yii::t('return/errors','Этот короб уже принят на склад'));
        }
		
		if(Stock::find()->andWhere(['primary_address'=>$box,'client_id'=>$this->clientId])->exists() ) {
            $this->addError($attribute, '<b>['.$box.']</b> '.Yii::t('return/errors','Этот короб уже принят на склад  и доступен'));
        }
    }

    /*
    * Validate $client_box_barcode
    * */
    public function checkClientBoxBarcode($attribute, $params)
    {
        $box = $this->$attribute;
        if(!ReturnOrderItems::find()->andWhere(['client_box_barcode'=>$box])->exists() ) {
            $this->addError($attribute, '<b>['.$box.']</b> '.'Этого короба нет в нашей системе');
        }

        if(!ReturnTmpOrder::isDefactoBox($box)) {
            $this->addError($attribute, '<b>['.$box.']</b> '.'Этот шк не дефакто');
        }

        if(ReturnTmpOrders::find()->andWhere(['client_box_barcode'=>$box,'status'=>[Status::SCANNED,Status::COMPLETE],'client_id'=>$this->clientId])->exists() ) {
            $this->addError($attribute, '<b>['.$box.']</b> '.Yii::t('return/errors','Этот короб уже принят на склад'));
        }

        $deliveryProposalID = $this->ttn;
        $deliveryProposal = TlDeliveryProposal::find()->andWhere(['id'=>$deliveryProposalID,'client_id'=>$this->clientId])->one();
        if($deliveryProposal) {
           $returnOrderItem = ReturnOrderItems::find()
                            ->andWhere(['client_box_barcode' => $box])
                            ->andWhere(['from_point_id' => $deliveryProposal->route_from])
                            ->exists();
            if (!$returnOrderItem) {
                //$this->addError($attribute, '<b>['.$box.']</b> '.Yii::t('return/errors','Этот короб не из этой накладной'));
            }
        }
    }

    /*
     * */
    public function attributeLabels()
    {
        return [
            'ttn' => "ТТН",
            'our_box_to_stock_barcode' => "Наш короб",
            'client_box_barcode' => "Короб клиента",
        ];
    }

    /*
     * */
    public function rules()
    {
        return [
              [['ttn','our_box_to_stock_barcode','client_box_barcode'], 'string',"on"=>['FIELD-TTN','FIELD-OUR-BOX-STOCK-BARCODE','FIELD-CLIENT-BOX-BARCODE']],
              [['ttn','our_box_to_stock_barcode','client_box_barcode'], 'trim',"on"=>['FIELD-TTN','FIELD-OUR-BOX-STOCK-BARCODE','FIELD-CLIENT-BOX-BARCODE']],

              [['ttn'],"checkTTN",'on'=>'FIELD-TTN'],
              [['ttn'],"required",'on'=>'FIELD-TTN'],

              [['our_box_to_stock_barcode'], 'checkOurBoxStockBarcode','on'=>'FIELD-OUR-BOX-STOCK-BARCODE'],
              [['our_box_to_stock_barcode','ttn'], 'required','on'=>'FIELD-OUR-BOX-STOCK-BARCODE'],

              [['client_box_barcode'], 'checkClientBoxBarcode','on'=>'FIELD-CLIENT-BOX-BARCODE'],
              [['our_box_to_stock_barcode','ttn','client_box_barcode'], 'required','on'=>'FIELD-CLIENT-BOX-BARCODE'],

              // UPDATE
              [['our_box_to_stock_barcode','client_box_barcode'], 'string',"on"=>['UPDATE']],
              [['our_box_to_stock_barcode','client_box_barcode'], 'required',"on"=>['UPDATE']],
              [['our_box_to_stock_barcode'], 'unique','targetClass'=>ReturnTmpOrders::className(),'filter'=>function($q) {
                  return $q->andWhere('id != :id',[':id'=>$this->id]);
              },"on"=>['UPDATE']],
              [['client_box_barcode'], 'unique','targetClass'=>ReturnTmpOrders::className(),'filter'=>function($q) {
                  return $q->andWhere('id != :id',[':id'=>$this->id]);
              },"on"=>['UPDATE']],

        ];
    }
}