<?php
namespace app\modules\avtovinbot\models\Telegram;
use yii\base\Model;

class TelegramMessage extends Model
{
    public $chat_id;
    public $message_id;
    public $text = null;
    public $caption = null;
    public $parse_mode = 'HTML';
    public $reply_markup = null;
    public $disable_notification = true;

    public function getMessage()
    {
        $params = [];

        foreach ($this as $attribute => $value) {
            if ($value !== null) {
                $params[$attribute] = $value;
            }
        }

        return  $params;
    }
    
}
