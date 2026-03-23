<?php
namespace stockDepartment\modules\alix\controllers\outboundSeparator\entities;

use Yii;

/**
 * This is the model class for table "outbound_separator".
 *
 * @property int $id
 * @property string $order_number Order number
 * @property string $comments Comments
 * @property string $status new,scanned,done
 * @property string $path_to_file Путь к файлу
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class OutboundSeparator extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'outbound_separator';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['path_to_file'], 'string'],
            [['created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['order_number', 'status'], 'string', 'max' => 256],
            [['comments'], 'string', 'max' => 1024],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_number' => 'Order Number',
            'comments' => 'Comments',
            'status' => 'Status',
            'path_to_file' => 'Path To File',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}
