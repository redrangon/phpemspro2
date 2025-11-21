<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2019/1/17
 * Time: 21:20
 */

namespace PHPEMSPRO\lesson\model;


class lessons
{
    static function getLessonById($id)
    {
        $args = array(array("AND","lessonid = :lessonid","lessonid",$id));
        $data = array(
            'select' => false,
            'table' => 'lessons',
            'query' => $args
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElement($data);
    }

    static function modifyLesson($id,$args)
    {
        $data = array(
            'table' => 'lessons',
            'value' => $args,
            'query' => array(
                array("AND","lessonid = :lessonid","lessonid",$id)
            )
        );
        \PHPEMSPRO\pepdo::getInstance()->updateElement($data);
    }

    //添加信息
    static function addLesson($args)
    {
        $data = array(
            'table' => 'lessons',
            'query' => $args
        );
        \PHPEMSPRO\pepdo::getInstance()->insertElement($data);
    }

    static function delLesson($id)
    {
        $data = array(
            'table' => 'lessons',
            'query' => array(
                array("AND","lessonid = :lessonid","lessonid",$id)
            )
        );
        return \PHPEMSPRO\pepdo::getInstance()->delElement($data);
    }

    static function getLessonList($args,$page,$number = \PHPEMSPRO\config::webpagenumber,$orderby = 'lessonid desc')
    {
        $data = array(
            'table' => 'lessons',
            'query' => $args,
            'orderby' => $orderby
        );
        return \PHPEMSPRO\pepdo::getInstance()->listElements($page,$number,$data);
    }

    static function getVideoList($args,$page,$number = \PHPEMSPRO\config::webpagenumber,$orderby = 'videoorder desc,videoid desc')
    {
        $data = array(
            'table' => 'videos',
            'query' => $args,
            'orderby' => $orderby
        );
        return \PHPEMSPRO\pepdo::getInstance()->listElements($page,$number,$data);
    }

    static function getVideosByArgs($args,$orderby = 'videoorder desc,videoid desc')
    {
        $data = array(
            'table' => 'videos',
            'query' => $args,
            'orderby' => $orderby
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElements($data);
    }

    static function getVideoById($id)
    {
        $args = array(array("AND","videoid = :videoid","videoid",$id));
        $data = array(
            'select' => false,
            'table' => 'videos',
            'query' => $args
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElement($data);
    }

    static function modifyVideo($id,$args)
    {
        $data = array(
            'table' => 'videos',
            'value' => $args,
            'query' => array(
                array("AND","videoid = :videoid","videoid",$id)
            )
        );
        \PHPEMSPRO\pepdo::getInstance()->updateElement($data);
    }

    //添加信息
    static function addVideo($args)
    {
        $data = array(
            'table' => 'videos',
            'query' => $args
        );
        \PHPEMSPRO\pepdo::getInstance()->insertElement($data);
    }

    static function delVideo($id)
    {
        $data = array(
            'table' => 'videos',
            'query' => array(
                array("AND","videoid = :videoid","videoid",$id)
            )
        );
        return \PHPEMSPRO\pepdo::getInstance()->delElement($data);
    }

    static function getVideosNumber($lessonid)
    {
        $args = array(array("AND","videolesson = :videolesson","videolesson",$lessonid));
        $data = array(
            'select' => "count(*) as number",
            'table' => 'videos',
            'query' => $args
        );
        $r = \PHPEMSPRO\pepdo::getInstance()->getElement($data);
        return $r['number'];
    }

    static function isLessonMember($username,$lessonid)
    {
        $data = array(
            'select' => false,
            'table' => 'openlessons',
            'query' => array(
                array("AND","oplusername = :oplusername","oplusername",$username),
                array("AND","opllessonid = :opllessonid","opllessonid",$lessonid)
            )
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElement($data);
    }

    static function getOpenLessons($username)
    {
        $args = array(array("AND","oplusername = :oplusername","oplusername",$username));
        $args[] = array("AND","opllessonid = lessonid");
        $data = array(
            'select' => false,
            'table' => array('openlessons','lessons'),
            'query' => $args,
            'limit' => false
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElements($data);
    }

    static function getLessonMemberList($args,$page,$number = \PHPEMSPRO\config::webpagenumber,$orderby = 'oplid desc')
    {
        $args[] = array("AND","oplusername = username");
        $data = array(
            'table' => array('openlessons','users'),
            'query' => $args,
            'orderby' => $orderby
        );
        return \PHPEMSPRO\pepdo::getInstance()->listElements($page,$number,$data);
    }

    static function delLessonMember($oplid)
    {
        $data = array(
            'table' => 'openlessons',
            'query' => array(
                array("AND","oplid = :oplid","oplid",$oplid)
            )
        );
        return \PHPEMSPRO\pepdo::getInstance()->delElement($data);
    }

    static function openLesson($username,$lessonid,$time)
    {
        $rs = self::isLessonMember($username,$lessonid);
        if($rs)
        {
            $rs['oplendtime'] = strtotime($rs['oplendtime']);
            if($rs['oplendtime'] >= TIME)
            {
                $endtime = $rs['oplendtime'] + $time * 3600 *24;
            }
            else
            {
                $endtime = TIME + $time * 3600 *24;
            }
            $data = array(
                'table' => 'openlessons',
                'value' => array(
                    'oplendtime' => $endtime
                ),
                'query' => array(
                    array("AND","oplid = :oplid","oplid",$rs['oplid'])
                )
            );
            \PHPEMSPRO\pepdo::getInstance()->updateElement($data);
            return 1;
        }
        else
        {
            $data = array(
                'table' => 'openlessons',
                'query' => array(
                    'oplusername' => $username,
                    'opllessonid' => $lessonid,
                    'opltime' => TIME,
                    'oplendtime' => TIME + $time * 3600 *24
                )
            );
            \PHPEMSPRO\pepdo::getInstance()->insertElement($data);
            return 0;
        }
    }

    static function getLessonRecordById($recid)
    {
        $args = array(array("AND","recid = :recid","recid",$recid));
        $data = array(
            'select' => false,
            'table' => 'lesson_records',
            'query' => $args,
            'limit' => false
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElement($data);
    }

    static function getLessonRecordByArgs($args = array())
    {
        $data = array(
            'select' => false,
            'table' => 'lesson_records',
            'query' => $args,
            'limit' => 1
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElement($data);
    }

    static function getLessonRecordsByArgs($args = array(),$index = false)
    {
        $data = array(
            'select' => false,
            'table' => 'lesson_records',
            'query' => $args,
            'index' => $index,
            'limit' => false
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElements($data);
    }

    static function addLessonRecord($args = array())
    {
        $data = array(
            'table' => 'lesson_records',
            'query' => $args
        );
        \PHPEMSPRO\pepdo::getInstance()->insertElement($data);
    }

    static function modifyLessonRecord($recid,$args = array())
    {
        $data = array(
            'table' => 'lesson_records',
            'value' => $args,
            'query' => array(
                array("AND","recid = :recid","recid",$recid)
            )
        );
        \PHPEMSPRO\pepdo::getInstance()->updateElement($data);
    }

    static function delLessonRecord($recid)
    {
        $data = array(
            'table' => 'lesson_records',
            'query' => array(
                array("AND","recid = :recid","recid",$recid)
            )
        );
        return \PHPEMSPRO\pepdo::getInstance()->delElement($data);
    }

    static function getLessonRecordsList($args = array(),$page = 1,$number = \PHPEMSPRO\config::webpagenumber,$orderby = 'recid desc',$index = false)
    {
        $data = array(
            'table' => 'lesson_records',
            'query' => $args,
            'orderby' => $orderby,
            'index' => $index
        );
        return \PHPEMSPRO\pepdo::getInstance()->listElements($page,$number,$data);
    }

    static function getVideoProgressList($args = array(),$page = 1,$number = \PHPEMSPRO\config::webpagenumber,$orderby = 'userid desc')
    {
        $args[] = array("AND","videoid = recvideoid");
        $args[] = array("AND","recusername = username");
        $data = array(
            'table' => array('lesson_records','videos','users'),
            'query' => $args,
            'orderby' => $orderby
        );
        return \PHPEMSPRO\pepdo::getInstance()->listElements($page,$number,$data);
    }

    static function getVideoProgresses($args = array(),$orderby = 'userid desc')
    {
        $args[] = array("AND","videoid = recvideoid");
        $args[] = array("AND","recusername = username");
        $data = array(
            'table' => array('lesson_records','videos','users'),
            'query' => $args,
            'orderby' => $orderby,
            'limit' => false
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElements($data);
    }

}