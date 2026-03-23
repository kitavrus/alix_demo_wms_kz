<?php

namespace stockDepartment\modules\store\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\store\models\Store;

/**
 * StoreSearch represents the model behind the search form about `common\modules\store\models\Store`.
 */
class StoreSearch extends Store
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','type_use', 'address_type', 'status', 'intercom', 'floor', 'elevator', 'created_at', 'updated_at'], 'integer'],
            [['city_prefix'], 'string'],
            [['shopping_center_name','client_id','city_id', 'name', 'contact_first_name', 'contact_middle_name', 'contact_last_name', 'contact_first_name2', 'contact_middle_name2', 'contact_last_name2', 'email', 'phone', 'phone_mobile', 'title', 'description', 'zip_code', 'street', 'house', 'entrance', 'flat', 'comment', 'shop_code', 'shop_code2'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Store::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'client_id' => $this->client_id,
            'id' => $this->id,
            'city_id' => $this->city_id,
            'type_use' => $this->type_use,
            'status' => $this->status,
            'intercom' => $this->intercom,
            'floor' => $this->floor,
            'elevator' => $this->elevator,
            'city_prefix' => $this->city_prefix,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'contact_first_name', $this->contact_first_name])
            ->andFilterWhere(['like', 'contact_middle_name', $this->contact_middle_name])
            ->andFilterWhere(['like', 'contact_last_name', $this->contact_last_name])
            ->andFilterWhere(['like', 'contact_first_name2', $this->contact_first_name2])
            ->andFilterWhere(['like', 'contact_middle_name2', $this->contact_middle_name2])
            ->andFilterWhere(['like', 'contact_last_name2', $this->contact_last_name2])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'phone_mobile', $this->phone_mobile])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
//            ->andFilterWhere(['like', 'country', $this->country])
//            ->andFilterWhere(['like', 'region', $this->region])
//            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'zip_code', $this->zip_code])
            ->andFilterWhere(['like', 'street', $this->street])
            ->andFilterWhere(['like', 'house', $this->house])
            ->andFilterWhere(['like', 'entrance', $this->entrance])
            ->andFilterWhere(['like', 'flat', $this->flat])
            ->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'shopping_center_name', $this->shopping_center_name])
            ->andFilterWhere(['like', 'shop_code', $this->shop_code])
            ->andFilterWhere(['like', 'shop_code2', $this->shop_code2]);

        return $dataProvider;
    }
}
