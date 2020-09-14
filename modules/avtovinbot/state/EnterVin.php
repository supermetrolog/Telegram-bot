<?php

namespace app\modules\avtovinbot\state;

use app\modules\avtovinbot\models\Bot;
use app\modules\avtovinbot\models\StateName;
use app\modules\avtovinbot\models\UpdateType;
use app\modules\avtovinbot\exceptions\StateException;
use app\models\User;

class EnterVin extends Bot
{
    /**
     * Первое сообщение после перехода в это состояние
     */
    protected function answer()
    {
        $message = $this->getMessage(self::DEFOULT_CHOICE);
        $this->telegram->send($message);
    }
    /**
     * Ответ на сообщение пользователя
     */
    protected function answerReCall()
    {
        if ($this->user->status == User::STATUS_SUBSCRIBE) {
            return $this->Transition(StateName::VIEW_ALL_INFO);
        }

        return $this->Transition(StateName::VIEW_SHORT_INFO);
    }
    /**
     * Сообщение которое будет отправлено пользователю
     */
    protected function getMessage($choice)
    {
        switch ($choice) {
            case self::DEFOULT_CHOICE:
                $this->telegramMessage->text = '<b>Привет! Введи VIN автомобиля, по которому нужна информация</b>';
                $this->telegramMessage->chat_id = $this->user->chat_id;
                $this->telegramMessage->reply_markup = $this->getButton($choice);

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
                return $this->telegramButton->removeButton();
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
        if ($this->update->update_type != UpdateType::TEXT) {
            return false;
        }
        if (mb_strlen($this->update->text) < 5) {
            return false;
        }
        return true;
    }
    /**
     * Сообщение пользователю при вводе некорректных данных
     */
    protected function validateMessage()
    {
        $this->telegramMessage->text = '<b>Неверно введен VIN</b>';
        $this->telegramMessage->chat_id = $this->user->chat_id;
        $this->telegramMessage->reply_markup = $this->getButton(self::DEFOULT_CHOICE);
        return $this->telegramMessage->getMessage();
    }

    protected function command()
    {
        return false;
    }
}
