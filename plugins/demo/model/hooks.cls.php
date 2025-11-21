<?php

namespace PHPEMSPRO\plugins\demo\model;

use PHPEMSPRO\plugin\model\plugin;
use PHPEMSPRO\user\model\users;

class hooks
{
    static $instance;

    static function getInstance()
    {
        if(self::$instance == NULL)
        {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function register()
    {
        plugin::getInstance()->registerHook('userRegister',array($this,'userRegister'),10);
    }

    public function userRegister($userid)
    {
        $plugin = plugin::getInstance()->getPluginByName('demo');
        $number = intval($plugin['pluginsetting']['coinnumber']);
        $user = users::modifyUser($userid,['usercoin' => $number]);
    }
}