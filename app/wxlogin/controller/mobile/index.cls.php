<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\wxlogin\controller\mobile;

use PHPEMSPRO\core\model\apps;
use PHPEMSPRO\user\model\users;
use PHPEMSPRO\wxlogin\mobile;
use PHPEMSPRO\wxlogin\model\wxlogin;

class index extends mobile
{public function pclogin()
    {
        if(!$_SESSION['autosessionid'])
        {
            $sessionid = \PHPEMSPRO\route::get('sessionid');
            $_SESSION['autosessionid'] = $sessionid;
        }
        else
        {
            $sessionid = $_SESSION['autosessionid'];
        }
        if(!$sessionid)
        {
            $message = array(
                'statusCode' => 300,
                "message" => "非法操作"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        $openid = \PHPEMSPRO\wxpay::getInstance()->getwxopenid(true);
        $user = users::getUserByOpenId($openid);
        if($user)
        {
            $args = array(
                'wxsid' => $sessionid,
                'wxinfo' => $user,
                'wxtime' => TIME,
                'wxtoken' => md5($sessionid.\PHPEMSPRO\config::secretkey)
            );
            wxlogin::addLogin($args);
        }
        else
        {
            if(\PHPEMSPRO\config::wxlogin['autoreg'])
            {
                $wxuser = \PHPEMSPRO\wxpay::getInstance()->getUserInfo();
                $username = 'wx_'.uniqid();
                $defaultgroup = users::getDefaultGroup();
                $args = array(
                    'useropenid' => $openid,
                    'usertruename' => $wxuser['nickname'],
                    'username' => $username,
		    'userphone' => $username,
                    'useremail' => $username.\PHPEMSPRO\config::wxlogin['emdomain'],
                    'usergroupcode' => $defaultgroup['groupcode'],
                    'userpassword' => md5($username),
                    'userphoto' => $wxuser['headimgurl']
                );
                users::addUser($args);
                $user = users::getUserByOpenId($openid);
                $args = array(
                    'wxsid' => $sessionid,
                    'wxinfo' => $user,
                    'wxtime' => TIME,
                    'wxtoken' => md5($sessionid.\PHPEMSPRO\config::secretkey)
                );
                wxlogin::addLogin($args);
            }
            else
            {
                $_SESSION['bindopenid'] = $openid;
                $_SESSION['bindtype'] = 'pc';
                header("location:index.php?wxlogin-mobile-index-bindlogin");
                exit;
            }
        }
        $_SESSION['autosessionid'] = null;
        \PHPEMSPRO\tpl::getInstance()->assign("user",$user);
        \PHPEMSPRO\tpl::getInstance()->display('login_pc');
    }

    public function bindlogin()
    {
        if(\PHPEMSPRO\route::get('userlogin'))
        {
            $tmp = \PHPEMSPRO\session::getInstance()->getSessionValue();
            if(TIME - $tmp['sessionlasttime'] < 1)
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "操作失败"
                );
                \PHPEMSPRO\route::urlJump($message);
            }
            $args = \PHPEMSPRO\route::get('args');
            $user = users::getUserByUserName($args['username']);
            if($user['userid'])
            {
                if($user['userpassword'] == md5($args['userpassword']))
                {
                    users::modifyUser($user['userid'],array('useropenid' => $_SESSION['bindopenid']));
                    if($_SESSION['bindtype'] == 'pc')
                    {
                        $args = array(
                            'wxsid' => $_SESSION['autosessionid'],
                            'wxinfo' => $user,
                            'wxtime' => TIME,
                            'wxtoken' => md5($_SESSION['autosessionid'].\PHPEMSPRO\config::secretkey)
                        );
                        wxlogin::addLogin($args);
                        $_SESSION['bindopenid'] = null;
                        $_SESSION['bindtype'] = null;
                        $_SESSION['autosessionid'] = null;
                        \PHPEMSPRO\tpl::getInstance()->assign("user",$user);
                        \PHPEMSPRO\tpl::getInstance()->display('login_pc');
                    }
                    else
                    {
                        \PHPEMSPRO\session::getInstance()->setSessionUser(array('sessionuserid'=>$user['userid'],'sessionpassword'=>$user['userpassword'],'sessionip'=>\PHPEMSPRO\route::getClientIp(),'sessiongroupid'=>$user['usergroupid'],'sessionlogintime'=>TIME,'sessionusername'=>$user['username']));
                        $_SESSION['bindopenid'] = null;
                        $_SESSION['bindtype'] = null;
                        $message = array(
                            'statusCode' => 201,
                            "message" => "操作成功",
                            "callbackType" => 'forward',
                            "forwardUrl" => "index.php"
                        );
                        \PHPEMSPRO\route::urlJump($message);
                    }
                }
                else
                {
                    $message = array(
                        'statusCode' => 300,
                        "message" => "操作失败"
                    );
                    \PHPEMSPRO\route::urlJump($message);
                }
            }
            else
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "操作失败"
                );
                \PHPEMSPRO\route::urlJump($message);
            }
        }
        else
        {
            \PHPEMSPRO\tpl::getInstance()->display('login');
        }
    }

    public function bindregister()
    {
        if(\PHPEMSPRO\route::get('userregister'))
        {
            $app = apps::getAppByCode('user');
            if($app['appsetting']['closeregist'])
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "管理员禁止了用户注册"
                );
                \PHPEMSPRO\route::urlJump($message);
            }
            $fob = array('admin','管理员','站长');
            $args = \PHPEMSPRO\route::get('args');
            $defaultgroup = users::getDefaultGroup();
            if(!$defaultgroup['groupid'] || !trim($args['username']))
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "用户不能注册"
                );
                \PHPEMSPRO\route::urlJump($message);
            }
            $randcode = \PHPEMSPRO\route::get('randcode');
            if((!$randcode) || ($randcode != $_SESSION['phonerandcode']['reg']) || ($args['userphone'] != $_SESSION['phonerandcode']['regphonenumber']))
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "验证码错误"
                );
                exit(json_encode($message));
            }
            else
            {
                $_SESSION['phonerandcode']['reg'] = 0;
            }
            $username = $args['username'];
            foreach($fob as $f)
            {
                if(strpos($username,$f) !== false)
                {
                    $message = array(
                        'statusCode' => 300,
                        'errorinput' => 'args[username]',
                        "message" => "用户已经存在"
                    );
                    \PHPEMSPRO\route::urlJump($message);
                }
            }
            $user = users::getUserByUserName($username);
            if($user)
            {
                $message = array(
                    'statusCode' => 300,
                    'errorinput' => 'args[username]',
                    "message" => "用户已经存在"
                );
                \PHPEMSPRO\route::urlJump($message);
            }
            $email = $args['useremail'];
            $user = users::getUserByEmail($email);
            if($user)
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "邮箱已经被注册"
                );
                \PHPEMSPRO\route::urlJump($message);
            }
            $phone = $args['userphone'];
            $user = users::getUserByPhone($phone);
            if($user)
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "手机号已经被注册"
                );
                \PHPEMSPRO\route::urlJump($message);
            }
            $fargs = array('useropenid' => $_SESSION['bindopenid'],'username' => $username,'userphone' => $phone,'usergroupcode' => $defaultgroup['groupcode'],'userpassword' => $args['userpassword'],'useremail' => $email);
            $id = users::addUser($fargs);
            $user = users::getUserById($id);
            if($_SESSION['bindtype'] == 'pc')
            {
                $args = array(
                    'wxsid' => $_SESSION['autosessionid'],
                    'wxinfo' => $user,
                    'wxtime' => TIME,
                    'wxtoken' => md5($_SESSION['autosessionid'].\PHPEMSPRO\config::secretkey)
                );
                wxlogin::addLogin($args);
                $_SESSION['bindopenid'] = null;
                $_SESSION['bindtype'] = null;
                $_SESSION['autosessionid'] = null;
                \PHPEMSPRO\tpl::getInstance()->assign("user",$user);
                \PHPEMSPRO\tpl::getInstance()->display('login_pc');
            }
            else
            {
                $sessionuser = array(
                    'sessionuserid' => $user['userid'],
                    'sessionusername' => $user['username'],
                    'sessionpassword' => $user['userpassword'],
                    'sessiongroupcode' => $user['usergroupcode'],
                    'sessionip'=>\PHPEMSPRO\route::getClientIp()
                );
                \PHPEMSPRO\session::getInstance()->setSessionUser($sessionuser);
                $_SESSION['bindopenid'] = null;
                $_SESSION['bindtype'] = null;
                $message = array(
                    'statusCode' => 201,
                    "message" => "操作成功",
                    "callbackType" => 'forward',
                    "forwardUrl" => "index.php"
                );
                \PHPEMSPRO\route::urlJump($message);
            }
        }
        else
        {
            \PHPEMSPRO\tpl::getInstance()->display('login_register');
        }
    }

    public function getopenid()
    {
        if($this->_user['sessionuserid'])
        {
            header("location:index.php");
            exit;
        }
        $openid = \PHPEMSPRO\wxpay::getInstance()->getwxopenid();
        $user = users::autoLoginWxUser($openid);
        if(!$user)
        {
            if(\PHPEMSPRO\config::wxlogin['autoreg'])
            {
                $wxuser = \PHPEMSPRO\wxpay::getInstance()->getUserInfo();
                $username = 'wx_'.uniqid();
                $defaultgroup = users::getDefaultGroup();
                $args = array(
                    'useropenid' => $openid,
                    'usertruename' => $wxuser['nickname'],
                    'username' => $username,
                    'userphone' => $username,
                    'useremail' => $username.EP,
                    'usergroupid' => $defaultgroup['groupid'],
                    'userpassword' => md5($username),
                    'userphoto' => $wxuser['headimgurl']
                );
                users::addUser($args);
                $user = users::autoLoginWxUser($openid);
            }
            else
            {
                $_SESSION['bindopenid'] = $openid;
                header("location:index.php?wxlogin-mobile-index-bindlogin");
                exit;
            }
        }
        header("location:index.php");
        exit;
    }

    public function index()
    {
        //
    }
}