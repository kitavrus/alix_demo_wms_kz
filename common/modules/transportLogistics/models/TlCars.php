<?php

namespace common\modules\transportLogistics\models;

use yii\helpers\ArrayHelper;
use common\models\ActiveRecord;
use Yii;
use app\modules\transportLogistics\transportLogistics;

/**
 * This is the model class for table "tl_cars".
 *
 * @property integer $id
 * @property integer $agent_id
 * @property string $title
 * @property string $name
 * @property string $description
 * @property integer $status
 * @property string $mc
 * @property string $kg
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property string $created_at
 * @property string $updated_at
 */
class TlCars extends ActiveRecord
{


    /*
    * @var integer status
    * */
    const STATUS_ACTIVE = 0;
    const STATUS_NOT_ACTIVE = 1;
    const STATUS_DELETED = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_cars';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['agent_id', 'status', 'created_user_id', 'updated_user_id'], 'integer'],
            [['description'], 'string'],
            [['mc', 'kg'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'name'], 'string', 'max' => 128]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('transportLogistics/forms', 'ID'),
            'agent_id' => Yii::t('transportLogistics/forms', 'Agent ID'),
            'title' => Yii::t('transportLogistics/forms', 'Car title'),
            'name' => Yii::t('transportLogistics/forms', 'Car name'),
            'description' => Yii::t('transportLogistics/forms', 'Description'),
            'status' => Yii::t('transportLogistics/forms', 'Status'),
            'mc' => Yii::t('transportLogistics/forms', 'Cargo capacity (м³)'),
            'kg' => Yii::t('transportLogistics/forms', 'Weight limit (kg)'),
            'created_user_id' => Yii::t('transportLogistics/forms', 'Created User ID'),
            'updated_user_id' => Yii::t('transportLogistics/forms', 'Updated User ID'),
            'created_at' => Yii::t('transportLogistics/forms', 'Created At'),
            'updated_at' => Yii::t('transportLogistics/forms', 'Updated At'),
        ];
    }

    /*
    * @return array with store id=>title
    */
//    public static function getCarArray($key = null)
    public static function getCarArray()
    {
        return ArrayHelper::map(self::find()->orderBy('title')->all(), 'id', 'title');
//        return isset($data[$key]) ? $data[$key] : $data;
    }

   /*
    * @return array with store id=>title
    */
    public static function getCarValue($key = null)
    {
        $data =  ArrayHelper::map(self::find()->orderBy('title')->all(), 'id', 'title');

        return isset($data[$key]) ? $data[$key] : '-';
    }

    /**
     * @return array Массив с статусами.
     */
    public static function getStatusArray($key=null)
    {
        $data = [
            self::STATUS_ACTIVE => Yii::t('transportLogistics/forms', 'Active'),
            self::STATUS_NOT_ACTIVE => Yii::t('transportLogistics/forms', 'Not active'),
            self::STATUS_DELETED => Yii::t('transportLogistics/forms', 'Deleted'),
        ];
        return isset($data[$key]) ? $data[$key] : $data;
    }


    /*
     *
     *
     * */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $agentName = '';
            if($agent = TlAgents::findOne($this->agent_id)) {
                $agentName = $agent->name;
            }

            $this->title = $this->name . ' '.$this->kg.' '.Yii::t('titles','kg').' '.$agentName;

            return true;
        } else {
            return false;
        }
    }

    /*
    * Relation has one with Agent table
    * */
    public function getAgent()
    {
        return $this->hasOne(TlAgents::className(), ['id' => 'agent_id']);
    }

    /*
     * Get formatted title
     * @return string
     * */
    public function getDisplayTitle()
    {
        $kg = ' / '.Yii::$app->formatter->asDecimal($this->kg).' '.Yii::t('titles','kg');
        $mc = ' / '.Yii::$app->formatter->asDecimal($this->mc).' '.Yii::t('titles','m3');

        return $this->agent->name.' / '.$this->name.(!empty($this->kg) ? $kg : '').' '.(!empty($this->mc) ? $mc : '');
    }

    /*
     * Get cars array  by Agent
     * @param integer $agentId
     * @return array cars ['id'=>'title']
     * */
    public static function getCarsByAgent($agentId)
    {
        return ArrayHelper::map(TlCars::find()->where(['agent_id'=>$agentId])->orderBy('title')->all(),'id','title');
    }

}
