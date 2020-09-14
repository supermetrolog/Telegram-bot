<?php
namespace app\modules\avtovinbot\exceptions;
use yii\base\Exception;
class TelegramException extends Exception
{
    protected $message;
    public function __construct($message)
    {
        $this->message = $message;
    }
}
