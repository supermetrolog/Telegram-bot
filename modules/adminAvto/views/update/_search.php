<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UpdateSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="update-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'update_id') ?>

    <?= $form->field($model, 'message_id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'name') ?>

    <?php // echo $form->field($model, 'username') ?>

    <?php // echo $form->field($model, 'language_code') ?>

    <?php // echo $form->field($model, 'chat_id') ?>

    <?php // echo $form->field($model, 'inline_text') ?>

    <?php // echo $form->field($model, 'update_type') ?>

    <?php // echo $form->field($model, 'text') ?>

    <?php // echo $form->field($model, 'date') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
