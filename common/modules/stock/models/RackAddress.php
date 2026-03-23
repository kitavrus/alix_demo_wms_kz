<?php

namespace common\modules\stock\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "stock".
 *
 * @property integer $id
 * @property string $address
 * @property integer $sort_order
 * @property integer $client_id
 * @property integer $zone_id
 * @property integer $warehouse_id
 * @property string $address_unit1
 * @property string $address_unit2
 * @property string $address_unit3
 * @property string $address_unit4
 * @property string $address_unit5
 * @property string $address_unit6
 * @property integer $is_printed
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class RackAddress extends \common\models\ActiveRecord
{
    const STAGE_MAX = 6; // Этаж
    const STAGE_MIN = 1;

    const ROW_MIN = 1; // Ряд
	const ROW_MAX = 500;

    const RACK_MIN = 1; // Полка
    const RACK_MAX = 500;

    const LEVEL_MIN = 0; // Уровень на полке
    const LEVEL_MAX = 9;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rack_address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sort_order', 'is_printed', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['client_id','zone_id','warehouse_id'], 'integer'],
            [['address_unit1','address_unit2','address_unit3','address_unit4','address_unit5','address_unit6'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('stock/forms', 'ID'),
            'address' => Yii::t('stock/forms', 'Address'),
            'sort_order' => Yii::t('stock/forms', 'Sort order'),
            'is_printed' => Yii::t('stock/forms', 'Is printed'),
            'created_user_id' => Yii::t('stock/forms', 'Created User ID'),
            'updated_user_id' => Yii::t('stock/forms', 'Updated User ID'),
            'created_at' => Yii::t('stock/forms', 'Created At'),
            'updated_at' => Yii::t('stock/forms', 'Updated At'),
            'deleted' => Yii::t('stock/forms', 'Deleted'),
        ];
    }

    public static function checkForExist($address,$warehouseID = null)
    {
        return RackAddress::find()
            ->andWhere(['address'=>$address])
            ->andFilterWhere(['warehouse_id'=>$warehouseID])
            ->exists();

//        if($exist = RackAddress::findOne(['address'=>$address])){
//            return true;
//        }
//
//        return false;
    }

    public static function getStageValuesArray()
    {
        $data=[];
        for ($i=self::STAGE_MIN; $i <= self::STAGE_MAX; $i++){
            $data[$i]=$i;
        }

        return $data;
    }

    public static function getLevelValuesArray()
    {
        $data=[];
        for ($i=self::LEVEL_MIN; $i <= self::LEVEL_MAX; $i++){
            $data[]=$i;
        }

        return $data;
    }

    public static function createAddress($stage, $row, $rack, $level = null,$warehouseID = 0, $zoneID = 0)
    {
        $rack  = $rack < 10 && $rack > 0 ? '0'.$rack : $rack;
        //$level = $level < 10 && $level > 0 ? '0'.$level : $level;

        $address = $stage.'-'.$row.'-'.$rack;
        if(!is_null($level)) {
            $address = $stage.'-'.$row.'-'.$rack.'-'.$level;
        }

        if(!self::checkForExist($address,$warehouseID)){
            $ra = new RackAddress();
            $ra->warehouse_id = $warehouseID;
            $ra->zone_id = $zoneID;
            $ra->address = $address;
            $ra->address_unit1 = $stage;
            $ra->address_unit2 = $row;
            $ra->address_unit3 = trim($rack,0);
            $ra->address_unit4 = $level;
            $ra->sort_order = (int)RackAddress::find()->max('sort_order') + 1;
            if($ra->save(false)) {
                return $ra->address;
            }
        }

        return false;
    }

    /**
     * Return rack number from address
     */
    public function getRackValue($address=null)
    {
        if(is_null($address)){
            $address = $this->address;
        }
        $address = explode('-', $address);
        return isset($address[2]) ? $address[2] : false;
    }

    /**
     * Return rack number from address
     */
    public function getRowValue($address=null)
    {
        if(is_null($address)){
            $address = $this->address;
        }
        $address = explode('-', $address);
        return isset($address[1]) ? $address[1] : false;
    }


    /*
    * Скрипт генерирует адреса для полок из
    * заданых диапазонов
    **/
    public function actionGenerateRackAddress()
    {
        echo 'start other/generate-rack-address end' . "\n";

        $lvlMin = RackAddress::STAGE_MIN; //Этаж минимальное значение
        $lvlMax = RackAddress::STAGE_MAX; //Этаж максимальное значение

        $rowMin = RackAddress::ROW_MIN; //Ряд минимальное значение
        $rowMax = RackAddress::ROW_MAX; //Ряд максимальное значение

        $rackInRowMin = RackAddress::RACK_MIN; //Полка в ряду минимальное значение
        $rackInRowMax = RackAddress::RACK_MAX; //Полка в ряду максимальное значение

        $upperMin = RackAddress::LEVEL_MIN; //Полка в ряду минимальное значение
        $upperMax = RackAddress::LEVEL_MAX; //Полка в ряду максимальное значение

        for ($i1 = $lvlMin; $i1 <= $lvlMax; $i1++) {
            if ($a = RackAddress::createAddress($i1, $rowMin, $rackInRowMin, $upperMin)) {
                echo 'address ' . $a . ' was generated' . "\n";
            } else {
                echo 'address was not created' . "\n";
            }

            for ($i2 = $rowMin; $i2 <= $rowMax; $i2++) {
                if ($a = RackAddress::createAddress($i1, $i2, $rackInRowMin, $upperMin)) {
                    echo 'address ' . $a . ' was generated' . "\n";
                } else {
                    echo 'address was not created' . "\n";
                }

                for ($i3 = $rackInRowMin; $i3 <= $rackInRowMax; $i3++) {

                    if ($a = RackAddress::createAddress($i1, $i2, $i3, $upperMin)) {
                        echo 'address ' . $a . ' was generated' . "\n";
                    } else {
                        echo 'address was not created' . "\n";
                    }
                    for ($i4 = $upperMin; $i4 <= $upperMax; $i4++) {
                        if ($a = RackAddress::createAddress($i1, $i2, $i3, $i4)) {
                            echo 'address ' . $a . ' was generated' . "\n";
                        } else {
                            echo 'address was not created' . "\n";
                        }
                    }
                }
            }
        }

        $i = 0;
        foreach(Stock::find()->each(100) as $stock) {
            if($address = RackAddress::find()->andWhere(['address'=>$stock->secondary_address])->one()) {
                $stock->address_sort_order = $address->sort_order;
                $stock->save(false);
                echo $i++." ".$stock->secondary_address.' '.$address->sort_order."\n";
            }
        }

        echo ' end other/generate-rack-address end' . "\n";
        return 0;
    }

     /*
    * Скрипт генерирует адреса для полок из
    * заданых диапазонов
    **/
    public function actionGenerateRackAddressWarehouse2()
    {
        echo 'start other/generate-rack-address-warehouse2 begin' . "\n";

        $lvlMin = 1; //Этаж минимальное значение
        $lvlMax = 1; //Этаж максимальное значение

        $rowMin = RackAddress::ROW_MIN; //Ряд минимальное значение
        $rowMax = RackAddress::ROW_MAX; //Ряд максимальное значение

        $rackInRowMin = RackAddress::RACK_MIN; //Полка в ряду минимальное значение
        $rackInRowMax = RackAddress::RACK_MAX; //Полка в ряду максимальное значение

        $upperMin = RackAddress::LEVEL_MIN; //Полка в ряду минимальное значение
        $upperMax = RackAddress::LEVEL_MAX; //Полка в ряду максимальное значение

        $warehouseID = 2; // Номер склада

        for ($i1 = $lvlMin; $i1 <= $lvlMax; $i1++) {
            if ($a = RackAddress::createAddress($i1, $rowMin, $rackInRowMin, $upperMin, $warehouseID)) {
                echo 'address ' . $a . ' was generated' . "\n";
            } else {
                echo 'address was not created' . "\n";
            }

            for ($i2 = $rowMin; $i2 <= $rowMax; $i2++) {
                if ($a = RackAddress::createAddress($i1, $i2, $rackInRowMin, $upperMin, $warehouseID)) {
                    echo 'address ' . $a . ' was generated' . "\n";
                } else {
                    echo 'address was not created' . "\n";
                }

                for ($i3 = $rackInRowMin; $i3 <= $rackInRowMax; $i3++) {

                    if ($a = RackAddress::createAddress($i1, $i2, $i3, $upperMin, $warehouseID)) {
                        echo 'address ' . $a . ' was generated' . "\n";
                    } else {
                        echo 'address was not created' . "\n";
                    }
                    for ($i4 = $upperMin; $i4 <= $upperMax; $i4++) {
                        if ($a = RackAddress::createAddress($i1, $i2, $i3, $i4, $warehouseID)) {
                            echo 'address ' . $a . ' was generated' . "\n";
                        } else {
                            echo 'address was not created' . "\n";
                        }
                    }
                }
            }
        }

//        $i = 0;
//        foreach(Stock::find()->each(100) as $stock) {
//            if($address = RackAddress::find()->andWhere(['address'=>$stock->secondary_address])->one()) {
//                $stock->address_sort_order = $address->sort_order;
//                $stock->save(false);
//                echo $i++." ".$stock->secondary_address.' '.$address->sort_order."\n";
//            }
//        }

        echo ' end other/generate-rack-address end' . "\n";
        return 0;
    }




}
