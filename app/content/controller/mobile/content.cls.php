<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\content\controller\mobile;

use PHPEMSPRO\content\mobile;
use PHPEMSPRO\exam\model\exams;
use PHPEMSPRO\exam\model\points;
use PHPEMSPRO\exam\model\training;

class content extends mobile
{public function index()
    {
        $contentid = \PHPEMSPRO\route::get('contentid');
        $content = \PHPEMSPRO\content\model\content::getContentById($contentid);
        $cat = \PHPEMSPRO\core\model\category::getCategoryById($content['contentcatid']);
        \PHPEMSPRO\tpl::getInstance()->assign('cat',$cat);
        \PHPEMSPRO\tpl::getInstance()->assign('content',$content);
        \PHPEMSPRO\tpl::getInstance()->display('content_default');
    }
}