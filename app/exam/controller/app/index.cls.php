<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\exam\controller\app;

use PHPEMSPRO\exam\app;
use PHPEMSPRO\exam\model\exams;
use PHPEMSPRO\exam\model\points;
use PHPEMSPRO\exam\model\training;

class index extends app
{
	public function intro()
    {
        $trid = \PHPEMSPRO\route::get('trid');
        $training = training::getTrainingById($trid);
        \PHPEMSPRO\tpl::getInstance()->assign('training',$training);
        \PHPEMSPRO\tpl::getInstance()->display('training_intro');
    }

    public function training()
    {
        $trid = \PHPEMSPRO\route::get('trid');
        $training = training::getTrainingById($trid);
        $subjects = points::getSubjects(array(array("AND","subjecttrid = :trid","trid",$training['trid'])));
        foreach($subjects as $subject)
        {
            $basics[$subject['subjectid']] = exams::getBasicsList($subject['subjectdb'],array(array("AND","basicsubject = :basicsubject","basicsubject",$subject['subjectid'])),1,5);
        }
        \PHPEMSPRO\tpl::getInstance()->assign('basics',$basics);
        \PHPEMSPRO\tpl::getInstance()->assign('subjects',$subjects);
        \PHPEMSPRO\tpl::getInstance()->assign('training',$training);
        \PHPEMSPRO\tpl::getInstance()->display('training');
    }

    public function index()
    {
        $trainings = training::getTrainingsByArgs();
        $subjects = array();
        foreach($trainings as $training)
        {
            $subjects[$training['trid']] = points::getSubjects(array(array("AND","subjecttrid = :trid","trid",$training['trid'])));
        }
        \PHPEMSPRO\tpl::getInstance()->assign('subjects',$subjects);
        \PHPEMSPRO\tpl::getInstance()->assign('trainings',$trainings);
        \PHPEMSPRO\tpl::getInstance()->display('index');
    }
}