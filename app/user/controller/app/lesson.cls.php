<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\user\controller\app;

use PHPEMSPRO\lesson\model\lessons;
use PHPEMSPRO\pedis;
use PHPEMSPRO\route;
use PHPEMSPRO\user\app;

class lesson extends app
{
    public function progress()
    {
        $lessonid = route::get('lessonid');
        $username = $this->_user['username'];
        $lesson = lessons::getLessonById($lessonid);
        $args = array(
            array("AND","videolesson = :videolesson","videolesson",$lessonid)
        );
        $videos = lessons::getVideosByArgs($args);
        $ids = array();
        foreach($videos as $key => $v)
        {
            $rate = pedis::getInstance()->getHashData('lessonrecord',$username.'-'.$v['videoid']);
            if($rate)
            {
                $videos[$key]['rate'] = $rate;
            }
            $ids[] = $v['videoid'];
        }
        $args = array(
            array("AND","recusername = :recusername","recusername",$username),
            array("AND","recvideoid IN (:recvideoid)","recvideoid",$ids)
        );
        $records = lessons::getLessonRecordsByArgs($args,'recvideoid');
        \PHPEMSPRO\tpl::getInstance()->assign('records',$records);
        \PHPEMSPRO\tpl::getInstance()->assign('videos',$videos);
        \PHPEMSPRO\tpl::getInstance()->assign('lesson',$lesson);
        \PHPEMSPRO\tpl::getInstance()->display('lesson_progress');
    }

    public function index()
    {
        $lessons = lessons::getOpenLessons($this->_user['username']);
        \PHPEMSPRO\tpl::getInstance()->assign('lessons',$lessons);
        \PHPEMSPRO\tpl::getInstance()->display('lesson');
    }
}