<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */
namespace PHPEMSPRO\plugin\controller\master;

use PHPEMSPRO\plugin\master;
use PHPEMSPRO\plugin\model\plugin;
use PHPEMSPRO\route;

class plugins extends master
{
    public function off()
    {
        $plugin = route::get('plugin');
        plugin::getInstance()->modifyPlugin($plugin,['pluginstatus' => 0]);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function on()
    {
        $plugin = route::get('plugin');
        plugin::getInstance()->modifyPlugin($plugin,['pluginstatus' => 1]);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function index()
    {
        $plugins = plugin::getInstance()->getLocalPlugins();
        $actives = plugin::getInstance()->getActivePlugins();
        $plugins = $plugins['dir'];
        foreach($plugins as $key => $plugin)
        {
            $config = "\\PHPEMSPRO\\plugins\\{$plugin['name']}\\model\\config";
            $plugins[$key]['title'] = $config::$title;
            $plugins[$key]['describe'] = $config::$describe;
            $plugins[$key]['manageUrl'] = $config::$manageUrl;
            if(in_array($plugin['name'],$actives))$plugins[$key]['actived'] = 1;
        }
        \PHPEMSPRO\tpl::getInstance()->assign('plugins',$plugins);
        \PHPEMSPRO\tpl::getInstance()->display('plugins');
    }
}