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

class clear extends mobile
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
        if(!$this->status['status'])
        {
            $message = array(
                'statusCode' => 200,
                'message' => '你需要购买后才可以使用本功能',
                "callbackType" => "forward",
                "forwardUrl" => "index.php?exam-mobile-basic-open"
            );
            \PHPEMSPRO\route::urlJump($message);
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

    public function notes()
    {
        foreach($this->basic['basicpoints'] as $ps)
        {
            foreach($ps as $p)
            {
                \PHPEMSPRO\pedis::getInstance()->delHashData('notenumber',$this->_user['username'].'-'.$this->subject['subjectdb'].'-'.$p);
            }
        }
        favor::delNotesByUsername($this->subject['subjectdb'],$this->_user['username']);
        $message = array(
            'statusCode' => 200,
            'message' => '清理成功！'
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function favors()
    {

        foreach($this->basic['basicpoints'] as $ps)
        {
            foreach($ps as $p)
            {
                \PHPEMSPRO\pedis::getInstance()->delHashData('favornumber',$this->_user['username'].'-'.$this->subject['subjectdb'].'-'.$p);
            }
        }
        favor::delFavorsByUsername($this->subject['subjectdb'],$this->_user['username']);
        $message = array(
            'statusCode' => 200,
            'message' => '清理成功！'
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function answers()
    {
        $record = favor::getRecordSession($this->subject['subjectdb'],$this->_user['username']);
		foreach($this->basic['basicpoints'] as $ps)
        {
            foreach($ps as $pointid)
            {
                unset($record['recordright'][$pointid]);
				unset($record['recordwrong'][$pointid]);
				unset($record['recordnumber'][$pointid]);
            }
        }
		\PHPEMSPRO\pedis::getInstance()->setHashData('records',$this->_user['username'].'-'.$this->subject['subjectdb'],json_encode($record));
        favor::delRecord($this->subject['subjectdb'],$this->_user['username']);
        $message = array(
            'statusCode' => 200,
            'message' => '清理成功！'
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function process()
    {
        foreach($this->basic['basicpoints'] as $ps)
        {
            foreach($ps as $p)
            {
                \PHPEMSPRO\pedis::getInstance()->delHashData('process',$this->_user['username'].'-'.$this->subject['subjectdb'].'-'.$p);
            }
        }
        $message = array(
            'statusCode' => 200,
            'message' => '清理成功！'
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function index()
    {
        \PHPEMSPRO\tpl::getInstance()->display('clear');
    }
}