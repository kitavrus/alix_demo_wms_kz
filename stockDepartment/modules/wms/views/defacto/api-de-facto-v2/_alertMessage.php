<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 01.11.2016
 * Time: 9:36
 */
 if (!empty($errorMessage)) {
    echo \yii\bootstrap\Alert::widget([
        'options' => [
            'id' => 'alert-message-inbound',
            'class' => 'alert-danger',
        ],
        'body' =>
            '<h3>'
            . $errorMessage
            . '</h3>'
        ,
    ]);
}