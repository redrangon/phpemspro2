<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\lesson\controller\app;

use PHPEMSPRO\exam\model\exams;
use PHPEMSPRO\exam\model\points;
use PHPEMSPRO\exam\model\training;
use PHPEMSPRO\lesson\app;
use PHPEMSPRO\lesson\model\lessons;

class category extends  app
{public function index()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page >1?$page:1;
        $catid = \PHPEMSPRO\route::get('catid');
        $cat = \PHPEMSPRO\core\model\category::getCategoryById($catid);
        $catchildren = \PHPEMSPRO\core\model\category::getChildCategory('lesson',$catid);
        $catbrother = \PHPEMSPRO\core\model\category::getChildCategory('lesson',$cat['catparent']);
        if(!count($catchildren))
        {
            $catstring = \PHPEMSPRO\core\model\category::getChildCategoryString('lesson',$catid);
            $args = array(
                array("AND","find_in_set(lessoncatid,:lessoncatid)","lessoncatid",$catstring)
            );
            $lessons = lessons::getLessonList($args,$page);
            \PHPEMSPRO\tpl::getInstance()->assign('lessons',$lessons);
        }
        \PHPEMSPRO\tpl::getInstance()->assign('catbrother',$catbrother);
        \PHPEMSPRO\tpl::getInstance()->assign('catchildren',$catchildren);
        \PHPEMSPRO\tpl::getInstance()->assign('cat',$cat);
        \PHPEMSPRO\tpl::getInstance()->assign('page',$page);
        if(!$cat['cattpl'])
        {
            $cat['cattpl'] = 'category_default';
        }
        \PHPEMSPRO\tpl::getInstance()->display($cat['cattpl']);
    }
}