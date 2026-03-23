<?php

namespace common\modules\codebook\models;

use Yii;

/**
 * This is the model class for table "box_size".
 *
 * @property integer $id
 * @property string $box_height
 * @property string $box_width
 * @property string $box_length
 * @property string $box_code
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class BoxSize extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'box_size';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['box_height', 'box_width', 'box_length', 'box_code'], 'string', 'max' => 4]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('forms', 'ID'),
            'box_height' => Yii::t('forms', 'Box Height'),
            'box_width' => Yii::t('forms', 'Box Width'),
            'box_length' => Yii::t('forms', 'Box Length'),
            'box_code' => Yii::t('forms', 'Box Code'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Updated User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
        ];
    }
}
