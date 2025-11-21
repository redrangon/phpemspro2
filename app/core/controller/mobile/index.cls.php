<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\core\controller\mobile;

use PHPEMSPRO\core\mobile;
use PHPEMSPRO\content\model\content;
use PHPEMSPRO\core\model\category;
use PHPEMSPRO\exam\model\exams;
use PHPEMSPRO\exam\model\points;
use PHPEMSPRO\exam\model\training;
use PHPEMSPRO\finance\model\orders;
use PHPEMSPRO\lesson\model\lessons;

class index extends mobile
{
    public function index()
    {
        $catindex = array(1,2,3);
        $contents = array();
        foreach($catindex as $catid)
        {
            $catstring = category::getChildCategoryString('content',$catid);
            $args = array(
                array("AND","find_in_set(contentcatid,:contentcatid)","contentcatid",$catstring)
            );
            $contents[$catid] = content::getContentList($args,1,5);
        }
        $trainings = training::getTrainingsByArgs();
        $subjects = array();
        $basics = array();
        foreach($trainings as $training)
        {
            $subjects[$training['trid']] = points::getSubjects(array(array("AND","subjecttrid = :trid","trid",$training['trid'])));
            foreach($subjects[$training['trid']] as $subject)
            {
                $basics[$subject['subjectid']] = exams::getBasicsList($subject['subjectdb'],array(array("AND","basicsubject = :basicsubject","basicsubject",$subject['subjectid'])),1,10);
                $openbasics[$subject['subjectid']] = exams::getOpenBasicsByUserName($subject['subjectdb'],$subject['subjectid'],$this->_user['username']);
            }
        }

        $lessoncats = \PHPEMSPRO\core\model\category::getCategoriesByArgs(array(array("AND","catparent = 0"),array("AND","catapp = 'lesson'")));

        $lessons = lessons::getLessonList(array(),1,5);

        $carousels = array();
        $app = \PHPEMSPRO\core\model\apps::getAppByCode('content');
        foreach($app['appsetting']['carousel'] as $key => $catid)
        {
            $mcatstring = category::getChildCategoryString('content',$catid);
            $margs = array(
                array("AND","find_in_set(contentcatid,:contentcatid)","contentcatid",$mcatstring)
            );
            $carousels[$key] = content::getContentList($margs,1,5);
        }
        \PHPEMSPRO\tpl::getInstance()->assign('lessoncats',$lessoncats);
        \PHPEMSPRO\tpl::getInstance()->assign('lessons',$lessons);
        \PHPEMSPRO\tpl::getInstance()->assign('contents',$contents);
        \PHPEMSPRO\tpl::getInstance()->assign('carousels',$carousels);
        \PHPEMSPRO\tpl::getInstance()->assign('subjects',$subjects);
        \PHPEMSPRO\tpl::getInstance()->assign('trainings',$trainings);
        \PHPEMSPRO\tpl::getInstance()->assign('openbasics',$openbasics);
        \PHPEMSPRO\tpl::getInstance()->assign('basics',$basics);
        \PHPEMSPRO\tpl::getInstance()->display('index');
    }
}