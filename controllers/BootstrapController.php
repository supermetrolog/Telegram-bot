<?php 
namespace app\controllers;

use Yii;
use yii\web\controller;

/**
 * Testing Bootstrap4
 */
class BootstrapController extends Controller
{
	
	public function actionIndex(){
		

		return $this->render('index');
	}

	public function beforeAction($action){
		if ($action->id == 'index') {
			$this->layout = false;
		}
		
		return parent::beforeAction($action);
	}
}


 ?>