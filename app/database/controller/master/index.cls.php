<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\database\controller\master;

use PHPEMSPRO\database\master;

class index extends master
{public function index()
    {
        \PHPEMSPRO\tpl::getInstance()->display('index');
    }
}