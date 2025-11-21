<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:36
 */

namespace PHPEMSPRO\exam;

use PHPEMSPRO\user\model\users;

class teach
{
    public $_user;

    public function __construct()
    {
        $_user = \PHPEMSPRO\session::getInstance()->getSessionUser();
        if(!$_user['sessionuserid'])
        {
            $message = array(
                'statusCode' => 300,
                "message" => "请您先登陆",
                "callbackType" => "forward",
                "forwardUrl" => "index.php?user-app-login"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        $this->_user = users::getUserById($_user['sessionuserid']);
        if($this->_user['usergroupcode'] != 'teacher')
        {
            $message = array(
                'statusCode' => 300,
                "message" => "只有教师用户才能登陆本后台",
                "callbackType" => "forward",
                "forwardUrl" => "index.php"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        \PHPEMSPRO\tpl::getInstance()->assign('_user',$this->_user);
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