<?php
namespace common\modules\product\models\defacto;

use Yii;
use common\models\ActiveRecord;
/**
 * This is the model class for table "colors".
 *
 * @property integer $id
 * @property string $cod
 * @property string $title
 */
class Colors extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'colors';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('dbDefactoSpecial');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cod'], 'string', 'max' => 16],
            [['title'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'cod' => Yii::t('app', 'color cod: ZP6'),
            'title' => Yii::t('app', 'color title: GREEN'),
        ];
    }


    /**
     * @param $cod string ER113
     * @param $title string GOLD
     * @return Colors
     */
    public static function create($cod,$title)
    {
        $color = new Colors();
        $color->cod = $cod;
        $color->title = $title;
        $color->save(false);
        return $color;
    }
}