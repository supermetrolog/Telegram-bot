<?php

namespace app\modules\avtovinbot\state;

use app\models\Qiwibill;
use app\modules\avtovinbot\models\Bot;
use app\modules\avtovinbot\models\StateName;
use app\modules\avtovinbot\models\UpdateType;
use app\modules\avtovinbot\exceptions\StateException;

class BuySubscribe extends Bot
{
    //Стоимость услуги
    private const AMOUNT = 2;
    
    /**
     * Первое сообщение после перехода в это состояние
     */
    protected function answer()
    {

        $message = $this->getMessage(self::BUY_MESSAGE);
        $this->telegram->edit($message);
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

        if ($this->update->text == StateName::VIEW_SHORT_INFO) {
            $this->Transition(StateName::VIEW_SHORT_INFO);
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
                    '<b>Купить подписку</b>';

                $this->telegramMessage->chat_id = $this->user->chat_id;
                $this->telegramMessage->message_id = (string)($this->update->message_id - 1);

                return $this->telegramMessage->getMessage();
                break;
            case self::BUY_MESSAGE:
                $this->telegramMessage->text = '<b>BITCH</b>';
                $this->telegramMessage->chat_id = $this->user->chat_id;
                $this->telegramMessage->reply_markup = $this->getButton($choice);
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
                $this->telegramMessage->text = '<b>Поздравляюс с покупкой подписки</b>';
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
                $button =[
                    [
                        ['text' => $this->button[StateName::ENTER_VIN]['TEXT']]
                    ]
                ];
                
                return $this->telegramButton->keyboard($button);
                break;
            case self::BUY_MESSAGE:
                $button = [
                    [
                        ['text'=> 'Купить', 'url' => $this->Qiwibill->getPayUrl(self::AMOUNT)],
                        ['text'=> 'Проверить', 'callback_data'=> Qiwibill::CONTROLL]
                    ],
                    [
                        ['text'=> 'Купить один просмотр', 'callback_data'=> StateName::VIEW_SHORT_INFO]
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
