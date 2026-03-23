<?php
/**
 * Created by PhpStorm.
 * User: Kitavrus
 * Date: 15.03.2018
 * Time: 10:01
 */

namespace app\modules\freeenter\forms;

use common\clientObject\constants\Constants;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use Yii;
use yii\base\Model;
use yii\web\NotFoundHttpException;

class SaveTTNForm extends Model
{
    public $ourTTN;
    public $clientTTN;

    //
    public function rules()
    {
        return [
            // Enter Our TTN
            [['ourTTN'], 'required', 'on' => 'onTTN'],
            [['ourTTN'], 'integer', 'on' => 'onTTN'],
            [['ourTTN'], 'trim', 'on' => 'onTTN'],
            [['ourTTN'], 'validateOurTTN'],
            // Enter Client TTN
            [['clientTTN'], 'required', 'on' => 'onTTN'],
            [['clientTTN'], 'string', 'on' => 'onTTN'],
            [['clientTTN'], 'trim', 'on' => 'onTTN'],
        ];
    }

    public function checkKey($key) {

        if(!array_key_exists($key,$this->getMapClientToPassword())) {
             throw new NotFoundHttpException('У вас нет доступа');
        }
    }

    //
    public function validateOurTTN($attribute,$params)
    {
        $ourTTN = $this->ourTTN;

        $dp = $this->findDeliveryProposal($ourTTN);

        if(!$dp) {
            $this->addError($attribute, 'ТТНка не найдена');
        } elseif(!empty($dp->client_ttn)) {
            $this->addError($attribute, 'Вы уже задали номер ТТН для этой ТТНки');
        }
    }

    public function saveClientTTN() {
        $dp = $this->findDeliveryProposal($this->ourTTN);
        if($dp) {
            $dp->client_ttn = $this->clientTTN;
            $dp->save(false);
        }
    }

    private function findDeliveryProposal($id) {
        return TlDeliveryProposal::findOne([
            'id'=>$id,
            'client_id'=>Constants::getCarPartClientIDs()
        ]);
    }

    public function getMapClientToPassword() {
        return [
            '41qaz2ett3'=>"ETT",
        ];
    }


    public function attributeLabels()
    {
        return [
            'ourTTN' => Yii::t('inbound/forms', 'Введите номер ТТН "NOMADEX"'),
            'clientTTN' => Yii::t('inbound/forms', 'Введите номер ТТН "ETT"'),
        ];
    }
}