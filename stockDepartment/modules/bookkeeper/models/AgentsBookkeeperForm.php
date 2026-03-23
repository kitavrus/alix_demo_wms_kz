<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace stockDepartment\modules\bookkeeper\models;

use yii\base\Model;
use Yii;

class AgentsBookkeeperForm extends Model {

    public $agent_id;
    public $month_from;
    public $month_to;
    public $status;
    public $invoice;

    /*
     *
     * */
    public function attributeLabels()
    {
        return [
            'agent_id' => Yii::t('AgentsBookkeeper/forms', 'Agent id'),
            'month_from' => Yii::t('AgentsBookkeeper/forms', 'Month from'),
            'month_to' => Yii::t('AgentsBookkeeper/forms', 'Month to'),
            'status' => Yii::t('AgentsBookkeeper/forms', 'Status'),
        ];
    }

    /*
     *
     * */
    public function rules()
    {
        return [
            [['agent_id'], 'required'],
            [['agent_id','status'], 'integer'],
            [['month_from','month_to'], 'string'],
            [['invoice'], 'string'],
        ];
    }

    /*
    *
    * */
    public function findStatus()
    {
        if($m = TlAgentsBookkeeper::find()->select('status')->andWhere(['agent_id'=>$this->agent_id,'month_from'=>$this->month_from,'month_to'=>$this->month_to])->one()) {
            $status = $m->status;
        } else {
            $status = 1;
        }
        return $status;
    }

    /*
    *
    * */
    public static function selectColorByStatus($status)
    {
        $color = '';
        switch($status) {
            case '1': $color = '#ff8080'; //
                break;
            case '2': $color = '#f0ad4e';
                break;
            case '3': $color = '#5cb85c';
                break;
            default:
                $color = '#ff8080';
        }

        return $color;
    }
}