<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\finance\controller\agent;

use PHPEMSPRO\finance\agent;

class index extends agent
{
    protected function _start()
    {
        //
    }

    public function index()
    {
        \PHPEMSPRO\tpl::getInstance()->display('index');
    }
}