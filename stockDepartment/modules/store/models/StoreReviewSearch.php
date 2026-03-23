<?php

namespace stockDepartment\modules\store\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\helpers\iHelper;
use common\modules\store\models\StoreReviews;


/**
 * StoreReviewSearch represents the model behind the search form about `common\modules\store\models\StoreReviews`.
 */
class StoreReviewSearch extends StoreReviews
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'store_id', 'number_of_places', 'tl_delivery_proposal_id', 'delivery_datetime', 'rate', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['comment'], 'safe'],
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
        $query = StoreReviews::find();


//        if(iHelper::isClient()) {
//            $query->andFilterWhere([
//                'client_id' => Yii::$app->user->id,
//            ]);
//        }


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'client_id' => $this->client_id,
            'store_id' => $this->store_id,
            'number_of_places' => $this->number_of_places,
            'tl_delivery_proposal_id' => $this->tl_delivery_proposal_id,
            'delivery_datetime' => $this->delivery_datetime,
            'rate' => $this->rate,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
