<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:36
 */

namespace PHPEMSPRO\api;

use PHPEMSPRO\user\model\users;

class api
{
    public $_user;

    public function __construct()
    {
        $_user = \PHPEMSPRO\session::getInstance()->getSessionUser();
        if($_user['sessionuserid'])
        {
            $this->_user = users::getUserById($_user['sessionuserid']);
            \PHPEMSPRO\tpl::getInstance()->assign('_user', $this->_user);
        }
    }

    public function json($message)
    {
        exit(json_encode($message,JSON_UNESCAPED_UNICODE));
    }

    public function display()
    {
        if(method_exists($this,'_start'))$this->_start();
        $selection = \PHPEMSPRO\route::getUrl('selection');
        if(method_exists($this,$selection))
        {
            $this->$selection();
        }
        else
        {
            $this->index();
        }
        if(method_exists($this,'_end'))$this->_end();
    }
}