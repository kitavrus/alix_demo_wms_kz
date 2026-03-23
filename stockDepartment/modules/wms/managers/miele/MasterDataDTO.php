<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 03.07.2017
 * Time: 8:00
 */

namespace stockDepartment\modules\wms\managers\miele;

use common\modules\client\models\Client;

class MasterDataDTO
{
    public function parseRequestUpdateMATMAS($data)
    {
        $result = [];
        if(isset($data->list->Запись)) {
            foreach($data->list->Запись as $item) {
                $result[] = $this->parseItem($item);
            }
        }

        return $result;
    }

    public function parseItem($item) {
        return [
            'field_extra1' => $item->МатНомер,
            'sku' => $item->МатНомер,
            'model' => $item->Артикул,
            'name' => $item->Наименование,
            'weight_brutto' => $item->ВесБрутто, // -
            'weight_netto' => $item->ВесНетто, // -
            'm3' => $item->Объем, // -
            'length' => $item->Длина, // -
            'width' => $item->Ширина, // -
            'height' => $item->Высота, // -
            'barcode' => $item->EAN11, // -
            'field_extra2' => $item->УровеньШтабелирования,
            'field_extra3' => $item->УчетПоФабричнымНомерам,
            'field_extra4' => $item->УчетПоКоммерческимНомерам,
            'field_extra5' => $item->УчетПоСрокамГодности,
            'field_extra6' => $item->ТребуетсяЭтикетирование,
            'client_id' =>  Client::CLIENT_MIELE,
            'created_user_id' =>  Client::CLIENT_MIELE,
            'updated_user_id' =>  Client::CLIENT_MIELE,
        ];
    }

    public function makeResponseUpdateMATMAS() {
        $std = [
            'UpdateMATMASResponse' => true
        ];
        return $std;
    }
}