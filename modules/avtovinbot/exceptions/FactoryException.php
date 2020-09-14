<?php

namespace app\modules\avtovinbot\exceptions;
use yii\base\Exception;

class FactoryException extends Exception
{
    protected $message;

    public function __construct($message = 'There is no such state')
    {
        $this->message = $message;
    }
}
