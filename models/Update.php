<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "update".
 *
 * @property int $id
 * @property string $update_id
 * @property string $message_id
 * @property string $user_id
 * @property string $name
 * @property string|null $username
 * @property string|null $language_code
 * @property string $chat_id
 * @property string|null $inline_text
 * @property string|null $update_type
 * @property string|null $text
 * @property string|null $date
 */
class Update extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'update';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['update_id', 'message_id', 'user_id', 'name', 'chat_id'], 'required'],
            [['date'], 'safe'],
            [['update_id', 'message_id', 'user_id', 'language_code', 'chat_id', 'update_type'], 'string', 'max' => 45],
            [['name', 'inline_text', 'text'], 'string', 'max' => 255],
            [['username'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'update_id' => 'Update ID',
            'message_id' => 'Message ID',
            'user_id' => 'User ID',
            'name' => 'Name',
            'username' => 'Username',
            'language_code' => 'Language Code',
            'chat_id' => 'Chat ID',
            'inline_text' => 'Inline Text',
            'update_type' => 'Update Type',
            'text' => 'Text',
            'date' => 'Date',
        ];
    }
}
