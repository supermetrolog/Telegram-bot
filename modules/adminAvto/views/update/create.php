<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Update */

$this->title = 'Create Update';
$this->params['breadcrumbs'][] = ['label' => 'Updates', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="update-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
