<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\lesson\controller\master;

use PHPEMSPRO\lesson\master;

class index extends master
{
    public function index()
    {
        \PHPEMSPRO\tpl::getInstance()->display('index');
    }
}