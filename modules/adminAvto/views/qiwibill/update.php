<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Qiwibill */

$this->title = 'Update Qiwibill: ' . $model->user_id;
$this->params['breadcrumbs'][] = ['label' => 'Qiwibills', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->user_id, 'url' => ['view', 'id' => $model->user_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="qiwibill-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
