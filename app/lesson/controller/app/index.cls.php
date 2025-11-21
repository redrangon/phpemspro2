<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\lesson\controller\app;

use PHPEMSPRO\core\model\category;
use PHPEMSPRO\finance\model\orders;
use PHPEMSPRO\lesson\app;
use PHPEMSPRO\lesson\model\lessons;

class index extends app
{
	public function index()
    {
        $lessons = array();
        $categories = category::getChildCategory('lesson',0);
        foreach($categories as $cat)
        {
            $catstring = category::getChildCategoryString('lesson',$cat['catid']);
            $args = array();
            $args[] = array("AND","find_in_set(lessoncatid,:lessoncatid)","lessoncatid",$catstring);
            $lessons[$cat['catid']] = lessons::getLessonList($args,1,4);
        }
        \PHPEMSPRO\tpl::getInstance()->assign('categories',$categories);
        \PHPEMSPRO\tpl::getInstance()->assign('lessons',$lessons);
        \PHPEMSPRO\tpl::getInstance()->display('index');
    }
}