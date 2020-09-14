<?php

namespace app\modules\avtovinbot\state;

use app\models\Qiwibill;
use app\modules\avtovinbot\models\Bot;
use app\modules\avtovinbot\models\StateName;
use app\modules\avtovinbot\models\UpdateType;
use app\modules\avtovinbot\exceptions\StateException;

class ViewShortInfo extends Bot
{
    //Стоимость услуги
    private const AMOUNT = 1;

    /**
     * Первое сообщение после перехода в это состояние
     */
    protected function answer()
    {   
        if ($this->update->update_type == UpdateType::CALLBACK_QUERY) {
            $message = $this->getMessage(self::EDIT_INLINE_MESSAGE);
            $this->telegram->edit($message);
            return true;
        }

        if (!$this->info = $this->getVinInfo()) {
            $message = $this->getMessage(self::INFO_NOT_FOUND_MESSAGE);
            return $this->telegram->send($message);
        }
        $message = $this->getMessage(self::DEFOULT_CHOICE);
        $this->telegram->send($message);

        $message = $this->getMessage(self::BUY_MESSAGE);
        $this->telegram->send($message);
        return true;
    }
    /**
     * Ответ на сообщение пользователя
     */
    protected function answerReCall()
    {
        if ($this->update->text == Qiwibill::CONTROLL) {
            if ($this->Qiwibill->controll()) {

                if (!$this->createSubscribe(self::AMOUNT)) {
                    $message = $this->getMessage(self::ERROR_BUY_MESSAGE);
                    return $this->telegram->send($message);
                }

                $message = $this->getMessage(self::SUCCESS_BUY_MESSAGE);
                $this->telegram->send($message);

                $this->createPayment(self::AMOUNT);

                return $this->Transition(StateName::VIEW_ALL_INFO);
            }

            $message = $this->getMessage(self::NO_BUY_MESSAGE);
            return  $this->telegram->send($message);
        }

        if ($this->update->text == StateName::BUY_SUBSCRIBE) {
            $this->Transition(StateName::BUY_SUBSCRIBE);
        }
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
                    '<b>Модель: кадилак</b>'
                    . 'Купите один просмотр или подписку';

                $this->telegramMessage->chat_id = $this->user->chat_id;
                $this->telegramMessage->reply_markup = $this->getButton(self::DEFOULT_CHOICE);

                return $this->telegramMessage->getMessage();
                break;
            case self::BUY_MESSAGE:
                $this->telegramMessage->text = '<b>Пожалуйста оплатите и нажмите на кнопку "Проверить"</b>';
                $this->telegramMessage->chat_id = $this->user->chat_id;
                $this->telegramMessage->reply_markup = $this->getButton(self::BUY_MESSAGE);

                return $this->telegramMessage->getMessage();
                break;
            case self::EDIT_INLINE_MESSAGE:
                $this->telegramMessage->text = '<b>Пожалуйста оплатите и нажмите на кнопку "Проверить"</b>';
                $this->telegramMessage->chat_id = $this->user->chat_id;
                $this->telegramMessage->reply_markup = $this->getButton(self::BUY_MESSAGE);
                $this->telegramMessage->message_id = $this->update->message_id;

                return $this->telegramMessage->getMessage();
                break;
            case self::NO_BUY_MESSAGE:
                $this->telegramMessage->text = '<b>Оплата не была произведена</b>';
                $this->telegramMessage->chat_id = $this->user->chat_id;
                $this->telegramMessage->reply_markup = $this->getButton(self::DEFOULT_CHOICE);

                return $this->telegramMessage->getMessage();
                break;
            case self::SUCCESS_BUY_MESSAGE:
                $this->telegramMessage->text = '<b>Покупка произведена</b>';
                $this->telegramMessage->chat_id = $this->user->chat_id;
                $this->telegramMessage->reply_markup = $this->getButton(self::DEFOULT_CHOICE);

                return $this->telegramMessage->getMessage();
                break;
            case self::ERROR_BUY_MESSAGE:
                $this->telegramMessage->text = '<b>Возникли проблемы с покупкой обратитесь пожалуйста сюда: </b>';
                $this->telegramMessage->chat_id = $this->user->chat_id;
                $this->telegramMessage->reply_markup = $this->getButton(self::DEFOULT_CHOICE);
                
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
                $button = [
                    [
                        ['text' => $this->button[StateName::ENTER_VIN]['TEXT']]
                    ]
                ];

                return $this->telegramButton->keyboard($button);
                break;
            case self::BUY_MESSAGE:
                $button = [
                    [
                        ['text' => 'Купить один просмотр', 'url' => $this->Qiwibill->getPayUrl(self::AMOUNT)],
                        ['text' => 'Проверить', 'callback_data' => Qiwibill::CONTROLL]
                    ],
                    [
                        ['text' => 'Купить подписку', 'callback_data' => StateName::BUY_SUBSCRIBE]
                    ]
                ];

                return $this->telegramButton->inlineKeyboard($button);
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
        if ($this->update->update_type != UpdateType::CALLBACK_QUERY) {
            return false;
        }
        return true;
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
