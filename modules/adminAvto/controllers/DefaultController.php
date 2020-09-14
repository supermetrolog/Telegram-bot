<?php

namespace app\modules\adminAvto\controllers;
use app\models\User;
use app\models\State;
use yii\web\Controller;
use Yii;
/**
 * Default controller for the `adminAvto` module
 */
class DefaultController extends Controller
{
    /**
    * Renders the index view for the module
    * @return string
    */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
