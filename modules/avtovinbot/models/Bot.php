<?php
/**
 * Abstract class for bot
 */
namespace app\modules\avtovinbot\models;

use app\models\Qiwibill;
use app\modules\avtovinbot\exceptions\StateException;
use app\modules\avtovinbot\exceptions\UserException;
use app\modules\avtovinbot\models\Telegram\TelegramButton;
use app\modules\avtovinbot\models\Telegram\TelegramMessage;
use app\models\User;
use app\models\Vininfo;
use Yii;
abstract class Bot extends \yii\base\Model
{
    //Различные сообщения
    protected const DEFOULT_CHOICE = 'defoult';
    protected const BUY_MESSAGE = 'buy';
    protected const EDIT_INLINE_MESSAGE = 'edit_inline_message';
    protected const NO_BUY_MESSAGE = 'no_buy';
    protected const SUCCESS_BUY_MESSAGE = 'success_buy';
    protected const ERROR_BUY_MESSAGE = 'error_buy';
    protected const INFO_NOT_FOUND_MESSAGE = 'info_not_found';

    protected $telegram;
    protected $telegramButton;
    protected $telegramMessage;
    protected $Qiwibill;
    protected $update;
    protected $user;
    protected $factory;
    protected $button;
    protected $emoji;

    protected $info;

    public function __construct($telegram, $update, $factory, $user)
    {
        $this->telegram = $telegram;
        $this->update = $update;
        $this->factory = $factory;
        $this->user = $user;
        $this->telegramButton = new TelegramButton;
        $this->telegramMessage = new TelegramMessage;
        $this->Qiwibill = new Qiwibill($this->user);
        $this->emoji = include(Yii::getAlias('@app') . '/components/emoji.php');
        $this->button = include(Yii::getAlias('@app') . '/components/button.php');
    }

    abstract protected function getMessage($choice);
    abstract protected function getButton($choice);
    abstract protected function answer();
    abstract protected function answerReCall();
    abstract protected function validateUpdate();
    abstract protected function validateMessage();
    abstract protected function command();
    /**
     * Запуск состояния
     */
    public function run()
    {
        // Проверка на команду в меню
        if ($StateName = $this->command()) {
            return $this->Transition($StateName);
        }
        if (!$this->re_call()) {
            return $this->answer();
        }
        if (!$this->validateUpdate()) {
            return $this->telegram->send($this->validateMessage());
      
        }
        return $this->answerReCall();
    }

    protected function updateState($newState){
        $this->user->state->previous = $this->user->state->current;
        $this->user->state->current = $newState;
        if (!$this->user->state->save()) {
            throw new StateException('Can not update State');
            return false;
        }
        return true;
    }
    /**
     * Проверка на повторямость состояния
     */
    protected function re_call(){
        if ($this->user->state->current == $this->user->state->previous) {
            return true;
        }
        $this->user->state->previous = $this->user->state->current;
        if (!$this->user->state->save()) {
            throw new StateException('Can not update State from re_call');
        }
        return false;
    }

    /**
     * Переход в другое состояние
     */
    protected function Transition($StateName)
    {
        $this->updateState($StateName);
        $newState = $this->factory->getState($StateName, $this->user);
        $newState->run();
    }

    protected function createPayment($amount)
    {
        $this->user->payment->user_id = $this->user->user_id;
        $this->user->payment->amount  = $amount;
        $this->user->payment->info = $this->user->state->current;
        $this->user->payment->billid = $this->user->qiwibill->billid;
        if ($this->user->payment->save()) {
            return true;
        }
        throw new UserException('Can not create payment');
        return false;
    }

    protected function createSubscribe($amount){
        $this->user->subscribe_duration = User::STATUS_SUBSCRIBE;
        $this->user->money += $amount;
        $this->user->subscribe_duration = date('Y-m-d H:i:s');

        return $this->user->save();
    }

    protected function getVinInfo()
    {
        $vininfo = new Vininfo();
        if ($info = $vininfo->getInfo($this->update->text)) {
            return $info;
        }

        return false;
    }
    
}

?>
