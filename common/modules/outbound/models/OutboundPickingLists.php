<?php

namespace common\modules\outbound\models;

use common\modules\client\models\Client;
use common\modules\outbound\models\OutboundOrder;
use common\modules\employees\models\Employees;
use common\modules\stock\models\Stock;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "outbound_picking_lists".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $outbound_order_id
 * @property integer $employee_id
 * @property string $barcode
 * @property string $employee_barcode
 * @property integer $status
 * @property integer $page_number
 * @property integer $page_total
 * @property integer $begin_datetime
 * @property integer $end_datetime
 * @property string  $kpi_value
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class OutboundPickingLists extends \common\models\ActiveRecord
{

    /*
    * @var integer status
    *
    * */
    const STATUS_NOT_SET = 0; // не указан
    const STATUS_PRINT = 1; // Напечатали лист сборки
    const STATUS_BEGIN = 2; // Начали сборку
    const STATUS_END = 3; // Закончили
    const STATUS_PRINT_BOX_LABEL = 4; // Напечатали этикетки на короба

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'outbound_picking_lists';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['barcode'], 'required'],
            [['client_id','page_total','page_number','outbound_order_id','status','employee_id', 'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['barcode', 'employee_barcode'], 'string', 'max' => 32],
            [['kpi_value'], 'string', 'max' => 512]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('outbound/forms', 'ID'),
            'client_id' => Yii::t('outbound/forms', 'Client ID'),
            'outbound_order_id' => Yii::t('outbound/forms', 'Outbound order ID'),
            'employee_id' => Yii::t('outbound/forms', 'Employee ID'),
            'barcode' => Yii::t('outbound/forms', 'Barcode'),
            'employee_barcode' => Yii::t('outbound/forms', 'Employee Barcode'),
            'status' => Yii::t('outbound/forms', 'Status'),
            'page_number' => Yii::t('outbound/forms', 'Page number'),
            'page_total' => Yii::t('outbound/forms', 'Page total'),
            'begin_datetime' => Yii::t('outbound/forms', 'Begin Datetime'),
            'end_datetime' => Yii::t('outbound/forms', 'End Datetime'),
            'kpi_value' => Yii::t('outbound/forms', 'KPI Time'),
            'created_user_id' => Yii::t('outbound/forms', 'Created User ID'),
            'updated_user_id' => Yii::t('outbound/forms', 'Updated User ID'),
            'created_at' => Yii::t('outbound/forms', 'Created At'),
            'updated_at' => Yii::t('outbound/forms', 'Updated At'),
            'deleted' => Yii::t('outbound/forms', 'Deleted'),
        ];
    }

    /**
     * @return array Массив с статусами.
     */
    public function getStatusArray()
    {
        return [
            self::STATUS_NOT_SET => Yii::t('outbound/titles', 'Not set'),
            self::STATUS_PRINT => Yii::t('outbound/titles', 'Print pick list'),
            self::STATUS_BEGIN => Yii::t('outbound/titles', 'Assembly begin'),
            self::STATUS_END => Yii::t('outbound/titles', 'Assembly end'),
            self::STATUS_PRINT_BOX_LABEL => Yii::t('outbound/titles', 'Box label printed'),
        ];
    }
    /*
   * Relation has one with Client
   * */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }
    /*
   * Relation has one with Employee
   * */
    public function getEmployee()
    {
        return $this->hasOne(Employees::className(), ['id' => 'employee_id']);
    }

    /*
  * Relation has one with Employee
  * */
    public function getOutboundOrder()
    {
        return $this->hasOne(OutboundOrder::className(), ['id' => 'outbound_order_id']);
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

    /*
     * Get products in picking list IDs
     * @param array $plIDs Picking list ids
     * @return array
     * */
    public static function getStockByPickingIDs($plIDs)
    {
//        $subQueryStatusPicked = Stock::find()->select('');

//        (select count(*) FROM stock as stck WHERE stck.status= "'.Stock::STATUS_OUTBOUND_PICKED.'"  AND stck.product_barcode = stock.product_barcode) as count_status_picked,
//                     (select count(*) FROM stock as stck WHERE stck.status= "'.Stock::STATUS_OUTBOUND_SORTING.'"  AND stck.product_barcode = stock.product_barcode) as count_status_sorting ,
//                     (select count(*) FROM stock as stck WHERE stck.status= "'.Stock::STATUS_OUTBOUND_SORTED.'"  AND stck.product_barcode = stock.product_barcode) as count_status_sorted,
//                     (select count(*) FROM stock as stck WHERE stck.product_barcode = stock.product_barcode AND stck.outbound_picking_list_id = stock.outbound_picking_list_id) as count_exp
//
        return Stock::find()
            ->select('id, product_barcode, box_barcode, status, primary_address, secondary_address, product_model, count(*) as items, inbound_client_box')
            ->where([
                'outbound_picking_list_id' => $plIDs,
                'status' => [
                    Stock::STATUS_OUTBOUND_PICKED,
                    Stock::STATUS_OUTBOUND_SCANNED,
                    Stock::STATUS_OUTBOUND_SCANNING,
                    Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                    Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
                ],
            ])
            ->groupBy('product_barcode, box_barcode')
            ->orderBy([
                'box_barcode'=>SORT_ASC,
                'product_barcode'=>SORT_ASC,
            ])
//            ->orderBy([
//                'secondary_address'=>SORT_DESC,
//                'primary_address'=>SORT_DESC,
//            ])
            ->asArray()
            ->all();
    }

    /*
     * Get count product in box and picking list
     * @param string $boxBarcode
     * @param string $pickingListIDs
     * @return integer Count in bpx
     * */
    public static function getCountInBoxByPickingList($boxBarcode,$pickingListIDs)
    {
        return Stock::find()->where([
            'box_barcode'=>$boxBarcode,
            'status'=>[Stock::STATUS_OUTBOUND_SCANNED,Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL],
            'outbound_picking_list_id'=>$pickingListIDs,
        ])->count();
    }

    /*
    * Get count product in box and picking list
    * @param string $boxBarcode
    * @return integer Count in bpx
    * */
    public static function getCountInBoxByBoxBarcode_NOT_USED($boxBarcode)
    {
        return Stock::find()->where([
            'box_barcode'=>$boxBarcode,
            'status'=>[Stock::STATUS_OUTBOUND_SCANNED,Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL]
        ])->count();
    }

    /*
    * Get count product in box by outbound order
    * @param string $boxBarcode
     * @param string $pickingListIDs
    * @return integer Count in bpx
    * */
    public static function getCountInBoxByOutboundOrder($boxBarcode,$pickingListIDs)
    {
        $outboundOrderIDs = OutboundPickingLists::find()->select('outbound_order_id')->andWhere(['id'=>$pickingListIDs])->groupBy('outbound_order_id')->asArray()->column();
        return Stock::find()->andWhere([
            'box_barcode'=>$boxBarcode,
            'status'=>[Stock::STATUS_OUTBOUND_SCANNED,Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL],
            'outbound_order_id'=>$outboundOrderIDs,
        ])->count();
    }
	
	/*
* Get count product in box by outbound order
* @param string $boxBarcode
 * @param string $pickingListIDs
* @return integer Count in bpx
* */
	public static function getCountInBoxByOutboundOrderId($boxBarcode,$outboundOrderIDs)
	{
//		$outboundOrderIDs = OutboundPickingLists::find()->select('outbound_order_id')->andWhere(['id'=>$pickingListIDs])->groupBy('outbound_order_id')->asArray()->column();
		return Stock::find()->andWhere([
			'box_barcode'=>$boxBarcode,
			'status'=>[Stock::STATUS_OUTBOUND_SCANNED,Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL],
			'outbound_order_id'=>$outboundOrderIDs,
		])->count();
	}
	
    /*
    * Get products in picking list IDs
    * @param string $pickingListIDs
    * @return array
    * */
    public static function getAccExpByPickingListInOrder($pickingListIDs)
    {
        $outboundOrderIDs = OutboundPickingLists::find()->select('outbound_order_id')->andWhere(['id'=>$pickingListIDs])->groupBy('outbound_order_id')->asArray()->column();

        return OutboundOrder::find()->select('expected_qty, accepted_qty, allocated_qty')->andWhere([
                 'id'=>$outboundOrderIDs,
        ])->asArray()->one();
    }

	/*
	* Get products in picking list IDs
	* @param string $pickingListIDs
	* @return array
	* */
	public static function getAccExpByPickingListInOrderId($outboundOrderIDs)
	{
//		$outboundOrderIDs = OutboundPickingLists::find()->select('outbound_order_id')->andWhere(['id'=>$pickingListIDs])->groupBy('outbound_order_id')->asArray()->column();

		return OutboundOrder::find()->select('expected_qty, accepted_qty, allocated_qty')->andWhere([
			'id'=>$outboundOrderIDs,
		])->asArray()->one();
	}

    /*
     * Prepare ids
     * @param array $plIDs Picking list ids
     * @param boolean $type Default true, return string
     * @return string | array
     *
     * */
    public static function prepareIDsHelper($plIDs,$type = false)
    {
        if (!empty($plIDs)) {
            $plIDs = trim($plIDs, ',');
            $tmp = explode(',', $plIDs);
            $plIDs = array_unique($tmp);
            if($type) {
                $plIDs = implode(',', $plIDs);
            }
        }

        return $plIDs;
    }
    /*
     *
     * */
    public function showEmployeeName()
    {
        return \yii\helpers\ArrayHelper::getValue($this->employee,function($e){
            return $e != null ? $e->first_name .' '.$e->last_name.' | '.$e->barcode : '';
        });
    }
    /*
     *
     * */
    public function showCountLot()
    {
        return Stock::find()->andWhere(['outbound_picking_list_id'=>$this->id])->count();
    }

    /*
     *
     * */
    public function showDiffRealBeginEndDateTime()
    {
        if(!empty($this->end_datetime) && !empty($this->begin_datetime)) {
            return $this->end_datetime - $this->begin_datetime;
        }
        return 0;
    }
    /*
     *
     * */
    public function showDiffKPIBeginEndDateTime()
    {
        if(!empty($this->end_datetime) && !empty($this->begin_datetime)&& !empty($this->kpi_value)) {
            return  $this->showDiffRealBeginEndDateTime()-$this->kpi_value;
        }
        return 0;
    }
    /*
     *
     * */
    public function showPercentDiffKPIBeginEndDateTime()
    {
        if(!empty($this->end_datetime) && !empty($this->begin_datetime)&& !empty($this->kpi_value)) {
            $t =  $this->showDiffKPIBeginEndDateTime();
            $kpiValueP = $this->kpi_value / 100;
            if(!empty($t) && !empty($kpiValueP)) {
                return  $t / $kpiValueP;
            }
        }
        return 0;
    }

    public static function getPickingListIDsByPickingListBarcode($pickingListBarcode) {

        $pickingListIDs = OutboundPickingLists::find()
            ->select('outbound_order_id')
            ->andWhere(['barcode'=>$pickingListBarcode])
            ->scalar();

        return OutboundPickingLists::find()
            ->select('id')
            ->andWhere(['outbound_order_id'=>$pickingListIDs])
//            ->andWhere(['barcode'=>$pickingListBarcode])
            ->column();
    }
	
	public static function getOutboundOrderIdByPickingLists($pickingListIDs) {
		return  OutboundPickingLists::find()
									->select('outbound_order_id')
									->andWhere(['id'=>$pickingListIDs])
									->limit(1)
									->scalar();
	}

	/**
	* Get count product in box by outbound order
	* @param string $boxBarcode
	* @param string $pickingListIDs
	* @return integer Count in bpx
	* */
	public static function getCountInBoxByOutboundOrderByOrderId($boxBarcode,$outboundOrderID)
	{
		return Stock::find()->andWhere([
			'box_barcode'=>$boxBarcode,
			'status'=>[
				Stock::STATUS_OUTBOUND_SCANNED,
				Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
				Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL
			],
			'outbound_order_id'=>$outboundOrderID,
		])->count();
	}
}