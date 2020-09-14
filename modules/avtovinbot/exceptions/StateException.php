<?php
namespace app\modules\avtovinbot\exceptions;
use yii\base\Exception;
class StateException extends Exception
{
    protected $message;
    public function __construct($message)
    {
        $this->message = $message;
    }
}
