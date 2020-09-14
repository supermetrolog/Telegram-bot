<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\UpdateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Updates';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="update-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Update', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'update_id',
            'message_id',
            'user_id',
            'name',
            //'username',
            //'language_code',
            //'chat_id',
            //'inline_text',
            //'update_type',
            //'text',
            //'date',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
