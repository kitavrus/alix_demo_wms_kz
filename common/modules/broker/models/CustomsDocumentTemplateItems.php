<?php

namespace common\modules\broker\models;

use Yii;
use yii\web\UploadedFile;
use yii\helpers\BaseFileHelper;
use common\helpers\iHelper;

/**
 * This is the model class for table "customs_document_template_items".
 *
 * @property integer $id
 * @property integer $customs_document_template_id
 * @property string $title
 * @property string $description
 * @property string $file
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class CustomsDocumentTemplateItems extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customs_document_template_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customs_document_template_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['file'],'file'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('customs/forms', 'ID'),
            'customs_document_template_id' => Yii::t('customs/forms', 'Customs Document ID'),
            'title' => Yii::t('customs/forms', 'Title'),
            'description' => Yii::t('customs/forms', 'Description'),
            'file' => Yii::t('customs/forms', 'File'),
            'created_user_id' => Yii::t('customs/forms', 'Created User ID'),
            'updated_user_id' => Yii::t('customs/forms', 'Updated User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
            'deleted' => Yii::t('customs/forms', 'Deleted'),
        ];
    }

    public function saveFile()
    {
        if($this->file) {

            $dirPath = 'uploads/attached-files/customs-documents-templates/' . $this->customs_document_template_id . '/' . date('Ymd') . '/' . date('H-i');
            BaseFileHelper::createDirectory($dirPath);
            $fileToPath = $dirPath . '/' . iHelper::transliterate($this->file->name);
            $this->file->saveAs($fileToPath);
            if (file_exists($fileToPath)) {
                $this->file = $fileToPath;

                return true;
            }
        }


        return false;
    }
}
