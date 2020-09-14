<?php

namespace app\modules\avtovinbot\state;

use app\modules\avtovinbot\exceptions\FactoryException;
use app\modules\avtovinbot\models\StateName;

class StateFactory
{
    private $telegram;
    private $update;

    public function __construct($telegram, $update)
    {
        
        $this->telegram = $telegram;
        $this->update = $update;
    }
   
    public function getState($StateName, $user)
    {

        switch ($StateName) {
            case StateName::START:
                return new Start($this->telegram, $this->update, $this, $user);
                break;
            case StateName::ENTER_VIN:
                return new EnterVin($this->telegram, $this->update, $this, $user);
                break;
            case StateName::VIEW_SHORT_INFO:
                return new ViewShortInfo($this->telegram, $this->update, $this, $user);
                break;
            case StateName::BUY_SUBSCRIBE:
                return new BuySubscribe($this->telegram, $this->update, $this, $user);
                break;
            case StateName::VIEW_ALL_INFO:
                return new ViewAllInfo($this->telegram, $this->update, $this, $user);
                break;
            default:
                throw new FactoryException();
                break;
        }
    }
}
