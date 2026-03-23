<?php

namespace stockDepartment\modules\wms\models\defacto;


use Yii;

/**
 * This is the model class for table "outbound_boxes".
 *
 * @property int $id
 * @property string $our_box Our box
 * @property string $client_box Client box
 * @property string $client_extra_json Client extra json data
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class OutboundBoxes extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'outbound_boxes';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['client_extra_json'], 'string'],
			[['created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
			[['our_box'], 'string', 'max' => 13],
			[['client_box'], 'string', 'max' => 16],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'our_box' => 'Our Box',
			'client_box' => 'Client Box',
			'client_extra_json' => 'Client Extra Json',
			'created_user_id' => 'Created User ID',
			'updated_user_id' => 'Updated User ID',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'deleted' => 'Deleted',
		];
	}
}