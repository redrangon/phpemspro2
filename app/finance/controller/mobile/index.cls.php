<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\finance\controller\mobile;

use PHPEMSPRO\finance\mobile;

class index extends mobile
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