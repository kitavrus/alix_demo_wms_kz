<?php

namespace common\modules\stock\models;

use Yii;
use yii\data\ActiveDataProvider;

class RackAddressSearch extends RackAddress
{
    public $address;

    public function rules()
    {
        return [
            [['address'], 'required'],
            [['address'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'address' => Yii::t('stock/forms', 'Address'),
        ];
    }

    public function search($params)
    {
        $query = RackAddress::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['address'=>SORT_ASC]],
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'address', $this->address]);

        return $dataProvider;
    }
} 