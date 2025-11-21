<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\user\controller\mobile;

use PHPEMSPRO\user\mobile;

class index extends mobile
{public function index()
    {
        $trainings = training::getTrainingsByArgs();
        $subjects = array();
        $basics = array();
        foreach($trainings as $training)
        {
            $subjects[$training['trid']] = points::getSubjects(array(array("AND","subjecttrid = :trid","trid",$training['trid'])));
            foreach($subjects[$training['trid']] as $subject)
            {
                $basics[$subject['subjectid']] = exams::getBasicsList($subject['subjectdb'],array(array("AND","basicsubject = :basicsubject","basicsubject",$subject['subjectid'])),1,2);
            }
        }
        \PHPEMSPRO\tpl::getInstance()->assign('subjects',$subjects);
        \PHPEMSPRO\tpl::getInstance()->assign('trainings',$trainings);
        \PHPEMSPRO\tpl::getInstance()->assign('basics',$basics);
        \PHPEMSPRO\tpl::getInstance()->display('index');
    }
}