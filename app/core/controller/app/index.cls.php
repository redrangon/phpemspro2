<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\core\controller\app;

use PHPEMSPRO\content\model\content;
use PHPEMSPRO\core\app;
use PHPEMSPRO\core\model\category;
use PHPEMSPRO\exam\model\training;
use PHPEMSPRO\lesson\model\lessons;

class index extends app
{
    public function index()
    {
        $catindex = array(1,2,3);
        $contents = array();
        foreach($catindex as $catid)
        {
            $catstring = \PHPEMSPRO\core\model\category::getChildCategoryString('content',$catid);
            $args = array(
                array("AND","find_in_set(contentcatid,:contentcatid)","contentcatid",$catstring)
            );
            $contents[$catid] = \PHPEMSPRO\content\model\content::getContentList($args,1,1);
        }
        $trainings = training::getTrainingsByArgs();
        $lessons = lessons::getLessonList(array(),1,5);

        $app = \PHPEMSPRO\core\model\apps::getAppByCode('content');
        $pccatstring = category::getChildCategoryString('content',$app['appsetting']['carousel']['pc']);
        $pcargs = array(
            array("AND","find_in_set(contentcatid,:contentcatid)","contentcatid",$pccatstring)
        );
        $carousels = content::getContentList($pcargs,1,5);

        \PHPEMSPRO\tpl::getInstance()->assign('lessons',$lessons);
        \PHPEMSPRO\tpl::getInstance()->assign('contents',$contents);
        \PHPEMSPRO\tpl::getInstance()->assign('carousels',$carousels);
        \PHPEMSPRO\tpl::getInstance()->assign('trainings',$trainings);
        \PHPEMSPRO\tpl::getInstance()->display('index');
    }
}