<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/12/8
 * Time: 10:47
 */

namespace PHPEMSPRO\exam\controller\app;

use PHPEMSPRO\exam\app;
use PHPEMSPRO\exam\model\exams;
use PHPEMSPRO\exam\model\favor;
use PHPEMSPRO\exam\model\points;
use PHPEMSPRO\exam\model\question;

class ajax extends app
{

    public function setcurrentbasic()
    {
        $basicid = \PHPEMSPRO\route::get('basicid');
        $subjectid = \PHPEMSPRO\route::get('subjectid');
        $subject = points::getSubjectById($subjectid);
        $basic = exams::getBasicById($subject['subjectdb'],$basicid);
        $status = exams::getIsMember($subject['subjectdb'],$this->_user['username'],$basicid);
        if(strtotime($status['obendtime']) >= TIME)
        {
            $status['status'] = true;
        }
        \PHPEMSPRO\session::getInstance()->setSessionUser(array('currentsubject' => $subjectid,'currentbasic' => $basicid));
        if($basic['basicexam']['model'])
        {
            if(!$status['status'])
            {
                $message = array(
                    'statusCode' => 200,
                    "callbackType" => "forward",
                    "forwardUrl" => "index.php?exam-app-basic-open"
                );
                \PHPEMSPRO\route::urlJump($message);
            }
        }
        $message = array(
            'statusCode' => 200,
            "callbackType" => "forward",
            "forwardUrl" => "index.php?exam-app-basic"
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function cancelfavor()
    {
        $questionid = \PHPEMSPRO\route::get('questionid');
        $username = $this->_user['username'];
        $subject = \PHPEMSPRO\exam\model\points::getSubjectById($this->currentsubject);
        \PHPEMSPRO\exam\model\favor::delFavor($subject['subjectdb'],$username,$questionid);
        $question = question::getQuestionById($subject['subjectdb'],$questionid);
        if($question['questionparent'])
        {
            $qr = question::getQuestionRowsById($subject['subjectdb'],$question['questionparent'],false);
            foreach($qr['qrpoints'] as $p)
            {
                $number = favor::getFavorNumberByPointid($subject['subjectdb'],$this->_user['username'],$p);
                \PHPEMSPRO\pedis::getInstance()->setHashData('favornumber',$this->_user['username'].'-'.$subject['subjectdb'].'-'.$p,$number);
            }
        }
        else
        {
            foreach($question['questionpoints'] as $p)
            {
                $number = favor::getFavorNumberByPointid($subject['subjectdb'],$this->_user['username'],$p);
                \PHPEMSPRO\pedis::getInstance()->setHashData('favornumber',$this->_user['username'].'-'.$subject['subjectdb'].'-'.$p,$number);
            }
        }
        $message = array(
            'statusCode' => 200
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function favorquestion()
    {
        $questionid = \PHPEMSPRO\route::get('questionid');
        $username = $this->_user['username'];
        $subject = \PHPEMSPRO\exam\model\points::getSubjectById($this->currentsubject);
        \PHPEMSPRO\exam\model\favor::favorQuestion($subject['subjectdb'],$username,$questionid);
        $question = question::getQuestionById($subject['subjectdb'],$questionid);
        if($question['questionparent'])
        {
            $qr = question::getQuestionRowsById($subject['subjectdb'],$question['questionparent'],false);
            foreach($qr['qrpoints'] as $p)
            {
                $number = favor::getFavorNumberByPointid($subject['subjectdb'],$this->_user['username'],$p);
                \PHPEMSPRO\pedis::getInstance()->setHashData('favornumber',$this->_user['username'].'-'.$subject['subjectdb'].'-'.$p,$number);
            }
        }
        else
        {
            foreach($question['questionpoints'] as $p)
            {
                $number = favor::getFavorNumberByPointid($subject['subjectdb'],$this->_user['username'],$p);
                \PHPEMSPRO\pedis::getInstance()->setHashData('favornumber',$this->_user['username'].'-'.$subject['subjectdb'].'-'.$p,$number);
            }
        }
        $message = array(
            'statusCode' => 200
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function index()
    {
        return;
    }
}