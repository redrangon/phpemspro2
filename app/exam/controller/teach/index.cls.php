<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\exam\controller\teach;

use PHPEMSPRO\exam\teach;

class index extends teach
{public function index()
    {
        \PHPEMSPRO\tpl::getInstance()->display('index');
    }
}