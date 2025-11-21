<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\user\controller\app;

use PHPEMSPRO\exam\model\exams;
use PHPEMSPRO\exam\model\points;
use PHPEMSPRO\exam\model\training;
use PHPEMSPRO\user\app;

class exam extends app
{public function index()
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
        \PHPEMSPRO\tpl::getInstance()->display('exam');
    }
}