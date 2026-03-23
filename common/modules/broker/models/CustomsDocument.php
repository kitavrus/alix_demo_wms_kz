<?php
namespace common\modules\broker\models;

use Yii;
use common\models\ActiveRecord;

/**
 * This is the model class for table "customs_documents".
 *
 * @property integer $id
 * @property integer $customs_account_id
 * @property string $customs_account_cost_id
 * @property integer $version
 * @property string $filename
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class CustomsDocument extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customs_documents';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customs_account_id', 'version', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['customs_account_cost_id'], 'string', 'max' => 255],
            //[['filename'], 'string', 'max' => 256]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('forms', 'ID'),
            'customs_account_id' => Yii::t('customs/forms', 'Invoice number'),
            'customs_account_cost_id' => Yii::t('customs/forms', 'Cost number'),
            'version' => Yii::t('customs/forms', 'File version'),
            'filename' => Yii::t('customs/forms', 'File name'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Updated User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
            'deleted' => Yii::t('forms', 'Deleted'),
        ];
    }
}