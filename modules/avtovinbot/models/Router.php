<?php

namespace app\modules\avtovinbot\models;

use app\models\User;
use app\modules\avtovinbot\exceptions\FactoryException;
use app\modules\avtovinbot\models\StateName;
use app\modules\avtovinbot\state\StateFactory;

class Router
{
    private $telegram;
    private $update;
    public function __construct($telegram, $update)
    {
        $this->telegram = $telegram;
        $this->update = $update;
    }

    public function run()
    {
        $user = User::findOne($this->update->user_id);
        $factory = new StateFactory($this->telegram, $this->update);
        try {
            if (!$user) {
                $state = $factory->getState(StateName::START, $user);
                return $state->run();
            }
            $state = $factory->getState($user->state->current, $user);
            return $state->run();
        } catch (FactoryException $e) {
            echo '<hr>'.$e->getMessage().'<hr>';
        }
    }
}
