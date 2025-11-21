<?php
/*
 * Created on 2013-12-26
 * 用于定时任务调起
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

set_time_limit(0);
define('PEP_PATH',dirname(dirname(__FILE__)));
include PEP_PATH.'/public/lib/base.cls.php';
class app
{
    static function getBasicMembers($dbid,$args)
    {
        $data = array(
            'select' => 'obusername',
            'table' => 'openbasics',
            'query' => $args,
            'limit' => false
        );
        return \pepdo::getInstance($dbid)->getElements($data);
    }

    static function getBasicsByArgs($dbid,$args)
    {
        $data = array(
            'table' => 'basics',
            'query' => $args,
            'limit' => false
        );
        return \pepdo::getInstance($dbid)->getElements($data);
    }

    static function setMemberProcess($dbid,$args)
    {
        $query = array(
            array("AND","obsusername = :obsusername","obsusername",$args['obsusername']),
            array("AND","obspointid = :obspointid","obspointid",$args['obspointid'])
        );
        $data = array(
            'table' => 'obprocess',
            'query' => $query
        );
        $r = \pepdo::getInstance($dbid)->getElement($data);
        if($r)
        {
            $data = array(
                'table' => 'obprocess',
                'value' => $args,
                'query' => array(
                    array("AND","obsid = :obsid","obsid",$r['obsid'])
                )
            );
            return \pepdo::getInstance($dbid)->updateElement($data);
        }
        else
        {
            $data = array(
                'table' => 'obprocess',
                'query' => $args
            );
            return \pepdo::getInstance($dbid)->insertElement($data);
        }
    }

    static function display()
    {
        $subjects = \exam\model\points::getSubjects();
        foreach($subjects as $subject)
        {
            $basics = self::getBasicsByArgs($subject['subjectdb'],array(array("AND","basicsubject = :basicsubject","basicsubject",$subject['subjectid'])));
            $unames = array();
            foreach($basics as $basic)
            {
                $members = self::getBasicMembers($subject['subjectdb'],array(array("AND","obbasicid = :obbasicid","obbasicid",$basic['basicid'])));

                foreach($members as $member)
                {
                    $unames[] = $member['obusername'];
                }
                unset($members);
                $unames = array_unique($unames);
            }
            $unames = array_unique($unames);
            $points = \exam\model\points::getPointsBySubjectid($subject['subjectid']);
            foreach($unames as $uname)
            {
                $record = \exam\model\favor::getRecordSession($subject['subjectdb'],$uname);
                foreach($points['points'] as $section)
                {
                    foreach($section as $point)
                    {
                        $args = array(
                            'obsusername' =>  $uname,
                            'obspointid' => $point['pointid'],
                            'obstime' => TIME,
                            'obsnumber' => \exam\model\question::getQuestionNumberByPointid($subject['subjectdb'],$point['pointid']),
                            'obsright' => intval($record['recordnumber'][$point['pointid']]['right']),
                            'obswrong' => intval($record['recordnumber'][$point['pointid']]['wrong']),
                            'obsdone' => intval($record['recordnumber'][$point['pointid']]['right']) + intval($record['recordnumber'][$point['pointid']]['wrong'])
                        );
                        self::setMemberProcess($subject['subjectdb'],$args);
                    }
                }
            }
        }
        echo 'Process fill finished!';
    }
}
app::display();