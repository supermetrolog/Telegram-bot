<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Update;

/**
 * UpdateSearch represents the model behind the search form of `app\models\Update`.
 */
class UpdateSearch extends Update
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['update_id', 'message_id', 'user_id', 'name', 'username', 'language_code', 'chat_id', 'inline_text', 'update_type', 'text', 'date'], 'safe'],
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
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Update::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'date' => $this->date,
        ]);

        $query->andFilterWhere(['like', 'update_id', $this->update_id])
            ->andFilterWhere(['like', 'message_id', $this->message_id])
            ->andFilterWhere(['like', 'user_id', $this->user_id])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'language_code', $this->language_code])
            ->andFilterWhere(['like', 'chat_id', $this->chat_id])
            ->andFilterWhere(['like', 'inline_text', $this->inline_text])
            ->andFilterWhere(['like', 'update_type', $this->update_type])
            ->andFilterWhere(['like', 'text', $this->text]);

        return $dataProvider;
    }
}
