<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\wxlogin\controller\app;

use PHPEMSPRO\wxlogin\app;
use PHPEMSPRO\wxlogin\model\wxlogin;

class index extends app
{
	public function autologin()
    {
        $sessionid = \PHPEMSPRO\session::getInstance()->getSessionId();
        $info = wxlogin::getLogin($sessionid);
        $user = $info['wxinfo'];
        if($user['userid'])
        {
            $sessionuser = array(
                'sessionuserid' => $user['userid'],
                'sessionpassword' => $user['userpassword'],
                'sessionip' => \PHPEMSPRO\route::getClientIp(),
                'sessiongroupcode' => $user['usergroupcode'],
                'sessionlogintime' => TIME,
                'sessionusername' => $user['username']
            );
            \PHPEMSPRO\session::getInstance()->setSessionUser($sessionuser);
            wxlogin::delLogin($sessionid);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "index.php"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        $message = array(
            'statusCode' => 200
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function login()
    {
        $sessionid = \PHPEMSPRO\session::getInstance()->getSessionId();
        $url = urlencode(\PHPEMSPRO\config::webpath.'index.php?wxlogin-mobile-index-pclogin&sessionid='.$sessionid);
        \PHPEMSPRO\tpl::getInstance()->assign('url',$url);
        \PHPEMSPRO\tpl::getInstance()->display('login');
    }

    public function index()
    {
		//
    }
}