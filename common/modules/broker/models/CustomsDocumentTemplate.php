<?php

namespace common\modules\broker\models;
use common\modules\broker\models\CustomsDocumentTemplateItems;

use Yii;

/**
 * This is the model class for table "customs_document_template".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class CustomsDocumentTemplate extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customs_document_template';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['description'], 'string'],
            [['created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['title'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('customs/forms', 'ID'),
            'title' => Yii::t('customs/forms', 'Title'),
            'description' => Yii::t('customs/forms', 'Description'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Updated User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
            'deleted' => Yii::t('forms', 'Deleted'),
        ];
    }

    /*
    * Relation has many with CustomsDocuments
    **/
    public function getTemplateItems()
    {
        return $this->hasMany(CustomsDocumentTemplateItems::className(), ['customs_document_template_id' => 'id']);
    }
}
