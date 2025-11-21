<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\content\controller\app;

use PHPEMSPRO\content\app;
use PHPEMSPRO\exam\model\exams;
use PHPEMSPRO\exam\model\points;
use PHPEMSPRO\exam\model\training;

class content extends app
{public function index()
    {
        $contentid = \PHPEMSPRO\route::get('contentid');
        $content = \PHPEMSPRO\content\model\content::getContentById($contentid);
        $cat = \PHPEMSPRO\core\model\category::getCategoryById($content['contentcatid']);
        $catchildren = \PHPEMSPRO\core\model\category::getChildCategory('content',$cat['catid']);
        $catbrother = \PHPEMSPRO\core\model\category::getChildCategory('content',$cat['catparent']);
        \PHPEMSPRO\tpl::getInstance()->assign('cat',$cat);
        \PHPEMSPRO\tpl::getInstance()->assign('catbrother',$catbrother);
        \PHPEMSPRO\tpl::getInstance()->assign('catchildren',$catchildren);
        \PHPEMSPRO\tpl::getInstance()->assign('content',$content);
        \PHPEMSPRO\tpl::getInstance()->display('content_default');
    }
}