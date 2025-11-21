<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\exam\controller\mobile;

use PHPEMSPRO\exam\mobile;
use PHPEMSPRO\exam\model\exams;
use PHPEMSPRO\exam\model\points;
use PHPEMSPRO\exam\model\training;
use PHPEMSPRO\finance\model\orders;

class index extends mobile
{public function active()
    {
        if((TIME - $_SESSION['onlytime']) < 1)
        {
            $message = array(
                'statusCode' => 300,
                "message" => "请勿多次提交"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        $_SESSION['onlytime'] = TIME;
        $activeid = \PHPEMSPRO\route::get('activeid');
        $args = array();
        $args[] = array("AND","activeid = :activeid","activeid",$activeid);
        $args[] = array("AND","activeusername = :activeusername","activeusername",$this->_user['username']);
        $args[] = array("AND","activestatus = 0");
        $active = orders::getActiveByArgs($args);
        if($active['activeid'])
        {
            $subject = points::getSubjectById($active['activesubjectid']);
            if($subject)
            {
                $basic = exams::getBasicById($subject['subjectdb'],$active['activebasicid']);
                if($basic)
                {
                    $r = \PHPEMSPRO\exam\model\exams::getIsMember($subject['subjectdb'],$this->_user['username'],$basic['basicid']);
                    if(!$r)
                    {
                        $args = array(
                            'obbasicid' => $basic['basicid'],
                            'obusername' => $this->_user['username'],
                            'obtime' => TIME,
                            'obendtime' => TIME + 24*3600*$active['activetime']
                        );
                        \PHPEMSPRO\exam\model\exams::addBasicMember($subject['subjectdb'],$args);
                    }
                    elseif(strtotime($r['obendtime']) < TIME)
                    {
                        $args = array(
                            'obendtime' => TIME + 24*3600*$active['activetime']
                        );
                        \PHPEMSPRO\exam\model\exams::modifyBasicMember($subject['subjectdb'],$r['obid'],$args);
                    }
                    elseif(strtotime($r['obendtime']) >= TIME)
                    {
                        $args = array(
                            'obendtime' => strtotime($r['obendtime']) + 24*3600*$active['activetime']
                        );
                        \PHPEMSPRO\exam\model\exams::modifyBasicMember($subject['subjectdb'],$r['obid'],$args);
                    }
                    orders::modifyActive($active['activeid'],array('activestatus' => 1));
                    \PHPEMSPRO\session::getInstance()->setSessionUser(array('currentsubject' => $subject['subjectid'],'currentbasic' => $basic['basicid']));
                    $message = array(
                        'statusCode' => 200,
                        "message" => "激活成功",
                        "callbackType" => "forward",
                        "forwardUrl" => "index.php?exam-mobile-basic"
                    );
                    exit(json_encode($message));
                }
            }
        }
        $message = array(
            'statusCode' => 300,
            "message" => "激活失败，请确认是否已经激活过，更多问题请询问管理员"
        );
        exit(json_encode($message));
    }

    public function index()
    {
        $trainings = training::getTrainingsByArgs();
        $usetraining = array();
        $subjects = array();
        $basics = array();
        foreach($trainings as $training)
        {
            $i = 0;
            $subjects[$training['trid']] = points::getSubjects(array(array("AND","subjecttrid = :trid","trid",$training['trid'])));
            foreach($subjects[$training['trid']] as $subject)
            {
                $basics[$subject['subjectid']] = exams::getOpenBasicsByUserName($subject['subjectdb'],$subject['subjectid'],$this->_user['username']);
                if($basics[$subject['subjectid']])$i++;
            }
            if($i)
            {
                $usetraining[$training['trid']] = 1;
            }
        }
        \PHPEMSPRO\tpl::getInstance()->assign('subjects',$subjects);
        \PHPEMSPRO\tpl::getInstance()->assign('trainings',$trainings);
        \PHPEMSPRO\tpl::getInstance()->assign('basics',$basics);
        \PHPEMSPRO\tpl::getInstance()->assign('usetraining',$usetraining);
        \PHPEMSPRO\tpl::getInstance()->display('index');
    }
}