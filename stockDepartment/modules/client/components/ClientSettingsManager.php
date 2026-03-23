<?php

namespace app\modules\client\components;

use Yii;
use yii\base\Component;
use common\modules\client\models\ClientSettings;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\models\ActiveRecord;
use yii\helpers\VarDumper;

class ClientSettingsManager extends Component
{
    //option type
    const OPTION_TYPE_FUNCTION = 1;
    const OPTION_TYPE_DROPDOWN = 2;

    /*
   * @var settings array
   */
    protected $data = [];

    /**
     * @return array Массив с формами оплаты.
     */
    public static function getPaymentMethodArray($key=null)
    {
        $data = ActiveRecord::getPaymentMethodArray();
        return isset($data[$key]) ? $data[$key] : $data;
    }

    /**
     * Get list no change price
     * @return array .
     */
    public static function getNoChangePriceArray($key=null)
    {
        $data = TlDeliveryProposal::getNoChangePriceArray();
        return isset($data[$key]) ? $data[$key] : $data;
    }



//FOR TEST
//    public static function getNoChangeMcKgNpArray($key=null)
//    {
//        $data = TlDeliveryProposal::getNoChangeMcKgNpArray();
//        return isset($data[$key]) ? $data[$key] : $data;
//    }

    /*
     * Return params value by key
     */
    public function getParams(){

        return $this->data;
    }

    /*
    * Find all settings for specified client
    */
    public function __construct($client_id)
    {
        $this->data = ClientSettings::findAll(['client_id' => $client_id]);
    }

}