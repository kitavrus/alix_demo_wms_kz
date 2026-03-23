<?php
namespace stockDepartment\modules\wms\models\miele\form;


use common\components\BarcodeManager;
use stockDepartment\modules\wms\models\miele\validation\Validation;
use stockDepartment\modules\wms\models\miele\validation\ValidationInbound;
use yii\base\Model;
use Yii;
use yii\helpers\VarDumper;

class InboundForm extends Model
{
//    public $client_id;
    public $order_id;
    public $our_box_barcode;
    public $product_barcode;
    public $fab_barcode;

    private $validation;
    //
    public function __construct($config = []) {
        parent::__construct($config);
        $this->validation = new ValidationInbound();
    }
    //
    public function rules()
    {
        return [
//            [['client_id'], 'integer'],

            [['order_id'], 'required', 'on' => 'onChangeOrderHandler'],
            [['order_id'], 'integer', 'on' => 'onChangeOrderHandler'],

            [['order_id','our_box_barcode'], 'required', 'on' => 'onOurBoxBarcodeHandler'],
            [['our_box_barcode'], 'string', 'on' => 'onOurBoxBarcodeHandler'],
            [['our_box_barcode'], 'trim','on'=>'onOurBoxBarcodeHandler'],
            [['our_box_barcode'], 'isOurBoxBarcode','on'=>'onOurBoxBarcodeHandler'],

            [['order_id','product_barcode','our_box_barcode'], 'required', 'on' => 'onProductBarcodeHandler'],
            [['our_box_barcode'], 'string', 'on' => 'onProductBarcodeHandler'],
            [['our_box_barcode'], 'trim','on'=>'onProductBarcodeHandler'],
            [['product_barcode'], 'string', 'on' => 'onProductBarcodeHandler'],
            [['product_barcode'], 'trim','on'=>'onProductBarcodeHandler'],
            [['product_barcode'], 'isProductBarcode','on'=>'onProductBarcodeHandler'],

            [['order_id','product_barcode','our_box_barcode','fab_barcode'], 'required', 'on' => 'onFabBarcodeHandler'],
            [['product_barcode'], 'isFabBarcode','on'=>'onFabBarcodeHandler'],
            [['product_barcode'], 'trim','on'=>'onFabBarcodeHandler'],
            [['product_barcode'], 'string', 'on' => 'onFabBarcodeHandler'],
            [['our_box_barcode'], 'trim','on'=>'onFabBarcodeHandler'],
            [['our_box_barcode'], 'string', 'on' => 'onFabBarcodeHandler'],
            [['fab_barcode'], 'string','on'=>'onFabBarcodeHandler'],
            [['fab_barcode'], 'trim','on'=>'onFabBarcodeHandler'],

            [['order_id'], 'required', 'on' => 'onCleanOurBoxHandler'],
            [['our_box_barcode'], 'isCleanOurBox','on'=>'onCleanOurBoxHandler'],
            [['our_box_barcode'], 'trim','on'=>'onCleanOurBoxHandler'],

            [['order_id'], 'required', 'on' => 'onPrintDiffHandler'],
            [['order_id'], 'integer','on'=>'onPrintDiffHandler'],
        ];
    }
    //
    public function isOurBoxBarcode($attribute, $params)
    {
        $ourBoxBarcode = $this->$attribute;
        if(!$this->validation->isBoxOnlyOur($ourBoxBarcode)) {
            $this->addError($attribute, '<b>['.$ourBoxBarcode.']</b> '.Yii::t('inbound/errors','"Это не штрихкод нашего короба"'));
        }
    }
    //
    public function isProductBarcode($attribute, $params)
    {
        $productBarcode = $this->$attribute;
        if(!$this->validation->isProductBarcode($productBarcode)) {
            $this->addError($attribute, '<b>['.$productBarcode.']</b> '.Yii::t('inbound/errors','"Это не штрихкод товара Miele"'));
        }

        $inboundId = $this->order_id;
        if($this->validation->isExtraBarcodeInOrder($inboundId,$productBarcode)) {
            $this->addError($attribute, '<b>['.$productBarcode.']</b> '.Yii::t('inbound/errors','"Это лишний товар в накладной"'));
        }

        if(!$this->validation->IsExistBarcodeInOrder($inboundId,$productBarcode)) {
            $this->addError($attribute, '<b>['.$productBarcode.']</b> '.Yii::t('inbound/errors','"Этого товара нет в этой накладной"'));
        }
    }
    //
    public function isFabBarcode($attribute, $params) {
        $fubBarcode = $this->$attribute;
        if(!$this->validation->isFabBarcode($fubBarcode)) {
            $this->addError($attribute, '<b>['.$fubBarcode.']</b> '.Yii::t('inbound/errors','"Это не штрихкод Фаб номара Miele"'));
        }
    }
    //
    public function isCleanOurBox($attribute, $params)
    {
        $ourBoxBarcode = $this->our_box_barcode;
        if(!$this->validation->isBoxOnlyOur($ourBoxBarcode)) {
            $this->addError($attribute, '<b>['.$ourBoxBarcode.']</b> '.Yii::t('inbound/errors','"Это не штрихкод нашего короба"'));
        }

        $inboundId = $this->order_id;
        if(!$this->validation->isBoxExist($inboundId,$ourBoxBarcode)) {
            $this->addError($attribute, '<b>['.$ourBoxBarcode.']</b> '.Yii::t('inbound/errors','"Этот штрихкод короба не найден в заказе"'));
        }
    }

    //
    public function getDTO() {
//        VarDumper::dump($this->validation->getRepository(),10,true);
        $obj = new \stdClass();
        $obj->inbound = $this->validation->getRepository()->getOrderInfo($this->order_id);
        $obj->primary_address = $this->our_box_barcode;
        $obj->product_barcode = $this->product_barcode;
        $obj->fab_barcode = $this->fab_barcode;

        return $obj;
    }
    //
    public function attributeLabels()
    {
        return [
            'client_id' => Yii::t('inbound/forms', 'Клиент'),
            'order_id' => Yii::t('inbound/forms', 'Номер заказа'),
            'our_box_barcode' => Yii::t('inbound/forms', 'Наш короб шк'),
            'product_barcode' => Yii::t('inbound/forms', 'Шк тавара клиента'),
            'fab_barcode' => Yii::t('inbound/forms', 'Фаб шк'),
        ];
    }
}