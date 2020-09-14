<?php
namespace app\modules\avtovinbot\models\Telegram;

use yii\base\Model;

class TelegramButton extends Model
{
    public $oneTimeKeyboard = false;
    public $resizeKeyboard = true;

    /**
     * Обычные кнопки
     */
    public function keyboard($buttons)
    {
        $keyboard = json_encode([
            'keyboard' => $buttons,
            'one_time_keyboard' => $this->oneTimeKeyboard,
            'resize_keyboard' => $this->resizeKeyboard
        ]);

        return $keyboard;
    }

     /**
     * Удаляет кнопки
     */
    public function removeButton(){
		$button = json_encode([
			'remove_keyboard'=> true
		]);
		return $button;
    }

    public function inlineKeyboard($buttons)
    {
        $inline_keyboard = json_encode(['inline_keyboard' => $buttons]);

        return $inline_keyboard;
    }
}
