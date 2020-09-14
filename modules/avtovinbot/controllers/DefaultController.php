<?php

namespace app\modules\avtovinbot\controllers;

use app\models\Vininfo;
use app\modules\avtovinbot\exceptions\StateException;
use app\modules\avtovinbot\exceptions\TelegramException;
use app\modules\avtovinbot\exceptions\UserException;
use app\modules\avtovinbot\models\Telegram\Telegram;
use app\modules\avtovinbot\models\Connector;
use app\modules\avtovinbot\models\Router;
use yii\web\Controller;
use Yii;

/**
 * Default controller for the `avtovinbot` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        echo "<pre>";
        // $vininfo = new Vininfo();
        // $vin = 'SDAsdasd';
        // print_r($vininfo->getInfo($vin));
        // die;
        
        $params = include(Yii::getAlias('@app') . '/config/bot_params.php');
     
        $telegram = new Telegram($params['token'], $params['domain_hook']);
        
        try {
            $updates = $telegram->getUpdates();
            $update = (new Connector($updates->result[0]))->sortUpdate();
            if (!$update) {
                return false;
            }
            (new Router($telegram, $update))->run();
        } catch (TelegramException $e) {
            echo '<hr>'.$e->getMessage().'<hr>';
        } catch (UserException $e){
            echo '<hr>'.$e->getMessage().'<hr>';
        } catch (StateException $e){
            echo '<hr>'.$e->getMessage().'<hr>';
        }


        echo "\n";
        print_r($update);

        return $this->render('index');
    }
}
