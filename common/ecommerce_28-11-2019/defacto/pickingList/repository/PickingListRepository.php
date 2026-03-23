<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 03.08.2019
 * Time: 13:19
 */

namespace common\ecommerce\defacto\pickingList\repository;


use common\ecommerce\entities\EcommerceEmployee;
use common\ecommerce\entities\EcommercePickingList;

class PickingListRepository
{
    const STATUS_NOT_SET = 0; // не указан
    const STATUS_PRINT = 1; // Напечатали лист сборки
    const STATUS_BEGIN = 2; // Начали сборку
    const STATUS_END = 3; // Закончили
    const STATUS_PRINT_BOX_LABEL = 4; // Напечатали этикетки на короба

    /*
    * Is picking list
    * @param string $barcode
    * @param integer $status
    * @return boolean
     * */
    public static function isPickingList($barcode,$status = null)
    {
        $barcode = trim($barcode);

        $q = EcommercePickingList::find();
        $q->where(['barcode'=>$barcode]);

        $q->andFilterWhere([
            'status' => $status,
        ]);

        return $q->exists();
    }

    public static function getPickListByBarcode($pickListBarcode,$clientId)
    {
        return EcommercePickingList::find()->andWhere([
            'client_id' => $clientId,
            'barcode' => $pickListBarcode,
        ])->one();
    }

    public static function makePrintBoxPickingList($outboundId)
    {
        EcommercePickingList::updateAll(['status'=>self::STATUS_PRINT_BOX_LABEL],['outbound_id'=>$outboundId]);
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
}