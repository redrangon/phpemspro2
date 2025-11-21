<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\user\controller\app;

use PHPEMSPRO\user\app;

class index extends app
{
    protected function _start()
    {
        $this->_user = \PHPEMSPRO\session::getInstance()->getSessionUser();
        if(!$this->_user['sessionuserid'])
        {
            $message = array(
                'statusCode' => 300,
                "message" => "请您先登陆",
                "callbackType" => "forward",
                "forwardUrl" => "index.php?user-app-login"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
    }

    public function index()
    {
		header("location:index.php?user-app-user");
		exit;
    }
}