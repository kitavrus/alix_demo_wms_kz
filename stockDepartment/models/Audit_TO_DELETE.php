<?php

namespace stockDepartment\models;

use Yii;
use dektrium\user\models\User;
/**
 * This is the model class for table "tl_delivery_proposals_audit".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property integer $date_created
 * @property integer $created_by
 * @property string $field_name
 * @property string $before_value_text
 * @property string $after_value_text
 */
class Audit extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            [['parent_id', 'date_created', 'created_by', 'field_name', 'before_value_text', 'after_value_text'], 'required'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('forms', 'ID'),
            'parent_id' => Yii::t('forms', 'Parent ID'),
            'date_created' => Yii::t('forms', 'Date Created'),
            'created_by' => Yii::t('forms', 'Created By'),
            'field_name' => Yii::t('forms', 'Field Name'),
            'before_value_text' => Yii::t('forms', 'Before Value Text'),
            'after_value_text' => Yii::t('forms', 'After Value Text'),
        ];
    }

    /*
    * Relation has one with user
    * */
    public function getCreatedUser()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }
}
