<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 11.02.2016
 * Time: 10:22
 */

namespace app\modules\operatorDella\models;

use common\modules\billing\models\TlDeliveryProposalBilling;
use yii\base\Model;
use Yii;

class QuickDeliveryOrderForm extends Model {

//    public $first_name;
//    public $sender;
//    public $senderContact;
//    public $clientId;
//    public $recipient;
//    public $recipientContact;
//    public $m3;
//    public $kg;
//    public $places;
//    public $declaredValue;
//    public $description;
//    public $deliveryType; // Тип достаки склад-дверь, дверь-дверь
//    public $typeLoading; // Тип погрузки. сверху, сзади, сбоку
//    public $whoPays; // Кто платит. Получатель, отправитель, по контракту
//    public $price;

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'sender' => Yii::t('operator/forms', 'SENDER'),
            'senderContact' => Yii::t('operator/forms', 'SENDER_CONTACT'),
            'recipient' => Yii::t('operator/forms', 'RECIPIENT'),
            'recipientContact' => Yii::t('operator/forms', 'RECIPIENT_CONTACT'),
            'declaredValue' => Yii::t('operator/forms', 'DECLARED_VALUE'),
            'description' => Yii::t('operator/forms', 'DESCRIPTION'),
            'deliveryType' => Yii::t('operator/forms', 'DELIVERY_TYPE'),
            'typeLoading' => Yii::t('operator/forms', 'TYPE_LOADING'),
            'whoPays' => Yii::t('operator/forms', 'WHO_PAYS'),
        ];
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            ['sender', 'required'],
            ['sender', 'integer','min'=>1,'max'=>999,'tooSmall'=>'Не должно быть пустым'],

            ['senderContact', 'required'],
            ['senderContact', 'integer'],

            ['recipient', 'required'],
            ['recipient', 'integer','min'=>1,'max'=>999,'tooSmall'=>'Не должно быть пустым'],

            ['recipientContact', 'required'],
            ['recipientContact', 'integer'],

            ['clientId', 'required'],
            ['clientId', 'integer'],

            ['deliveryType', 'required'],
            ['deliveryType', 'integer','min'=>1,'max'=>999,'tooSmall'=>'Не должно быть пустым'],

            ['typeLoading', 'required'],
            ['typeLoading', 'integer','min'=>1,'max'=>999,'tooSmall'=>'Не должно быть пустым'],

//            ['whoPays', 'required' ,'isEmpty'=>function($v){ return empty($v);}],
            //['whoPays', 'required' ,'skipOnEmpty'=>false],
            ['whoPays', 'required'],
            ['whoPays', 'integer','min'=>1,'max'=>999,'tooSmall'=>'Не должно быть пустым'],

            [['description'],'string'],
            [['m3','kg','price','declaredValue'],'number']
        ];
    }

    /**
     * @return array Массив с типами доставки
     */
    public function getDeliveryTypeArray()
    {
        return TlDeliveryProposalBilling::getDeliveryTypeArray();
    }

    /**
     * @return array Массив с типами доставки
     */
    public function getTransportTypeLoadingArray()
    {
        return TlDeliveryProposal::getTransportTypeLoadingArray();
    }

    /**
     * @return array Кто оплачивает
     */
    public function getTransportWhoPaysArray()
    {
        return TlDeliveryProposal::getTransportWhoPaysArray();
    }
}