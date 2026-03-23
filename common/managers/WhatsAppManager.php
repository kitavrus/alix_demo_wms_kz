<?php
namespace common\managers;
/**
 * Created by PhpStorm.
 * User: Jack22
 * Date: 27.08.2015
 * Time: 17:17
 */
class WhatsAppManager
{
    private $username = "77771506633";
    private $password ='/cdxVuAP//LjohyAjcZ2a6Fn3YI=';
    private $nickname = "AutoInCity.kz";
    private $debug = false;

    /*
     * Sent message
     * @param string $target
     * */
    public function sendTextMessage($target,$message)
    {
        $target = '7'.str_replace('-','',$target);
        $w = new \WhatsProt($this->username, $this->nickname, $this->debug);
        $w->connect();
        $w->loginWithPassword($this->password);
       return $w->sendMessage($target , $message);
    }
}