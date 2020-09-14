<?php

namespace app\modules\avtovinbot\state;

use app\models\Qiwibill;
use app\modules\avtovinbot\models\Bot;
use app\modules\avtovinbot\models\StateName;
use app\modules\avtovinbot\models\UpdateType;
use app\modules\avtovinbot\exceptions\StateException;

class ViewAllInfo extends Bot
{
    
    /**
     * Первое сообщение после перехода в это состояние
     */
    protected function answer()
    {
        $message = $this->getMessage(self::DEFOULT_CHOICE);
        return $this->telegram->send($message);
    }
    /**
     * Ответ на сообщение пользователя
     */
    protected function answerReCall()
    {
        return true;
    }

    /**
     * Сообщение которое будет отправлено пользователю
     */
    protected function getMessage($choice)
    {   
        switch ($choice) {
            case self::DEFOULT_CHOICE:
                $this->telegramMessage->text =
                    '<b>Вся информация</b>';

                $this->telegramMessage->chat_id = $this->user->chat_id;
                $this->telegramMessage->message_id = (string)($this->update->message_id - 1);
                return $this->telegramMessage->getMessage();
                break;
            default:
                throw new StateException('Incorrect var "$choice"');
                return false;
                break;
        }
        throw new StateException('Incorrect var "$choice"');
        return false;
    }

    protected function getButton($choice)
    {   
        switch ($choice) {
            case self::DEFOULT_CHOICE:
                $button =[
                    [
                        ['text' => $this->button[StateName::ENTER_VIN]['TEXT']]
                    ]
                ];
                
                return $this->telegramButton->keyboard($button);
                break;
            default:
                throw new StateException('Incorrect var "$choice"');
                return false;
                break;
        }
        throw new StateException('Incorrect var "$choice"');
        return false;
    }
    

    /**
     * Валидация данных введенных пользователем
     */
    protected function validateUpdate()
    {
        return false;
    }
    /**
     * Сообщение пользователю при вводе некорректных данных
     */
    protected function validateMessage()
    {
        $this->telegramMessage->text = '<b>Не понимаю</b>';
        $this->telegramMessage->chat_id = $this->user->chat_id;
        $this->telegramMessage->reply_markup = $this->getButton(self::DEFOULT_CHOICE);

        return $this->telegramMessage->getMessage();
    }

    protected function command()
    {
        if ($this->update->update_type != UpdateType::TEXT) {
            return false;
        }
        switch ($this->update->text) {
            case $this->button[StateName::ENTER_VIN]['TEXT']:
                return $this->button[StateName::ENTER_VIN]['NEXT'];
                break;
            case $this->button[StateName::START_COMMAND]['TEXT']:
                return $this->button[StateName::START_COMMAND]['NEXT'];
                break;
            default:
                return false;
                break;
        }
    }
}
