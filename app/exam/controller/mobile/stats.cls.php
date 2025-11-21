<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2019/1/4
 * Time: 21:09
 */

namespace PHPEMSPRO\exam\controller\mobile;


use PHPEMSPRO\core\model\apps;
use PHPEMSPRO\exam\mobile;
use PHPEMSPRO\exam\model\exams;
use PHPEMSPRO\exam\model\favor;
use PHPEMSPRO\exam\model\points;
use PHPEMSPRO\exam\model\question;

class stats extends mobile
{
    protected function _start()
    {
        if(!$this->currentsubject)
        {
            $message = array(
                'statusCode' => 300,
                "callbackType" => "forward",
                "forwardUrl" => "index.php?content-mobile"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        $this->subject = points::getSubjectById($this->currentsubject);
        $this->basic = exams::getBasicById($this->subject['subjectdb'],$this->currentbasic);
        $this->status = exams::getIsMember($this->subject['subjectdb'],$this->_user['username'],$this->currentbasic);
        if(strtotime($this->status['obendtime']) >= TIME)
        {
            $this->status['status'] = true;
        }
        \PHPEMSPRO\tpl::getInstance()->assign('subject',$this->subject);
        \PHPEMSPRO\tpl::getInstance()->assign('basic',$this->basic);
        \PHPEMSPRO\tpl::getInstance()->assign('status',$this->status);
        if($this->basic['basicexam']['model'] == 2)
        {
            $message = array(
                'statusCode' => 200,
                "callbackType" => "forward",
                "forwardUrl" => "index.php?exam-mobile-exam"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
    }

    public function index()
    {
        $args = array();
        $args[] = array("AND","ehbasicid = :basicid","basicid",$this->basic['basicid']);
        $args[] = array("AND","ehusername = :username","username",$this->_user['username']);
        $histories = favor::getExamHistoryList($this->subject['subjectdb'],$args,1,10,'ehscore,ehstarttime');
        $points = points::getPointsBySubjectid($this->subject['subjectid']);
        $sections = $points['sections'];
        $points = $points['points'];
        $record = favor::getRecordSession($this->subject['subjectdb'],$this->_user['username']);
        $wrong = array();
        $right = array();
        $favor = array();
        $note = array();
        foreach($this->basic['basicpoints'] as $key => $point)
        {
            $rt = 0;
            $wg = 0;
            foreach($point as $p)
            {
                $numbers[$p] = question::getQuestionNumberByPointid($this->subject['subjectdb'],$p);
                $favor[$key] += intval(\PHPEMSPRO\pedis::getInstance()->getHashData('favornumber',$this->_user['username'].'-'.$this->subject['subjectdb'].'-'.$p));
                $note[$key] += intval(\PHPEMSPRO\pedis::getInstance()->getHashData('notenumber',$this->_user['username'].'-'.$this->subject['subjectdb'].'-'.$p));
                $rt += $record['recordnumber'][$p]['right'];
                $wg += $record['recordnumber'][$p]['wrong'];
            }
            $wrong[$key] = intval($wg);
            $right[$key] = intval($rt);
        }
        unset($record);
        $favor = array_sum($favor);
        $note = array_sum($note);
        $histories['data'] = array_reverse($histories['data']);
        \PHPEMSPRO\tpl::getInstance()->assign('allnumber',array('right' => is_array($right)?array_sum($right):0,'wrong' => is_array($wrong)?array_sum($wrong):0,'all' => is_array($numbers)?array_sum($numbers):0));
        \PHPEMSPRO\tpl::getInstance()->assign('wrong',$wrong);
        \PHPEMSPRO\tpl::getInstance()->assign('favor',$favor);
        \PHPEMSPRO\tpl::getInstance()->assign('note',$note);
        \PHPEMSPRO\tpl::getInstance()->assign('histories',$histories);
        \PHPEMSPRO\tpl::getInstance()->display('stats');
    }
}