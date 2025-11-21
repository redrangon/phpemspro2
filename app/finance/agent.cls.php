<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:36
 */

namespace PHPEMSPRO\finance;

use PHPEMSPRO\user\model\users;
class agent
{
    public $_user;

    public function __construct()
    {
        $_user = \PHPEMSPRO\session::getInstance()->getSessionUser();
        if (!$_user['sessionuserid']) {
            $message = array(
                'statusCode' => 300,
                "message" => "请您先登陆",
                "callbackType" => "forward",
                "forwardUrl" => "index.php?user-app-login"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        $this->_user = users::getUserById($_user['sessionuserid']);
        $group = users::getGroupByCode($this->_user['usergroupcode']);
        if ($group['groupmodel'] != 'agent') {
            $message = array(
                'statusCode' => 300,
                "message" => "您无权登陆代理后台",
                "callbackType" => "forward",
                "forwardUrl" => "index.php"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        \PHPEMSPRO\tpl::getInstance()->assign('_user', $this->_user);
        $orderstatus = array(1=>'待付款',2=>'已完成',99=>'已撤单');
        \PHPEMSPRO\tpl::getInstance()->assign('orderstatus',$orderstatus);
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