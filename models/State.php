<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "state".
 *
 * @property string $user_id
 * @property string $current
 * @property string $previous
 * @property string $date
 *
 * @property User $user
 */
class State extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'state';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'current', 'previous'], 'required'],
            [['date'], 'safe'],
            [['user_id', 'current', 'previous'], 'string', 'max' => 45],
            [['user_id'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'current' => 'Current',
            'previous' => 'Previous',
            'date' => 'Date',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }
}
