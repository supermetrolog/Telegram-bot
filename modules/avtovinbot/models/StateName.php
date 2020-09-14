<?php
namespace app\modules\avtovinbot\models;
/**
 * All state names
 */
class StateName
{
    public const START = 'Start';
    public const ENTER_VIN = 'EnterVin';
    public const VIEW_ALL_INFO = 'ViewAllInfo';
    public const VIEW_SHORT_INFO = 'ViewShortInfo';
    public const BUY_SUBSCRIBE = 'BuySubscribe';
    public const START_COMMAND = '/start';
}
