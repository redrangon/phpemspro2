<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/11/25
 * Time: 21:24
 */

namespace PHPEMSPRO\exam\controller\master;

use PHPEMSPRO\exam\master;
use PHPEMSPRO\exam\model\favor;
use PHPEMSPRO\exam\model\points;
use PHPEMSPRO\exam\model\question;
use PHPEMSPRO\user\model\msg;

class tools extends master
{

    public function index()
    {

        \PHPEMSPRO\tpl::getInstance()->display('tools');
    }

    public function errors()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 1?$page:1;
        $subjects = points::getSubjects();
        $args = array();
        $errors = question::getErrorsList($args,$page);
        \PHPEMSPRO\tpl::getInstance()->assign('subjects',$subjects);
        \PHPEMSPRO\tpl::getInstance()->assign('errors',$errors);
        \PHPEMSPRO\tpl::getInstance()->assign('page',$page);
        \PHPEMSPRO\tpl::getInstance()->display('tools_errors');
    }

    public function signdone()
    {
        $erid = \PHPEMSPRO\route::get('erid');
        $error = question::getErrorById($erid);
        $args = array(
            'erstatus' => 1,
            'erteacher' => $this->_user['username']
        );
        question::modifyErrors($erid,$args);
        $subject = points::getSubjectById($error['ersubjectid']);
        $args = array(
            'msgtitle' => "错题反馈处理通知",
            'msgcontent' => "您反馈的《{$subject['subjectname']}》科目下ID为{$error['erquestionid']}的试题已经被教师修改，感谢您的反馈",
            'msgposter' => $this->_user['username'],
            'msgurl' => 'index.php?user-auto-msg-error&erid='.$erid
        );
        $msgid = msg::addMsg($args);
        $args = array(
            'msrusername' => $error['erusername'],
            'msrmsgid' => $msgid,
            'msrstatus' => 0
        );
        msg::addMsgUser($args);
        $message = array(
            'statusCode' => 200,
            "message" => "标记成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function del()
    {
        $erid = \PHPEMSPRO\route::get('erid');
        question::delErrors($erid);
        $message = array(
            'statusCode' => 200,
            "message" => "删除成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function gorepair()
    {
        $erid = \PHPEMSPRO\route::get('erid');
        $error = question::getErrorById($erid);
        $subject = points::getSubjectById($error['ersubjectid']);
        $question = question::getQuestionById($subject['subjectdb'],$error['erquestionid']);
        if($question['questionparent'])
        {
            $message = array(
                'statusCode' => 200,
                "callbackType" => "forward",
                "forwardUrl" => "index.php?exam-master-exams-questionrows&subjectid={$error['ersubjectid']}&search[qrid]={$question['questionparent']}"
            );
            exit(json_encode($message));
        }
        else
        {
            $message = array(
                'statusCode' => 200,
                "callbackType" => "forward",
                "forwardUrl" => "index.php?exam-master-exams-questions&subjectid={$error['ersubjectid']}&search[questionid]={$error['erquestionid']}"
            );
            exit(json_encode($message));
        }
    }

    public function clearuser()
    {
        \PHPEMSPRO\pedis::getInstance()->delHashData('users');
        \PHPEMSPRO\pedis::getInstance()->delHashData('session');
        $message = array(
            'statusCode' => 200,
            "message" => "清理成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function clearquestionrows()
    {
        \PHPEMSPRO\pedis::getInstance()->delHashData('questionrows');
        \PHPEMSPRO\pedis::getInstance()->delHashData('subjectquestionrows');
        $message = array(
            'statusCode' => 200,
            "message" => "清理成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function clearquestions()
    {
        \PHPEMSPRO\pedis::getInstance()->delHashData('questions');
        \PHPEMSPRO\pedis::getInstance()->delHashData('subjectquestions');
        $message = array(
            'statusCode' => 200,
            "message" => "清理成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function clearnumber()
    {
        $subjects = points::getSubjects();
        foreach($subjects as $subject)
        {
            \PHPEMSPRO\pedis::getInstance()->delHashData('number',$subject['subjectdb']);
        }
        $message = array(
            'statusCode' => 200,
            "message" => "清理成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function clearsession()
    {
        \PHPEMSPRO\pedis::getInstance()->delHashData('examsession');
        $message = array(
            'statusCode' => 200,
            "message" => "清理成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }
}