<?php

namespace backend\modules\ruangrapat\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Reservation;

/**
 * ReservationSearch represents the model behind the search form of `common\models\Reservation`.
 */
class ReservationSearch extends Reservation
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'room_id'], 'integer'],
            [['document', 'affiliation', 'reason_of_use', 'date', 'start_time', 'end_time', 'status', 'created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = Reservation::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'room_id' => $this->room_id,
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'document', $this->document])
            ->andFilterWhere(['like', 'affiliation', $this->affiliation])
            ->andFilterWhere(['like', 'reason_of_use', $this->reason_of_use])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
