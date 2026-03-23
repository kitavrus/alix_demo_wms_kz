<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace stockDepartment\modules\wms\models\defacto;

use common\modules\crossDock\models\CrossDock;

//use common\modules\employees\models\Employees;
//use common\modules\outbound\models\OutboundPickingLists;
use common\modules\stock\models\Stock;
use yii\base\Model;
use Yii;

class ApplyExpAccQtyCrossDockForm extends Model
{

    public $expected_number_places_qty;
    public $accepted_number_places_qty;
    public $box_m3;

    /*
    * */
    public function rules()
    {
        return [
            [['expected_number_places_qty', 'accepted_number_places_qty', 'box_m3'], 'required'],
            [['expected_number_places_qty', 'accepted_number_places_qty'], 'integer'],
            [['box_m3'], 'number'],
            [['expected_number_places_qty', 'accepted_number_places_qty', 'box_m3'], 'trim'],
        ];
    }

    /*
     * */
    public function attributeLabels()
    {
        return [
            'expected_number_places_qty' => Yii::t('outbound/forms', 'Предполагаемое кол-во'),
            'accepted_number_places_qty' => Yii::t('outbound/forms', 'Действительное кол-во'),
            'box_m3' => Yii::t('outbound/forms', 'M3'),
        ];
    }
}