<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\user\controller\mobile;

use PHPEMSPRO\exam\mobile;

class user extends mobile
{public function wxpay()
    {
        $ordersn = \PHPEMSPRO\route::get('ordersn');
        $order = \PHPEMSPRO\finance\model\orders::getOrderBySn($ordersn);
        if($order['orderstatus'] == 1)
        {
            $jsApiParameters = \PHPEMSPRO\wxpay::getInstance()->outJsPay($order);
            \PHPEMSPRO\tpl::getInstance()->assign('jsApiParameters',$jsApiParameters);
        }
        \PHPEMSPRO\tpl::getInstance()->assign('order',$order);
        \PHPEMSPRO\tpl::getInstance()->display('wxpay');
    }

    public function password()
    {
        if(\PHPEMSPRO\route::get('modifypassword'))
        {
            $oldpassword = \PHPEMSPRO\route::get('oldpassword');
            $newpassword = \PHPEMSPRO\route::get('newpassword');
            $userid = $this->_user['userid'];
            $user = \PHPEMSPRO\user\model\users::getUserById($userid);
            if(md5($oldpassword) != $user['userpassword'])
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "操作失败，原密码验证失败"
                );
                \PHPEMSPRO\route::urlJump($message);
            }
            if($userid)
            {
                $id = \PHPEMSPRO\user\model\users::modifyUser($userid,array('userpassword' => $newpassword));
                $message = array(
                    'statusCode' => 200,
                    "message" => "操作成功",
                    "callbackType" => 'forward',
                    "forwardUrl" => "index.php?user-mobile-login-logout"
                );
                \PHPEMSPRO\route::urlJump($message);
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
            \PHPEMSPRO\tpl::getInstance()->display('user_password');
        }
    }

    public function index()
    {
        $user = \PHPEMSPRO\user\model\users::getUserById($this->_user['userid']);
        $group = \PHPEMSPRO\user\model\users::getGroupByCode($user['usergroupcode']);
        $modelcode = $group['groupmodel'];
        if(\PHPEMSPRO\route::get('modifyuser'))
        {
            $args = \PHPEMSPRO\route::get('args');
            if(!$args['userpassword'])unset($args['userpassword']);
            $properties = \PHPEMSPRO\database\model\model::getAllowPropertiesByModelcode($modelcode);
            $args = \PHPEMSPRO\database\model\model::callModelFieldsFilter($args,$properties);
            \PHPEMSPRO\user\model\users::modifyUser($user['userid'],$args);
            $message = array(
                'statusCode' => 200,
                "message" => "修改成功"
            );
            exit(json_encode($message));
        }
        else
        {
            $properties = \PHPEMSPRO\database\model\model::getAllowPropertiesByModelcode($modelcode);
            unset($user['userpassword']);
            $forms = \PHPEMSPRO\html::buildForm($properties,$user);
            \PHPEMSPRO\tpl::getInstance()->assign('user',$user);
            \PHPEMSPRO\tpl::getInstance()->assign('forms',$forms);
            \PHPEMSPRO\tpl::getInstance()->display('user');
        }
    }
}