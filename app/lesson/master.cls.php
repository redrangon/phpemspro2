<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:36
 */

namespace PHPEMSPRO\lesson;
use PHPEMSPRO\user\model\users;
class master
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
                "forwardUrl" => "index.php?core-master-login"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        $this->_user = users::getUserById($_user['sessionuserid']);
        if ($this->_user['usergroupcode'] != 'webmaster') {
            $message = array(
                'statusCode' => 300,
                "message" => "您无权登陆后台",
                "callbackType" => "forward",
                "forwardUrl" => "index.php?core-master-login"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        \PHPEMSPRO\tpl::getInstance()->assign('_user', $this->_user);
        $apps = \PHPEMSPRO\core\model\apps::getAppList();
        \PHPEMSPRO\tpl::getInstance()->assign('apps', $apps);
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