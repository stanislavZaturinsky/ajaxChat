<?php

use yii\helpers\Html;

/* @var $data app\models\Messages[] */
?>

<? foreach ($data as $item): ?>

    <div class="row">
        <p><?= Html::encode($item->idUser->nickname) . ' | ' . $item->date ?></p>
        <p><?= Html::encode($item->text) ?></p>
        <hr>
    </div>

<? endforeach ?>