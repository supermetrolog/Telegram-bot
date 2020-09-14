<?php

namespace app\modules\avtovinbot\state;

use app\modules\avtovinbot\models\Bot;
use app\modules\avtovinbot\models\StateName;
use app\models\User;
use app\models\State;
use app\modules\avtovinbot\exceptions\UserException;
/**
 * Первый запуск бота
 */
class Start extends Bot
{
    /**
     * Запуск состояния
     */
    public function run()
    {
        if (!$this->createUser()) {
            return false;
        }
        $this->Transition(StateName::ENTER_VIN);
    }

    private function createUser()
    {
        $user = new User();
        $state = new State();

        User::getDb()->transaction(function($db) use ($user, $state) {
            $user->user_id = $this->update->user_id;
            $user->chat_id = $this->update->chat_id;
            $user->name = $this->update->name;
            $user->username = $this->update->username;
            $user->language_code = $this->update->language_code;
            $user->status = User::STATUS_ACTIVE;
            $user->money = User::DEFOULT_MONEY;

            if (!$user->save()) {
                throw new UserException('Can not create User');
                return false;
            }
            State::getDb()->transaction(function($db) use ($state) {
                $state->user_id = $this->update->user_id;
                $state->current = StateName::START;
                $state->previous = StateName::START;
                if (!$state->save()) {
                    throw new UserException('Can not create User');
                    return false;
                }
            });
        });

        $this->user = $user;
        return true;
    }
    /**
     * Первое сообщение после перехода в это состояние
     */
    protected function answer()
    {
        return true;
    }
    /**
     * Сообщение которое будет отправлено пользователю
     */
    protected function getMessage()
    {
        return true;
    }
    /**
     * Ответ на сообщение пользователя
     */
    protected function answerReCall()
    {
        return true;
    }

    /**
     * Валидация данных введенных пользователем
     */
    protected function validateUpdate()
    {
        return true;
    }
    /**
     * Сообщение пользователю при вводе некорректных данных
     */
    protected function validateMessage()
    {
       return true;
    }
    /**
     * Нажатие кнопки
     */
    protected function command()
    {
        return false;
    }
}
