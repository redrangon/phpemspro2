<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 13:42
 */
namespace PHPEMSPRO;

class base
{
    static $systime = NULL;

    static function autoLoadClass($class)
    {
        $class = explode('\\',$class);
        if($class[0] == 'PHPEMSPRO')
        {
            if(count($class) == 2)
            {
                $class = $class[1];
                $path = PEP_PATH.'/public/lib/'.$class.'.cls.php';
            }
            elseif($class[1] == 'plugins')
            {
                unset($class[0]);
                $class = implode('/',$class);
                $path = PEP_PATH.'/'.$class.'.cls.php';
            }
            else
            {
                unset($class[0]);
                $class = implode('/',$class);
                $path = PEP_PATH.'/app/'.$class.'.cls.php';
            }
            if($path && file_exists($path))
            {
                include $path;
            }
        }
    }

    static function loadVendor()
    {
        $path = PEP_PATH."/public/vendor/vendor/autoload.php";
        if(file_exists($path))
        {
            include_once $path;
        }
    }

    static function run()
    {
        spl_autoload_register([self::class, 'autoLoadClass']);
        ini_set('date.timezone',config::systimezone);
        header('P3P: CP=CAO PSA OUR; Content-Type: text/html; charset=utf-8');
        self::$systime = time();
        self::loadVendor();
        $app = route::getUrl()?route::getUrl():config::defaultapp;
        $module = route::getUrl('module')?route::getUrl('module'):'app';
        $method = route::getUrl('method')?route::getUrl('method'):'index';
        $className = '\\PHPEMSPRO\\'.$app.'\\controller\\'.$module.'\\'.$method;
        if(class_exists($className))
        {
            $app = new $className();
            tpl::getInstance()->assign('_style',style::loadStyle());
            tpl::getInstance()->assign('_route',route::$url);
            tpl::getInstance()->assign('_userhash',route::get('userhash'));
            $app->display();
        }
        elseif($app == 'plugins')
        {
            $selection = route::getUrl('selection')?route::getUrl('selection'):'index';
            $className = '\\PHPEMSPRO\\plugins\\'.$module.'\\controller\\'.$method.'\\'.$selection;
            $app = new $className();
            $app->display();
        }
    }
}