<?php
namespace app\modules\operatorDella\models;

use Yii;
use yii\helpers\ArrayHelper;
use app\modules\transportLogistics\transportLogistics;
use yii\helpers\Html;
use common\modules\transportLogistics\components\TLHelper;

/**
 * This is the model class for table "tl_delivery_proposals".
 *
 * @property integer $id
 * @property integer $delivery_method
 * @property integer $client_id
 * @property integer $external_client_lead_id
 * @property integer $transportation_order_lead_id
 * @property integer $source
 * @property integer $is_client_confirmed
 * @property integer $ready_to_invoicing
 * @property integer $route_from
 * @property integer $route_to
 * @property string  $sender_contact
 * @property integer  $sender_contact_id
 * @property string  $recipient_contact
 * @property integer  $recipient_contact_id
 * @property integer $company_transporter //  Помпиния перевозчикћ
 * @property integer $change_price
 * @property integer $change_mckgnp
 * @property integer $delivery_type //  Example: transfer
 * @property integer $car_id
 * @property integer $agent_id
 * @property string  $driver_name
 * @property string  $driver_phone
 * @property string  $driver_auto_number
 * @property integer $delivery_date // фактическая дата доставки в магазин
 * @property integer $expected_delivery_date // Предположительная дата доставки в магазин
 * @property integer $shipped_datetime // Фактическая дата отгрузки со склада, устанавливается когда напечатали ТТН
 * @property integer $accepted_datetime // Фактическая дата получения товара на склад. Должна устанавливаться работниками склада
 * @property string $mc
 * @property integer $mc_actual
 * @property integer $kg
 * @property integer $kg_actual
 * @property integer $volumetric_weight
 * @property integer $number_places
 * @property integer $number_places_actual
 * @property integer $cash_no
 * @property integer $price_invoice
 * @property string $price_invoice_with_vat
 * @property integer $status
 * @property integer $status_invoice
 * @property string $comment
 * @property string $extra_fields
 * @property string $bl_data
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $seal
 */
class TlDeliveryProposal extends \common\modules\transportLogistics\models\TlDeliveryProposal
{
    private $_sourceConditionEditDelete = [self::SOURCE_DELLA_OPERATOR,self::SOURCE_OUR_OPERATOR];
    private $_sourceConditionPrice = [self::SOURCE_DELLA_OPERATOR];
    /*
     * Show price
     * @param bool $format
     * @return string;
     * */
    public function showPriceForOperator($format = true)
    {
        $price = 0;
        if(in_array($this->source,$this->_sourceConditionPrice)) {
            $price = $this->price_invoice;
        }

        return ($format ? Yii::$app->formatter->asCurrency($price) : $price);
    }

    /*
     * Show price
     * @return bool
     * */
    public function showPriceForFormOperator()
    {
        return in_array($this->source,$this->_sourceConditionPrice);
    }

    /*
     * Show edit button
     * @return string Html edit button;
     * */
    public function showEditButtonForOperator()
    {
        $out = '';
        if(in_array($this->source,$this->_sourceConditionEditDelete)) {
           $out =  Html::a(Yii::t('client/buttons', 'Edit'), ['edit-order', 'id' => $this->id], ['class' => 'btn btn-warning']);
        }
        return $out;
    }

    /*
     * Show delete button
     * @return string Html edit button;
     * */
    public function showDeleteButtonForOperator()
    {
        $out = '';
        if(in_array($this->source, $this->_sourceConditionEditDelete)) {
           $out = Html::a(Yii::t('client/buttons', 'Delete'), ['delete', 'id' => $this->id], ['class' => 'btn btn-danger','data' => [
               'confirm' => Yii::t('titles', 'Are you sure you want to delete this item?'),
               'method' => 'post',
           ]]);
        }
        return $out;
    }
}