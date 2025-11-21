<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\content\controller\app;

use PHPEMSPRO\content\app;
use PHPEMSPRO\content\model\content;
use PHPEMSPRO\exam\model\exams;
use PHPEMSPRO\exam\model\points;
use PHPEMSPRO\exam\model\training;

class category extends app
{public function index()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 1?$page:1;
        $catid = \PHPEMSPRO\route::get('catid');
        $cat = \PHPEMSPRO\core\model\category::getCategoryById($catid);
        $catchildren = \PHPEMSPRO\core\model\category::getChildCategory('content',$catid);
        $catbrother = \PHPEMSPRO\core\model\category::getChildCategory('content',$cat['catparent']);
        $catstring = \PHPEMSPRO\core\model\category::getChildCategoryString('content',$catid);
        $args = array(
            array("AND","find_in_set(contentcatid,:contentcatid)","contentcatid",$catstring)
        );
        $contents = content::getContentList($args,$page);
        \PHPEMSPRO\tpl::getInstance()->assign('contents',$contents);
        \PHPEMSPRO\tpl::getInstance()->assign('catbrother',$catbrother);
        \PHPEMSPRO\tpl::getInstance()->assign('catchildren',$catchildren);
        \PHPEMSPRO\tpl::getInstance()->assign('page',$page);
        \PHPEMSPRO\tpl::getInstance()->assign('cat',$cat);
        if(!$cat['cattpl'])
        {
            $cat['cattpl'] = 'category_default';
        }
        \PHPEMSPRO\tpl::getInstance()->display($cat['cattpl']);
    }
}