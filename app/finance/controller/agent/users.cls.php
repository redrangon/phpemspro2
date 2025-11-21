<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\finance\controller\agent;

use PHPEMSPRO\exam\model\favor;
use PHPEMSPRO\exam\model\question;
use PHPEMSPRO\finance\agent;
use PHPEMSPRO\exam\model\exams;
use PHPEMSPRO\exam\model\points;
use PHPEMSPRO\exam\model\training;

class users extends agent
{
    protected function _start()
    {
        //
    }

    public function schedule()
    {
        $page = \PHPEMSPRO\route::get('page');
        $username = \PHPEMSPRO\route::get('username');
        $subjectid = \PHPEMSPRO\route::get('subjectid');
        $basicid = \PHPEMSPRO\route::get('basicid');
        $subject = points::getSubjectById($subjectid);
        $basic = exams::getBasicById($subject['subjectdb'],$basicid);
        $points = points::getPointsBySubjectid($subject['subjectid']);
        $sections = $points['sections'];
        $points = $points['points'];
        $record = favor::getRecordSession($subject['subjectdb'],$username);
        $wrong = array();
        $right = array();
        $numbers = array();
        $donenumber = array();
        foreach($points as $key => $point)
        {
            $rt = 0;
            $wg = 0;
            foreach($point as $p)
            {
                if($basic['basicpoints'][$key][$p['pointid']])
                {
                    $numbers[$p['pointid']] = question::getQuestionNumberByPointid($subject['subjectdb'],$p['pointid']);
                    $right[$p['pointid']] = intval($record['recordnumber'][$p['pointid']]['right']);
                    $wrong[$p['pointid']] = intval($record['recordnumber'][$p['pointid']]['wrong']);
                    $donenumber[$p['pointid']] = $record['recordnumber'][$p['pointid']]['right'] + $record['recordnumber'][$p['pointid']]['wrong'];
                }
            }
        }
        unset($record);
        \PHPEMSPRO\tpl::getInstance()->assign('sections',$sections);
        \PHPEMSPRO\tpl::getInstance()->assign('allnumber',array('right' => array_sum($right),'wrong' => array_sum($wrong),'all' => array_sum($numbers)));
        \PHPEMSPRO\tpl::getInstance()->assign('donenumber',$donenumber);
        \PHPEMSPRO\tpl::getInstance()->assign('numbers',$numbers);
        \PHPEMSPRO\tpl::getInstance()->assign('username',$username);
        \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
        \PHPEMSPRO\tpl::getInstance()->assign('basic',$basic);
        \PHPEMSPRO\tpl::getInstance()->assign('right',$right);
        \PHPEMSPRO\tpl::getInstance()->assign('wrong',$wrong);
        \PHPEMSPRO\tpl::getInstance()->assign('points',$points);
        \PHPEMSPRO\tpl::getInstance()->display('users_schedule');
    }

    public function history()
    {
        $page = \PHPEMSPRO\route::get('page');
        $username = \PHPEMSPRO\route::get('username');
        $subjectid = \PHPEMSPRO\route::get('subjectid');
        $basicid = \PHPEMSPRO\route::get('basicid');
        $subject = points::getSubjectById($subjectid);
        $basic = exams::getBasicById($subject['subjectdb'],$basicid);
        $page = $page?$page:1;
        $args = array();
        $args[] = array("AND","ehbasicid = :basicid","basicid",$basicid);
        $args[] = array("AND","ehusername = :username","username",$username);
        $histories = favor::getExamHistoryList($subject['subjectdb'],$args,$page);
        \PHPEMSPRO\tpl::getInstance()->assign('username',$username);
        \PHPEMSPRO\tpl::getInstance()->assign('basic',$basic);
        \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
        \PHPEMSPRO\tpl::getInstance()->assign('histories',$histories);
        \PHPEMSPRO\tpl::getInstance()->assign('page',$page);
        \PHPEMSPRO\tpl::getInstance()->display('users_history');
    }

    public function status()
    {
        $username = \PHPEMSPRO\route::get('username');
        $trainings = training::getTrainingsByArgs();
        $usetraining = array();
        $subjects = array();
        $basics = array();
        $status = array();
        foreach($trainings as $training)
        {
            $i = 0;
            $subjects[$training['trid']] = points::getSubjects(array(array("AND","subjecttrid = :trid","trid",$training['trid'])));
            foreach($subjects[$training['trid']] as $subject)
            {
                $basics[$subject['subjectid']] = exams::getOpenBasicsByUserName($subject['subjectdb'],$subject['subjectid'],$username);
                if($basics[$subject['subjectid']])
                {
                    $i++;
                    foreach($basics[$subject['subjectid']] as $basic)
                    {
                        $lastquestion = json_decode(\PHPEMSPRO\pedis::getInstance()->getHashData('lastquestion',$username.'-'.$subject['subjectdb'].'-'.$basic['basicid']),true);
                        $pointid = key($lastquestion);
                        if($pointid)
                        {
                            $index = intval(\PHPEMSPRO\pedis::getInstance()->getHashData('process',$username.'-'.$subject['subjectdb'].'-'.$pointid));
                            $point = points::getPointById($pointid);
                            $status[$subject['subjectid']][$basic['basicid']] = array(
                                'point' => $point['pointname'],
                                'index' => $index
                            );
                        }
                    }
                }
            }
            if($i)
            {
                $usetraining[$training['trid']] = 1;
            }
        }
        \PHPEMSPRO\tpl::getInstance()->assign('status',$status);
        \PHPEMSPRO\tpl::getInstance()->assign('subjects',$subjects);
        \PHPEMSPRO\tpl::getInstance()->assign('trainings',$trainings);
        \PHPEMSPRO\tpl::getInstance()->assign('username',$username);
        \PHPEMSPRO\tpl::getInstance()->assign('basics',$basics);
        \PHPEMSPRO\tpl::getInstance()->assign('usetraining',$usetraining);
        \PHPEMSPRO\tpl::getInstance()->display('users_status');
    }

    public function index()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 1?$page:1;
        $search = \PHPEMSPRO\route::get('search');
        $args = array();
        $args[] = array("AND","orderagent = :orderagent","orderagent",$this->_user['username']);
        //$args[] = array("AND","orders.orderstatus = 2");
        //$args[] = array("AND","ordertime >= :stime","stime",TIME - 365*240*3600);
        if($search['userphone'])
        {
            $args[] = array("AND","orderusername = :orderusername","orderusername",$search['userphone']);
        }
        $users = \PHPEMSPRO\finance\model\orders::getAgentUsersList($args,$page);
        \PHPEMSPRO\tpl::getInstance()->assign('users',$users);
        \PHPEMSPRO\tpl::getInstance()->display('users');
    }
}