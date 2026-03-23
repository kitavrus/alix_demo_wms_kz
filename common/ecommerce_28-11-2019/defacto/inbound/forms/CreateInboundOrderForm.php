<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 12:23
 */

namespace common\ecommerce\defacto\inbound\forms;

use common\clientObject\hyundaiAuto\inbound\repository\InboundRepository;
use Yii;
use yii\base\Model;
use yii\helpers\BaseFileHelper;
use yii\web\UploadedFile;

class CreateInboundOrderForm extends Model
{
    public $orderNumber;
    public $qtyBox;
    //
    public function __construct($config = []) {
        parent::__construct($config);
//        $this->validation = new \common\clientObject\hyundaiAuto\inbound\validation\InboundOrderUploadValidation();
//        $this->inboundRepository = new InboundRepository();
//        $this->clientID =  $this->inboundRepository->getClientID();
    }


    public function attributeLabels()
    {
        return [
            'orderNumber' => Yii::t('inbound/forms', 'Номер накладной'),
            'qtyBox' => Yii::t('inbound/forms', 'Кол-во коробок'),
        ];
    }

    public function rules()
    {
        return [
            [['orderNumber'], 'required','on'=>'onCreate'],
            [['orderNumber'], 'string','on'=>'onCreate'],
            [['orderNumber'], 'trim','on'=>'onCreate'],
//            [['orderNumber'], 'validateIsOrderExist','on'=>'onCreate'],

            [['qtyBox'], 'required','on'=>'onCreate'],
            [['qtyBox'], 'integer','on'=>'onCreate'],
            [['qtyBox'], 'trim','on'=>'onCreate'],
//            [['qtyBox'], 'validateIsOrderExist','on'=>'onCreate'],
        ];
    }

    public function getDTO() {
        $dto = new \stdClass();
        $dto->orderNumber = $this->orderNumber;
        $dto->qtyBox = $this->qtyBox;
        return $dto;
    }
}