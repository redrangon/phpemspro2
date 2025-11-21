<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:36
 */

namespace PHPEMSPRO\exam;

use PHPEMSPRO\exam\model\training;
use PHPEMSPRO\user\model\users;

abstract class app
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
        $this->currentsubject = $_user['currentsubject'];
        $this->currentbasic = $_user['currentbasic'];
        \PHPEMSPRO\tpl::getInstance()->assign('_user',$this->_user = users::getUserById($_user['sessionuserid']));
        $trainings = training::getTrainingList(array(),1,5);
        \PHPEMSPRO\tpl::getInstance()->assign('navtrainings',$trainings['data']);
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