<?php

namespace stockDepartment\modules\city\models;

use common\modules\city\models\City;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use stockDepartment\modules\city\models\RouteDirectionCitySearchForm;
use yii\data\ArrayDataProvider;

/**
 * RouteDirectionSearch represents the model behind the search form about `common\modules\city\models\RouteDirectionToCity`.
 */
class RouteDirectionCitySearch extends RouteDirectionCitySearchForm
{
    public $cityId;
    public $regionId;
    public $countryId;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cityId', 'regionId', 'countryId'], 'integer'],
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
        $query = City::find()->select('city.id as cityId,
                                          city.name as cityName,
                                          region.name as regionName,
                                          country.name as countryName
                                          ')
                ->leftJoin('region','region.id = city.region_id')
                ->leftJoin('country','country.id = region.country_id')
                ->orderBy('cityName');

        $this->load($params);

        $query->andFilterWhere([
            'city.id' => $this->cityId,
            'region.id' => $this->regionId,
            'country.id' => $this->countryId,
        ]);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $query->asArray()->all(),
            'pagination' => false,
            'sort' => false,
        ]);

        return $dataProvider;
    }
}
