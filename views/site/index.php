<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $modelUsers app\models\Users */
/* @var $modelMessages app\models\Messages */
/* @var $users app\models\Users[] */
/* @var $messages app\models\Messages[] */

$this->title = Yii::$app->name;
$this->registerJs(<<<JS
var clientIp;
$('[name="Users[nickname]"]').on('input', function() {
   if ($(this).val().length > 2) {
        $('[name="Messages[text]"]').attr('disabled', false);
   } else {
       $('[name="Messages[text]"]').val('').attr('disabled', true);
   }
});
$('[name="send-button"]').on('click', function() {
    var button  = $(this);
    var userId  = $('[name="Messages[id_user]').val();
    var message = $('[name="Messages[text]"]').val();

    button.attr('disabled', true);
    if (userId.length === 0) {
        var nickNameVal = $('[name="Users[nickname]"]').val();
        if (nickNameVal.length === 0) {
            return false;
        }

        $.getJSON('http://ip.jsontest.com/?callback=?', function(data) {
            if (data instanceof Object) {
                clientIp = data.ip;
            }
        });
        
        setTimeout(function() {
            if (isErrosExist()) {                
                return false;
            }
            
            $.ajax({
                url : '/site/create-user',
                type: 'post',
                data: {
                    Users: {
                        nickname: nickNameVal,
                        ip      : clientIp
                    }
                },
                async  : false,
                success: function(data) {
                    if (data.hasOwnProperty('user_id')) {
                        $('[name="Messages[id_user]').val(data.user_id);
                        $('[name="Users[nickname]"]').attr('disabled', true);                        
                        userId = data.user_id;
                    }
                }
            });
        }, 300);
    }

    setTimeout(function() {
        if (isErrosExist()) {
            button.attr('disabled', false);
            return false;
        }
        
        if (message.length > 0) {
            $.ajax({
                url : '/site/create-message',
                type: 'post',
                data: {
                    Messages: {
                        id_user: userId,
                        text   : message
                    }
                },
                async  : false,
                success: function(data) {
                    button.attr('disabled', false);
                    if (data.length > 0) {
                        $('[data-chat-block]').append(data);
                        $('[name="Messages[text]"]').val('');
                        $("html, body").animate({scrollTop: $(document).height()}, 1000);
                    }
                }
            });
        }
    }, 350);
});
function isErrosExist() {
    if ($('.has-error').length > 0) {
        return true;
    }
    return false;
}
JS
)
?>
<div class="site-index">
    <div class="container">
        <div class="col-md-8">
            <h2 class="text-center"><?= Html::encode($this->title) ?></h2>
            <div class="row">
                <? $form = ActiveForm::begin(['id' => 'user-form', 'enableAjaxValidation' => true]) ?>

                    <?= $form->field($modelUsers, 'nickname')->textInput(['autofocus' => false]) ?>

                <? ActiveForm::end() ?>
            </div>

            <div class="row">
                <? $form = ActiveForm::begin(['id' => 'message-form', 'enableAjaxValidation' => true]) ?>

                    <?= $form->field($modelMessages, 'id_user', ['options' => ['style' => 'display: none']])->textInput() ?>

                    <?= $form->field($modelMessages, 'text')->textarea(['rows' => 5, 'disabled' => false, 'style' => 'resize: none']) ?>

                <? ActiveForm::end() ?>
            </div>

            <div class="row">
                <div class="form-group">
                    <?= Html::button('Send', ['class' => 'btn btn-primary', 'name' => 'send-button']) ?>
                </div>
            </div>

            <div class="row">
                <h2 class="text-center">Messages</h2>
                <div data-chat-block="1">
                    <?= Yii::$app->controller->renderPartial('_messages', ['data' => $messages]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <h2 class="text-center">Users</h2>
            <ul class="list-group custom-margin-top">
                <? foreach($users as $item): ?>

                    <li class="list-group-item">
                        <span><?= Html::encode($item->nickname) . ' (' . Html::encode($item->city) . ')' ?></span>
                        <span class="badge badge-default badge-pill"><?= $item->getMessages()->count() ?></span>
                    </li>

                <? endforeach ?>
            </ul>
        </div>
    </div>
</div>