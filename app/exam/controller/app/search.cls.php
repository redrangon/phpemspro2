<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2019/1/4
 * Time: 21:09
 */

namespace PHPEMSPRO\exam\controller\app;


use PHPEMSPRO\core\model\apps;
use PHPEMSPRO\exam\app;
use PHPEMSPRO\exam\model\exams;
use PHPEMSPRO\exam\model\points;
use PHPEMSPRO\exam\model\question;

class search extends app
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
                "forwardUrl" => "index.php?exam-app-basic-open"
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
                "forwardUrl" => "index.php?exam-app-exam"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        $search = \PHPEMSPRO\route::get('search');
        if($search)
        {
            $this->u = '';
            $this->search = $search;
            \PHPEMSPRO\tpl::getInstance()->assign('search',$search);
            foreach($search as $key => $arg)
            {
                $this->u .= "&search[{$key}]={$arg}";
            }
            unset($search);
        }
    }

    public function qrdetail()
    {
        $qrid = \PHPEMSPRO\route::get('qrid');
        $questionrows = question::getQuestionRowsById($this->subject['subjectdb'],$qrid);
        $app = apps::getAppByCode('exam');
        $app['appsetting']['selectortype'] = explode(',',$app['appsetting']['selectortype']);
        $questypes = question::getQuestypesByArgs(array(array("AND","find_in_set(questcode,:questcode)","questcode",implode(',',$this->subject['subjectsetting']))));
        \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
        \PHPEMSPRO\tpl::getInstance()->assign('setting',$app['appsetting']);
        \PHPEMSPRO\tpl::getInstance()->assign('questionrows',$questionrows);
        \PHPEMSPRO\tpl::getInstance()->display('search_qrdetail');
    }

    public function detail()
    {
        $questionid = \PHPEMSPRO\route::get('questionid');
        $question = question::getQuestionById($this->subject['subjectdb'],$questionid);
        $app = apps::getAppByCode('exam');
        $app['appsetting']['selectortype'] = explode(',',$app['appsetting']['selectortype']);
        $questypes = question::getQuestypesByArgs(array(array("AND","find_in_set(questcode,:questcode)","questcode",implode(',',$this->subject['subjectsetting']))));
        \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
        \PHPEMSPRO\tpl::getInstance()->assign('setting',$app['appsetting']);
        \PHPEMSPRO\tpl::getInstance()->assign('question',$question);
        \PHPEMSPRO\tpl::getInstance()->display('search_detail');
    }

    public function index()
    {
        $page = \PHPEMSPRO\route::get('page');
        if($this->search['keywords'])
        {
            $keywords = str_replace(',',' +',\PHPEMSPRO\word::getInstance()->analy($this->search['keywords'],3));
            if($this->search['questiontype'])
            {
                $args = array();
                $args[] = array("AND","qrstatus = 1");
                $args[] = array("AND","qrsubject = :subjectid","subjectid",$this->subject['subjectid']);
                $args[] = array("AND","MATCH (`qrtags`) AGAINST (:qrtags IN BOOLEAN MODE)","qrtags",'+'.$keywords);
                $questions = question::getQuestionRowsList($this->subject['subjectdb'],$args,$page);
            }
            else
            {
                $args = array();
                $args[] = array("AND","questionstatus = 1");
                $args[] = array("AND","questionparent = 0");
                $args[] = array("AND","questionsubject = :subjectid","subjectid",$this->subject['subjectid']);
                $args[] = array("AND","MATCH (`questiontags`) AGAINST (:questiontags IN BOOLEAN MODE)","questiontags",'+'.$keywords);
                $questions = question::getQuestionList($this->subject['subjectdb'],$args,$page);
            }
        }
        $questypes = question::getQuestypesByArgs(array(array("AND","find_in_set(questcode,:questcode)","questcode",implode(',',$this->subject['subjectsetting']))));
        \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
        \PHPEMSPRO\tpl::getInstance()->assign('questions',$questions);
        \PHPEMSPRO\tpl::getInstance()->display('search');
    }
}