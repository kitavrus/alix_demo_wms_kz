<?php

namespace common\ecommerce\entities;

use common\components\BarcodeManager;
use common\ecommerce\constants\InboundStatus;
use common\ecommerce\constants\StockAvailability;
use common\ecommerce\entities\EcommerceInventoryRows;
use common\modules\client\models\Client;
use common\modules\stock\models\RackAddress;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "ecommerce_inventory".
 *
 * @property integer $id
 * @property integer $client_id
 * @property string $order_number
 * @property integer $expected_qty
 * @property integer $accepted_qty
 * @property integer $expected_places_qty
 * @property integer $accepted_places_qty
 * @property integer $status
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class EcommerceInventory extends \common\models\ActiveRecord
{
    const STATUS_NEW = 1; // Новый
    const STATUS_IN_PROCESS = 2; // Сканируем ряды
    const STATUS_DONE = 3; // Закончили

    const STATUS_SCAN_NO = 0; // еще не участвуют в сканировании
    const STATUS_SCAN_PROCESS = 1; // ряд обрабытывается
    const STATUS_SCAN_YES = 2; // Отсканирован



    const INVENTORY_BARCODE = '0-inventory-0';
    const INVENTORY_FILE_NAME_ERROR = 'inventory20201212.csv';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_inventory';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'expected_qty', 'accepted_qty', 'expected_places_qty', 'accepted_places_qty', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['order_number'], 'string', 'max' => 54]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'client_id' => Yii::t('inventory/forms', 'Client'),
            'order_number' => Yii::t('inventory/forms', 'Order Number'),
            'expected_qty' => Yii::t('inventory/forms', 'Expected Qty'),
            'accepted_qty' => Yii::t('inventory/forms', 'Accepted Qty'),
            'expected_places_qty' => Yii::t('inventory/forms', 'Ожидаемое кол-во коробов'),
            'accepted_places_qty' => Yii::t('inventory/forms', 'Итоговое кол-во коробов'),
            'status' => Yii::t('inventory/forms', 'Status'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Updated User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
        ];
    }

    /**
     * @return array Массив с статусами.
     */
    public static function getStatusScanArray()
    {
        return [
            self::STATUS_SCAN_NO => Yii::t('stock/titles', 'Не отсканирован'),
            self::STATUS_SCAN_PROCESS => Yii::t('stock/titles', 'В процессе'),
            self::STATUS_SCAN_YES => Yii::t('stock/titles', 'Отсканирован'),
        ];
    }

    /**
     * @return string Читабельный статус поста.
     */
    public static function getStatusScanValue($status = null)
    {
        return ArrayHelper::getValue(self::getStatusScanArray(), $status);
    }

    /**
     * @return array Массив с статусами.
     */
    public function getStatusArray()
    {
        return [
            self::STATUS_NEW => Yii::t('stock/titles', 'Новый'),
            self::STATUS_IN_PROCESS => Yii::t('stock/titles', 'В процессе'),
            self::STATUS_DONE => Yii::t('stock/titles', 'Завершено'),
        ];
    }

    /**
     * @return string Читабельный статус поста.
     */
    public function getStatusValue($status = null)
    {
        if(is_null($status)){
            $status = $this->status;
        }
        return ArrayHelper::getValue($this->getStatusArray(), $status);
    }

    /**
     * @return array Массив с статусами.
     */
    public static function getActiveInventory()
    {
        return ArrayHelper::map(self::find()->select('id,order_number, client_id')->andWhere(['status'=>[
                self::STATUS_NEW,
                self::STATUS_IN_PROCESS,
            ]
        ])->orderBy(['id'=>SORT_DESC])->all(),'id',function($data) {

            $clientTitle = '';
            if($rClient =  $data->client) {
                $clientTitle = $rClient->title;
            }

            return "№ ".$data->order_number.' - '.$clientTitle;
//            return "№ ".$data->order_number.' - '.$data->client->title;
        });
    }

    /*
    * Relation has one with Client
    **/
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    /*
    * Relation has one with Rows
    **/
    public function getRows()
    {
        return $this->hasMany(EcommerceInventoryRows::className(), ['inventory_id'=>'id']);
    }


    /*
     * Get count product in box
     * @param string $boxBarcode primary address
     *
     * */
    public static function getCountProductInBox($boxBarcode,$minMax,$inventory_id)
    {
        return EcommerceStock::find()->where(['status_inventory'=>self::STATUS_SCAN_YES,'place_address_barcode'=>$minMax,'box_address_barcode'=>$boxBarcode,'inventory_id'=>$inventory_id])->count();
    }

    /*
    * Get count product in row
    * @param array $minMax secondary address
    * @param integer $inventory_id
    * */
    public static function getCountProductInRow($minMax,$inventory_id)
    {
        return EcommerceStock::find()->where(['status_inventory'=>self::STATUS_SCAN_YES,'place_address_barcode'=>$minMax,'inventory_id'=>$inventory_id])->count();
    }

    /*
    * Check start inventory
    * */
    public static function checkStart($column_number,$floor_number,$level_number = null,$inventory_id)
    {
        return EcommerceInventoryRows::find()->andWhere([
            'floor_number'=>$floor_number,
            'column_number'=>$column_number,
//            'level_number'=>$level_number,
            'inventory_id'=>$inventory_id
        ])->exists();
    }

//    /*
//    * Get row
//    * return active record Inventory Row
//    * */
//    public static function getRowModel($column_number,$inventory_id)
//    {
//        return InventoryRows::find()->where(['column_number'=>$column_number,'inventory_id'=>$inventory_id])->one();
//    }

    /*
     *
     * */
    public static function saveAcceptedQtyByRow($qty,$row,$inventory_id)
    {
        $floorNumber = EcommerceInventory::getFloorNumber($row);
        $columnNumber = EcommerceInventory::getRowNumber($row);
       if($ir = EcommerceInventoryRows::find()->andWhere(['column_number'=>$columnNumber,'floor_number'=>$floorNumber,'inventory_id'=>$inventory_id])->one()) {
           $ir->accepted_qty = $qty;
           $ir->save(false);
       }

       return true;
    }

    /*
     * Get min max place_address_barcode
     * @param string $secondary_address
     * @return array
     * */
    public static function getMinMaxSecondaryAddress($secondary_address)
    {
//        if(RackAddress::checkForExist($secondary_address,2)) {
//            return [$secondary_address];
//        }

        $sa = explode('-',trim($secondary_address));
        $minMax = [];
        if($sa) {
            $rackInRowMin = RackAddress::RACK_MIN; //Полка в ряду минимальное значение
            $rackInRowMax = RackAddress::RACK_MAX*5; //Полка в ряду максимальное значение
            $upperMin = RackAddress::LEVEL_MIN; //Полка в ряду минимальное значение
//            $upperMax = RackAddress::LEVEL_MAX - 6; //Полка в ряду максимальное значение
            $upperMax = RackAddress::LEVEL_MAX - 5; //Полка в ряду максимальное значение
            // 2-10-02-1  // этаж-ряд-полка-уровень
            $stage = preg_replace('/[^0-9]/', '',$sa['0']); // этаж
            $row = preg_replace('/[^0-9]/', '',$sa['1']); // ряд
            $rack = preg_replace('/[^0-9]/', '',$sa['2']); // полка
            $level = preg_replace('/[^0-9]/', '',$sa['3']); // уровень
            // 1-6-06-1
            for ($i3 = $rackInRowMin; $i3 <= $rackInRowMax; $i3++) {
                for ($i4 = $upperMin; $i4 <= $upperMax; $i4++) {
                    $rack = $i3 < 10 && $i3 > 0 ? '0' . $i3 : $i3;
                    $minMax[] = $stage . '-' . $row . '-' . $rack . '-' . $i4;
                }
            }
            $fullRow = 0;
            $row++;
            if ($fullRow) {
                for ($i3 = $rackInRowMin; $i3 <= $rackInRowMax; $i3++) {
                    for ($i4 = $upperMin; $i4 <= $upperMax; $i4++) {
                        $rack = $i3 < 10 && $i3 > 0 ? '0' . $i3 : $i3;
                        $minMax[] = $stage . '-' . $row . '-' . $rack . '-' . $i4;
                    }
                }
            }
        }

        return $minMax;
    }

    /*
     * Get row
     * @param string $secondary_address
     * @return integer row number
     * */
    public static function getRowNumber($secondary_address)
    {
        $sa = explode('-',trim($secondary_address));

        $row = -1;

        if(is_array($sa) && isset($sa['1'])) {
            // 2-10-02-1  // этаж-ряд-полка-уровень
            $subject = $sa['1'];
            $row = preg_replace('/[^0-9]/','',$subject); // ряд
        }

//        die($row);

        return $row;
    }

    /*
     * Get floor
     * @param string $secondary_address
     * @return integer floor number
     * */
    public static function getFloorNumber($secondary_address)
    {
        $sa = explode('-',trim($secondary_address));
        $stage = -1;
        if(is_array($sa) && isset($sa['0'])) {
            // 2-10-02-1  // этаж-ряд-полка-уровень
            $subject = $sa['0'];
            $stage = preg_replace('/[^0-9]/', '',$subject); // этаж
        }

        return $stage;
    }

    /*
     * Get Level
     * @param string $secondary_address
     * @return integer Level number
     * */
    public static function getLevelNumber($secondary_address)
    {
        $sa = explode('-',trim($secondary_address));
        $stage = -1;
        if(is_array($sa) && isset($sa['2'])) {
            // 2-10-02-1  // этаж-ряд-полка-уровень
            $subject = $sa['2'];
            $stage = preg_replace('/[^0-9]/', '',$subject); // этаж
        }

        return $stage;
    }


    //---------------------------------------------

    public function getNotEmptyBoxIdsQuery($boxBarcode)
    {
        return EcommerceStock::find()->select('id')
            ->andWhere(['inventory_box_address_barcode'=>$boxBarcode,'status_availability'=>[StockAvailability::YES]]);
//            ->andWhere(['box_address_barcode'=>$boxBarcode,'status_availability'=>[StockAvailability::YES,StockAvailability::NO]])
//            ->orWhere(['box_address_barcode'=>$boxBarcode,'status_availability'=>[StockAvailability::NO],'status_inbound'=>InboundStatus::SCANNED]);
    }

    public function getNotEmptyBoxIds($boxBarcode)
    {
        return EcommerceStock::find()->select('id')
            ->andWhere(['inventory_box_address_barcode'=>$boxBarcode,'status_availability'=>[StockAvailability::YES]])
//            ->andWhere(['box_address_barcode'=>$boxBarcode,'status_availability'=>[StockAvailability::YES,StockAvailability::NO]])
//            ->orWhere(['box_address_barcode'=>$boxBarcode,'status_availability'=>[StockAvailability::NO],'status_inbound'=>InboundStatus::SCANNED])
            ->column();
    }

    public function isBoxEmpty($boxBarcode)
    {
        return EcommerceStock::find()
            ->andWhere(['id'=>$this->getNotEmptyBoxIds($boxBarcode)])
            ->exists();
    }
}
