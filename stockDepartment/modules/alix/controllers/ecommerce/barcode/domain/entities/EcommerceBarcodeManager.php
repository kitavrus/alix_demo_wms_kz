<?php

namespace stockDepartment\modules\intermode\controllers\ecommerce\barcode\domain\entities;

/**
 * This is the model class for table "ecommerce_barcode_manager".
 *
 * @property int $id
 * @property string $barcode_prefix Barcode prefix
 * @property string $title Title
 * @property int $counter Counter
 * @property int $status Status
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommerceBarcodeManager extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_barcode_manager';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['counter', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['barcode_prefix'], 'string', 'max' => 5],
            [['title'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'barcode_prefix' => 'Barcode Prefix',
            'title' => 'Title',
            'counter' => 'Counter',
            'status' => 'Status',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}
