<?php

namespace common\modules\audit\models;

use common\modules\bookkeeper\models\Bookkeeper;
use Yii;
use common\modules\audit\interfaces\AuditInterface;

/**
 * This is the model class for table "bookkeeper_audit".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $date_created
 * @property integer $created_by
 * @property string $field_name
 * @property string $before_value_text
 * @property string $after_value_text
 */
class BookkeeperAudit extends Audit implements AuditInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bookkeeper_audit';
    }

    /**
     * @inheritdoc
     */
    public function getAuditObjectClass()
    {
        return Bookkeeper::className();
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
//    public static function getDb()
//    {
//        return Yii::$app->get('dbAudit');
//    }

    /**
     * @inheritdoc
     */
//    public function rules()
//    {
//        return [
//            [['parent_id', 'created_by'], 'integer'],
//            [['date_created'], 'safe'],
//            [['field_name', 'before_value_text', 'after_value_text'], 'string', 'max' => 255]
//        ];
//    }

    /**
     * @inheritdoc
     */
//    public function attributeLabels()
//    {
//        return [
//            'id' => Yii::t('app', 'ID'),
//            'parent_id' => Yii::t('app', 'Parent ID'),
//            'date_created' => Yii::t('app', 'Date Created'),
//            'created_by' => Yii::t('app', 'Created By'),
//            'field_name' => Yii::t('app', 'Field Name'),
//            'before_value_text' => Yii::t('app', 'Before Value Text'),
//            'after_value_text' => Yii::t('app', 'After Value Text'),
//        ];
//    }
}