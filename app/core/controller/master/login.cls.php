<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\core\controller\master;

use PHPEMSPRO\core\master;
use PHPEMSPRO\core\model\apps;
use PHPEMSPRO\user\model\users;

class login extends master
{
    public function logout()
    {
        \PHPEMSPRO\session::getInstance()->clearSessionUser();
        $message = array(
            'statusCode' => 200,
            "message" => "退出成功",
            "callbackType" => "forward",
            "forwardUrl" => "index.php?core-master"
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function index()
    {
        if(\PHPEMSPRO\route::get('userlogin'))
        {
            $args = \PHPEMSPRO\route::get('args');
            $user = \PHPEMSPRO\user\model\users::getUserByUsername($args['username']);
            if($user)
            {
                if($user['userpassword'] == md5($args['userpassword']))
                {
                    $sessionuser = array(
                        'sessionuserid' => $user['userid'],
                        'sessionusername' => $user['username'],
                        'sessionpassword' => $user['userpassword'],
                        'sessiongroupcode' => $user['usergroupcode']
                    );
                    \PHPEMSPRO\session::getInstance()->setSessionUser($sessionuser);
                    $message = array(
                        'statusCode' => 200,
                        "message" => "登陆成功",
                        "callbackType" => "forward",
                        "forwardUrl" => "index.php?core-master",
                        'callback' => 'cleardata'
                    );
                    \PHPEMSPRO\route::urlJump($message);
                }
            }
            $message = array(
                'statusCode' => 300,
                "message" => "用户名或密码错误"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        else
        {
            \PHPEMSPRO\tpl::getInstance()->display('login');
        }
    }
}