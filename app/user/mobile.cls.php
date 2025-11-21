<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:36
 */

namespace PHPEMSPRO\user;

use PHPEMSPRO\user\model\users;

class mobile
{
    public $_user;

    public function __construct()
    {
        $_user = \PHPEMSPRO\session::getInstance()->getSessionUser();
        if(\PHPEMSPRO\route::getUrl('method') != 'login')
        {
            if(!$_user['sessionuserid'])
            {
                $message = array(
                    'statusCode' => 301,
                    "message" => "请您先登陆",
                    "callbackType" => "forward",
                    "forwardUrl" => "index.php?user-mobile-login"
                );
                \PHPEMSPRO\route::urlJump($message);
            }
            $this->_user = users::getUserById($_user['sessionuserid']);
            \PHPEMSPRO\tpl::getInstance()->assign('_user', $this->_user);
        }
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