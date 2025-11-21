<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/11/25
 * Time: 21:24
 */

namespace PHPEMSPRO\user\controller\master;

use PHPEMSPRO\user\master;

class setting extends master
{
    public function index()
    {
        $app = \PHPEMSPRO\core\model\apps::getAppByCode('user');
        if(\PHPEMSPRO\route::get('setting'))
        {
            $args = \PHPEMSPRO\route::get('args');
            \PHPEMSPRO\core\model\apps::modifyAppByCode('user',array('appsetting' => $args));
            $message = array(
                'statusCode' => 200,
                "message" => "删除成功",
                "callbackType" => "forward",
                "forwardUrl" => "reload"
            );
            exit(json_encode($message));
        }
        else
        {
            \PHPEMSPRO\tpl::getInstance()->assign('setting',$app['appsetting']);
            \PHPEMSPRO\tpl::getInstance()->display('setting');
        }
    }
}