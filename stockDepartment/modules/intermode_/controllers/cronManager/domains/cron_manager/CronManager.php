<?php

namespace stockDepartment\modules\intermode\controllers\cronManager\domains\cron_manager;

use common\models\ActiveRecord;
use Yii;

/**
 * This is the model class for table "cron_manager".
 *
 * @property int $id
 * @property string $name Название задачи
 * @property int $order_id Id закрываемой накладной
 * @property string $status Статус
 * @property string $type b2c-in,b2b-in,b2b-re
 * @property string $result_message
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class CronManager extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cron_manager';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['name', 'status', 'type'], 'string', 'max' => 128],
            [['result_message'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'order_id' => 'Order ID',
            'status' => 'Status',
            'type' => 'Type',
            'result_message' => 'Result message',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}
