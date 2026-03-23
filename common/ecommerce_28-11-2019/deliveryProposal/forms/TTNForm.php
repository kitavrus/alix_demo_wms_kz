<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 10:01
 */

namespace common\ecommerce\deliveryProposal\forms;

use common\modules\transportLogistics\models\TlDeliveryProposal;
use Yii;
use yii\base\Model;

class TTNForm extends Model
{
    public $ourTTN;
    public $clientTTN;

    //
    public function rules()
    {
        return [
            // Select order
            [['ourTTN'], 'required', 'on' => 'onTTN'],
            [['ourTTN'], 'integer', 'on' => 'onTTN'],
            [['ourTTN'], 'trim', 'on' => 'onTTN'],
            [['ourTTN'], 'validateOurTTN'],
            // Select order
            [['clientTTN'], 'required', 'on' => 'onTTN'],
            [['clientTTN'], 'string', 'on' => 'onTTN'],
            [['clientTTN'], 'trim', 'on' => 'onTTN'],
        ];
    }
    //
    public function validateOurTTN($attribute,$params)
    {
        $ourTTN = $this->ourTTN;
        if(!TlDeliveryProposal::find()->andWhere([
            'id'=>$ourTTN
        ])->exists()) {
            $this->addError($attribute, 'ТТНка не найдена');
        }
    }

    //
    public function getDTO() {
        $dto = new \stdClass();
        $dto->ourTTN = $this->ourTTN;
        $dto->clientTTN = $this->clientTTN;
        return $dto;
    }

    public function saveClientTTN() {
        $dp = TlDeliveryProposal::findOne($this->ourTTN);
        if($dp) {
            $dp->client_ttn = $this->clientTTN;
            $dp->save(false);
        }

    }


    public function attributeLabels()
    {
        return [
            'ourTTN' => Yii::t('inbound/forms', 'Наша ТТНка'),
            'clientTTN' => Yii::t('inbound/forms', 'Клиента ТТНка'),
        ];
    }
}