<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/11/25
 * Time: 21:24
 */

namespace PHPEMSPRO\user\controller\app;

use PHPEMSPRO\core\model\apps;
use PHPEMSPRO\exam\model\faq;
use PHPEMSPRO\exam\model\points;
use PHPEMSPRO\exam\model\question;
use PHPEMSPRO\user\app;

class msg extends app
{
    protected function _start()
    {
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

    public function error()
    {
        $erid = \PHPEMSPRO\route::get('erid');
        $error = question::getErrorById($erid);
        $subject = points::getSubjectById($error['ersubjectid']);
        $questypes = question::getQuestypesByArgs();
        $question = question::getQuestionById($subject['subjectdb'],$error['erquestionid']);
        if($question['questionparent'])
        {
            $parent = question::getQuestionRowsById($subject['subjectdb'],$question['questionparent'],false);
            \PHPEMSPRO\tpl::getInstance()->assign('parent',$parent);
        }
        \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
        \PHPEMSPRO\tpl::getInstance()->assign('error',$error);
        \PHPEMSPRO\tpl::getInstance()->assign('question',$question);
        \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
        \PHPEMSPRO\tpl::getInstance()->display('msg_error');
    }

    public function faq()
    {
        $faqid = \PHPEMSPRO\route::get('faqid');
        $subjectid = \PHPEMSPRO\route::get('subjectid');
        $subject = points::getSubjectById($subjectid);
        $faq = faq::getFaqById($subject['subjectdb'],$faqid);
        $questypes = question::getQuestypesByArgs();
        $question = question::getQuestionById($subject['subjectdb'],$faq['faqquestionid']);
        if($question['questionparent'])
        {
            $parent = question::getQuestionRowsById($subject['subjectdb'],$question['questionparent'],false);
            \PHPEMSPRO\tpl::getInstance()->assign('parent',$parent);
        }
        $app = apps::getAppByCode('exam');
        $app['appsetting']['selectortype'] = explode(',',$app['appsetting']['selectortype']);
        \PHPEMSPRO\tpl::getInstance()->assign('setting',$app['appsetting']);
        \PHPEMSPRO\tpl::getInstance()->assign('faq',$faq);
        \PHPEMSPRO\tpl::getInstance()->assign('question',$question);
        \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
        \PHPEMSPRO\tpl::getInstance()->display('msg_faq');
    }

    public function batdel()
    {
        $delids = \PHPEMSPRO\route::get('delids');
        foreach($delids as $msrid => $p)
        {
            \PHPEMSPRO\user\model\msg::delMsgUser($msrid);
        }
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function del($msrid)
    {
        $msrid = \PHPEMSPRO\route::get('msrid');
        \PHPEMSPRO\user\model\msg::delMsgUser($msrid);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function getnewmsgnumber()
    {
        echo \PHPEMSPRO\user\model\msg::getNewUserMsgNumber($this->_user['username']);
        exit;
    }

    public function detail()
    {
        $msrid = \PHPEMSPRO\route::get('msrid');
        $msg = \PHPEMSPRO\user\model\msg::getUserMsgById($msrid);
        if(!$msg['msrstatus'])
        {
            \PHPEMSPRO\user\model\msg::modifyMsgUser($msrid,array('msrstatus' => 1 ));
        }
        \PHPEMSPRO\tpl::getInstance()->assign('msg',$msg);
        \PHPEMSPRO\tpl::getInstance()->display('msg_detail');
    }

    public function index()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 1?$page:1;
        $args = array();
        $args[] = array('AND',"msrusername = :msrusername",'msrusername',$this->_user['username']);
        if($this->search['stime'])
        {
            $stime = strtotime($this->search['stime']);
            $args[] = array('AND',"msgtime >= :msgstime",'msgstime',$stime);
        }
        if($this->search['etime'])
        {
            $etime = strtotime($this->search['etime']);
            $args[] = array('AND',"msgtime <= :msgetime",'msgetime',$etime);
        }
        $msgs = \PHPEMSPRO\user\model\msg::getUserMsgList($args,$page);
        \PHPEMSPRO\tpl::getInstance()->assign('msgs',$msgs);
        \PHPEMSPRO\tpl::getInstance()->display('msg');
    }
}