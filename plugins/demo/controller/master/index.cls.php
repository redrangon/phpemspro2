<?php

namespace PHPEMSPRO\plugins\demo\controller\master;

use PHPEMSPRO\tpl;

class index
{
    public $tpl;

    public function __construct()
    {
        $this->tpl = tpl::getInstance('plugin');
    }

    public function display()
    {
        $this->tpl->display('index');
    }
}