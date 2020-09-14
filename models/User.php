<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property string $user_id
 * @property string $chat_id
 * @property string $name
 * @property string|null $username
 * @property string|null $language_code
 * @property int|null $status
 * @property int|null $money
 * @property string|null $subscribe_duration
 * @property string $date
 *
 * @property Payment[] $payments
 * @property Qiwibill $qiwibill
 * @property State $state
 */
class User extends \yii\db\ActiveRecord
{
    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;
    public const STATUS_SUBSCRIBE = 2;
    public const DEFOULT_MONEY = 0;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'chat_id', 'name'], 'required'],
            [['status', 'money'], 'integer'],
            [['subscribe_duration', 'date'], 'safe'],
            [['user_id', 'chat_id', 'language_code'], 'string', 'max' => 45],
            [['name'], 'string', 'max' => 255],
            [['username'], 'string', 'max' => 100],
            [['user_id'], 'unique'],
            [['chat_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'chat_id' => 'Chat ID',
            'name' => 'Name',
            'username' => 'Username',
            'language_code' => 'Language Code',
            'status' => 'Status',
            'money' => 'Money',
            'subscribe_duration' => 'Subsctibe Duration',
            'date' => 'Date',
        ];
    }

    /**
     * Gets query for [[Payments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPayments()
    {
        return $this->hasMany(Payment::className(), ['user_id' => 'user_id']);
    }

    /**
     * Gets query for [[Qiwibill]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQiwibill()
    {
        return $this->hasOne(Qiwibill::className(), ['user_id' => 'user_id']);
    }

    /**
     * Gets query for [[State]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::className(), ['user_id' => 'user_id']);
    }
}
