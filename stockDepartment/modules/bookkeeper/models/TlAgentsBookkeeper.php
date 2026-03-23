<?php

namespace stockDepartment\modules\bookkeeper\models;

use Yii;

/**
 * This is the model class for table "tl_agents_bookkeeper".
 *
 * @property integer $id
 * @property integer $agent_id
 * @property string $name
 * @property string $description
 * @property string $invoice
 * @property string $month_from
 * @property string $month_to
 * @property integer $status
 * @property integer $date_of_invoice
 * @property integer $payment_date_invoice
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class TlAgentsBookkeeper extends \common\modules\bookkeeper\models\TlAgentsBookkeeper
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['agent_id', 'status'], 'integer'],
            [['invoice'], 'number'],
            [['name'], 'string', 'max' => 128],
            [['description'], 'string', 'max' => 228],
            [['month_from', 'month_to'], 'string', 'max' => 64]
        ];
    }
}