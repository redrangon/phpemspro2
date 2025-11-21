<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\core\controller\master;

use PHPEMSPRO\core\master;

class index extends master
{
	public function index()
    {
        \PHPEMSPRO\tpl::getInstance()->display('index');
    }
}