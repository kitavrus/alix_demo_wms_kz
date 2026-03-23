<?php

namespace stockDepartment\modules\wms\managers\erenRetail\inventory;

//use common\components\BarcodeManager;
//use common\modules\stock\models\RackAddress;
//use stockDepartment\modules\wms\managers\erenRetail\inventory\InventoryRows;
use common\modules\client\models\Client;
use Yii;
use yii\helpers\ArrayHelper;
//use yii\helpers\VarDumper;
use common\modules\stock\models\Stock;

/**
 * This is the model class for table "inventory".
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
class Inventory extends \common\models\ActiveRecord
{
    const STATUS_NEW = 1; // Новый
    const STATUS_IN_PROCESS = 2; // Сканируем ряды
    const STATUS_DONE = 3; // Закончили

    const STATUS_SCAN_NO = 0; // еще не участвуют в сканировании
    const STATUS_SCAN_PROCESS = 1; // ряд обрабытывается
    const STATUS_SCAN_YES = 2; // Отсканирован



    const INVENTORY_BARCODE = '0-inventory-0';
    const INVENTORY_FILE_NAME_ERROR = 'inventory20181206.csv';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'inventory';
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
        return $this->hasMany(InventoryRows::className(), ['inventory_id'=>'id']);
    }


    /*
     * Get count product in box
     * @param string $boxBarcode primary address
     *
     * */
    public static function getCountProductInBox($boxBarcode,$row,$inventory_id)
    {
        return Stock::find()
					->andWhere([
						'status_inventory'=>self::STATUS_SCAN_YES,
						'primary_address'=>$boxBarcode,
						'inventory_id'=>$inventory_id
//						'secondary_address'=>$minMax,
					])
					->andWhere('secondary_address LIKE "'.$row.'%" ')
					->count();
    }

    /*
    * Get count product in row
    * @param array $minMax secondary address
    * @param integer $inventory_id
    * */
    public static function getCountProductInRow($row,$inventory_id)
    {
		//file_put_contents("getCountProductInRow.log",print_r($minMax,true)."\n",FILE_APPEND);
			
        return Stock::find()
					->andWhere([
						'status_inventory'=>self::STATUS_SCAN_YES,
						'inventory_id'=>$inventory_id
//						'secondary_address'=>$minMax,
					])
					->andWhere('secondary_address LIKE "'.$row.'%" ')
					->count();
	/*	file_put_contents("getCountProductInRow.log",print_r($minMax,true)."\n",FILE_APPEND);


		$start = $minMax[0];
		$start = substr($start,0,4);
		//die($start);

		return Stock::find()
					->andWhere(['status_inventory'=>self::STATUS_SCAN_YES,'secondary_address'=>$minMax,'inventory_id'=>$inventory_id])
					->andWhere('secondary_address LIKE "'.$start.'%" ')
					->count(); */
					
    }

    /*
    * Check start inventory
    * */
    public static function checkStart($column_number,$floor_number,$level_number = null,$inventory_id)
    {
        return InventoryRows::find()->andWhere([
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
        $floorNumber = Inventory::getFloorNumber($row);
        $columnNumber = Inventory::getRowNumber($row);
       if($ir = InventoryRows::find()->andWhere(['column_number'=>$columnNumber,'floor_number'=>$floorNumber,'inventory_id'=>$inventory_id])->one()) {
           $ir->accepted_qty = $qty;
           $ir->save(false);
       }

       return true;
    }

    /*
     * Get min max secondary_address
     * @param string $secondary_address
     * @return array
     * */
    public static function getMinMaxSecondaryAddress($secondary_address)
    {
        $sa = explode('-',trim($secondary_address));
		if(is_array($sa)) {
			$c = count($sa);
			if ($c == 4) { // mezanin address
				$stage = preg_replace('/[^0-9]/', '',$sa['0']); // этаж
				$row = preg_replace('/[^0-9]/', '',$sa['1']); // ряд
				return $stage."-".$row."-";
			} else if ($c == 3) {
				$row = preg_replace('/[^0-9]/', '',$sa['0']); // ряд
				return $row."-";
			}
		}
		return "999999-";
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

//        VarDumper::dump($sa,10,true);
//        VarDumper::dump(isset($sa['1']),10,true);
//        die;
        if(is_array($sa) && isset($sa['1'])) {
            // 2-10-02-1  // этаж-ряд-полка-уровень
  			$c = count($sa);
			if ($c == 4) {
				$subject = $sa['1'];
				$row = preg_replace('/[^0-9]/', '', $subject); // ряд
			} else {
				$subject = $sa['0'];
				$row = preg_replace('/[^0-9]/', '', $subject); // ряд
			}
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
			$c = count($sa);
			if ($c == 4) {
				$subject = $sa['0'];
				$stage = preg_replace('/[^0-9]/', '',$subject); // этаж
			} else {
				$stage = "0"; // этаж
			}
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
}
