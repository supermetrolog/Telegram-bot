<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "payment".
 *
 * @property int $id
 * @property string $user_id
 * @property string|null $currency
 * @property int $amount
 * @property string|null $info
 * @property string $billid
 * @property string|null $date
 *
 * @property User $user
 */
class Payment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'amount', 'billid'], 'required'],
            [['amount'], 'integer'],
            [['date'], 'safe'],
            [['user_id', 'currency', 'info'], 'string', 'max' => 45],
            [['billid'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'currency' => 'Currency',
            'amount' => 'Amount',
            'info' => 'Info',
            'billid' => 'Billid',
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
