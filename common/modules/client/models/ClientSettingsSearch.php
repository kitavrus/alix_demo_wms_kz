<?php

namespace common\modules\client\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\client\models\ClientSettings;

/**
 * ClientSettingsClientSettingsSearch represents the model behind the search form about `common\modules\client\models\ClientSettings`.
 */
class ClientSettingsSearch extends ClientSettings
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'deleted', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'option_type'], 'integer'],
            [['option_name', 'option_value', 'default_value', 'description'], 'safe']
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
    public function search($client_id)
    {
        $query = ClientSettings::find();

        $query->andFilterWhere([
            'client_id' => $client_id,
        ]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }
}
