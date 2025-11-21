<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\lesson\controller\mobile;

use PHPEMSPRO\finance\model\orders;
use PHPEMSPRO\lesson\mobile;
use PHPEMSPRO\lesson\model\lessons;

class index extends mobile
{public function index()
    {
        $lessons = lessons::getOpenLessons($this->_user['username']);
        \PHPEMSPRO\tpl::getInstance()->assign('lessons',$lessons);
        \PHPEMSPRO\tpl::getInstance()->display('index');
    }
}