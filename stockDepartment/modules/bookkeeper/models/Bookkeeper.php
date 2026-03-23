<?php

namespace stockDepartment\modules\bookkeeper\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * bookkeeper represents the model behind the search form about `stockDepartment\modules\Bookkeeper\models\bookkeeper`.
 */
class Bookkeeper extends \common\modules\bookkeeper\models\Bookkeeper
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sort_order','client_id','cash_type','expenses_type_id','type_id','id', 'tl_delivery_proposal_id', 'tl_delivery_proposal_route_unforeseen_expenses_id', 'department_id', 'doc_type_id', 'status', 'date_at', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['doc_file', 'name_supplier', 'description'], 'safe'],
            [['price', 'balance_sum'], 'number'],
        ];
    }

    /*
    * Get grid row color, depend on
    * record status
    * @return string
    **/
    public static function getGridColorByValue($status){

        switch($status) {
            case self::STATUS_NEW: //#FFA54F
                $class = 'color-tan';
                break;
            case self::STATUS_DONE: //#FFA500
                $class = 'color-orange';
                break;
            case self::STATUS_MONEY_RECEIVED: //#FFF68F
                $class = 'color-khaki';
                break;
            case self::STATUS_MONEY_GIVEN: //#CAFF70
                $class = 'color-dark-olive-green';
                break;
//            case Stock::STATUS_OUTBOUND_SCANNING: //#87CEFA
//                $class = 'color-light-sky-blue';
//                break;
//            case Stock::STATUS_OUTBOUND_SCANNED: //#1E90FF
//                $class = 'color-dodger-blue';
//                break;
//            case Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST: //#FFFFE0
//                $class = 'color-light-yellow';
//                break;
//            case Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API: //#EE82EE
//                $class = 'color-violet ';
//                break;
//            case Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL: //#FF6A6A
//            case Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL: //#FF6A6A
//                $class = 'color-indian-red';
//                break;
//            case Stock::STATUS_OUTBOUND_COMPLETE: //#C6E2FF
//                $class = 'color-slate-gray';
//                break;
            default:
                $class = '';
                break;
        }
        return $class;
    }
}