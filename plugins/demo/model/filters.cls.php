<?php

namespace PHPEMSPRO\plugins\demo\model;

use PHPEMSPRO\plugin\model\plugin;
use PHPEMSPRO\user\model\users;

class filters
{
    static $instance;

    static function getInstance()
    {
        if(self::$instance == NULL)
        {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function register()
    {
        plugin::getInstance()->registerFilter('afterGetArticle',array($this,'afterGetArticle'),10);
        plugin::getInstance()->registerFilter('afterLoadPcCss',array($this,'afterLoadPcCss'),10);
    }

    public function afterGetArticle($content)
    {
        return $content;
    }

    public function afterLoadPcCss($css)
    {
        return $css;
    }
}