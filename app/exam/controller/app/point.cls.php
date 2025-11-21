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
use PHPEMSPRO\exam\model\faq;
use PHPEMSPRO\exam\model\favor;
use PHPEMSPRO\exam\model\points;
use PHPEMSPRO\exam\model\question;

class point extends app
{
    protected function _start()
    {
        if(!$this->currentsubject)
        {
            $message = array(
                'statusCode' => 300,
                "callbackType" => "forward",
                "forwardUrl" => "index.php"
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
                "forwardUrl" => "index.php?exam-app-exam"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
    }

    public function faq()
    {
        if(\PHPEMSPRO\route::get('savefaq'))
        {
            $rs = faq::getLastFaqByUsername($this->subject['subjectdb'],$this->_user['username']);
            if((TIME - strtotime($rs['faqasktime'])) < 30)
            {
                $message = array(
                    'statusCode' => 300,
                    'message' => '您提问的太频繁了，休息30秒再提问吧！'
                );
                \PHPEMSPRO\route::urlJump($message);
            }
            $args = \PHPEMSPRO\route::get('args');
            $args['faqusername'] = $this->_user['username'];
            $args['faqsubjectid'] = $this->subject['subjectid'];
            $args['faqasktime'] = TIME;
            $args['faqstatus'] = 0;
            faq::saveFaq($this->subject['subjectdb'],$args);
            $message = array(
                'statusCode' => 200,
                'message' => '保存成功！',
                "callbackType" => "forward",
                "forwardUrl" => "reload"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        else
        {
            $page = \PHPEMSPRO\route::get('page');
            $questionid = \PHPEMSPRO\route::get('questionid');
            $question = question::getQuestionById($this->subject['subjectdb'],$questionid);
            $parent = question::getQuestionRowsById($this->subject['subjectdb'],$question['questionparent'],false);
            $questypes = question::getQuestypesByArgs();
            $args = array(
                array("AND","faqquestionid = :faqquestionid","faqquestionid",$questionid),
                array("AND","(faqstatus = 1 OR (faqstatus = 0 AND faqusername = :faqusername))","faqusername",$this->_user['username'])
            );
            $faqs = faq::getFaqList($this->subject['subjectdb'],$args,$page);
            $app = apps::getAppByCode('exam');
            $app['appsetting']['selectortype'] = explode(',',$app['appsetting']['selectortype']);
            \PHPEMSPRO\tpl::getInstance()->assign('setting',$app['appsetting']);
            \PHPEMSPRO\tpl::getInstance()->assign('question',$question);
            \PHPEMSPRO\tpl::getInstance()->assign('parent',$parent);
            \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
            \PHPEMSPRO\tpl::getInstance()->assign('faqs',$faqs);
            \PHPEMSPRO\tpl::getInstance()->display('point_faq');
        }
    }

    public function intro()
    {
        $pointid = \PHPEMSPRO\route::get('pointid');
        $point = points::getPointById($pointid);
        $section = points::getSectionById($point['pointsection']);
        \PHPEMSPRO\tpl::getInstance()->assign('section',$section);
        \PHPEMSPRO\tpl::getInstance()->assign('point',$point);
        \PHPEMSPRO\tpl::getInstance()->display('point_intro');
    }

    public function video()
    {
        $pointid = \PHPEMSPRO\route::get('pointid');
        $point = points::getPointById($pointid);
        $section = points::getSectionById($point['pointsection']);
        \PHPEMSPRO\tpl::getInstance()->assign('section',$section);
        \PHPEMSPRO\tpl::getInstance()->assign('point',$point);
        \PHPEMSPRO\tpl::getInstance()->display('point_video');
    }

    public function errors()
    {
        $questionid = \PHPEMSPRO\route::get('questionid');
        if(!$questionid)
        {
            $message = array(
                'statusCode' => 300,
                'message' => '错误的题号！'
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        if(\PHPEMSPRO\route::get('adderrors'))
        {
            $error = implode(',',\PHPEMSPRO\route::get('error'));
            $errorcontent = \PHPEMSPRO\route::get('errorcontent');
            $args = array(
                'erusername' => $this->_user['username'],
                'ertime' => TIME,
                'erintro' => "{$error}:{$errorcontent}",
                'ersubjectid' => $this->subject['subjectid'],
                'erquestionid' => $questionid
            );
            question::addErros($args);
            $message = array(
                'statusCode' => 200,
                'message' => '反馈成功！',
                "callbackType" => "forward",
                "forwardUrl" => "back"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        else
        {
            \PHPEMSPRO\tpl::getInstance()->assign('questionid',$questionid);
            \PHPEMSPRO\tpl::getInstance()->display('errors');
        }
    }

    public function save()
    {
        $pointid = \PHPEMSPRO\route::get('pointid');
        $questionid = \PHPEMSPRO\route::get('questionid');
        $question = \PHPEMSPRO\route::get('question');
        favor::saveSingleRecord($this->subject['subjectdb'],$this->_user['username'],$pointid,$questionid,$question[$questionid]);
        $message = array(
            'statusCode' => 200
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function note()
    {
        if(\PHPEMSPRO\route::get('savenote'))
        {
            $args = \PHPEMSPRO\route::get('args');
            $args['noteusername'] = $this->_user['username'];
            $args['notesubject'] = $this->subject['subjectid'];
            $args['notetime'] = TIME;
            $args['notestatus'] = 0;
            favor::saveNote($this->subject['subjectdb'],$args);
            $question = question::getQuestionById($this->subject['subjectdb'],$args['notequestionid']);
            if($question['questionparent'])
            {
                $qr = question::getQuestionRowsById($this->subject['subjectdb'],$question['questionparent'],false);
                foreach($qr['qrpoints'] as $p)
                {
                    $number = favor::getNoteNumberByPointid($this->subject['subjectdb'],$this->_user['username'],$p);
                    \PHPEMSPRO\pedis::getInstance()->setHashData('notenumber',$this->_user['username'].'-'.$this->subject['subjectdb'].'-'.$p,$number);
                }
            }
            else
            {
                foreach($question['questionpoints'] as $p)
                {
                    $number = favor::getNoteNumberByPointid($this->subject['subjectdb'],$this->_user['username'],$p);
                    \PHPEMSPRO\pedis::getInstance()->setHashData('notenumber',$this->_user['username'].'-'.$this->subject['subjectdb'].'-'.$p,$number);
                }
            }
            $message = array(
                'statusCode' => 200,
                'message' => '保存成功！'
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        else
        {
            $page = \PHPEMSPRO\route::get('page');
            $page = $page >=1?$page:1;
            $questionid = \PHPEMSPRO\route::get('questionid');
            $args = array();
            $args[] = array("and","noteusername != :noteusername","noteusername",$this->_user['username']);
            $args[] = array("and","notequestionid = :notequestionid","notequestionid",$questionid);
            $args[] = array("and","notestatus = 1");
            \PHPEMSPRO\pg::setUrlTarget(' class="ajax" target="noteboxlist" ');
            $notes = favor::getNoteList($this->subject['subjectdb'],$args,$page);
            \PHPEMSPRO\tpl::getInstance()->assign('page',$page);
            \PHPEMSPRO\tpl::getInstance()->assign('questionid',$questionid);
            \PHPEMSPRO\tpl::getInstance()->assign('notes',$notes);
            \PHPEMSPRO\tpl::getInstance()->display('point_note');
        }
    }

    public function index()
    {
        $pointid = \PHPEMSPRO\route::get('pointid');
        if($pointid != current(current($this->basic['basicpoints'])))
        {
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
        }
        $number = \PHPEMSPRO\route::get('number');
        $questype = \PHPEMSPRO\route::get('questype');
        if($number < 1)
        {
            if($questype)
            {
                $number = 1;
            }
            else
            {
                $lastquestion = intval(\PHPEMSPRO\pedis::getInstance()->getHashData('process',$this->_user['username'].'-'.$this->subject['subjectdb'].'-'.$pointid));
                $number = intval($lastquestion) + 1;
            }
        }
        $point = points::getPointById($pointid);
        $ids = question::getQuestionidsByPointid($this->subject['subjectdb'],$point['pointid']);
        $ids = implode(',',$ids);
        $parent = array();
        $args = array(array("AND","questionid in ({$ids})"),array("AND","questionstatus = 1"));
        if($questype)
        {
            $args[] = array("AND","questiontype = :questiontype","questiontype",$questype);
        }
        $questions = question::getQuestionsByArgs($this->subject['subjectdb'],$args,"questionparent asc,questionorder asc,questionid desc",'questionid');
        $allnumber = count($questions);
        if($number > $allnumber)$number = $allnumber;
        if(!$questype)
        {
            \PHPEMSPRO\pedis::getInstance()->setHashData('process',$this->_user['username'].'-'.$this->subject['subjectdb'].'-'.$pointid,$number-1);
            \PHPEMSPRO\pedis::getInstance()->setHashData('lastquestion',$this->_user['username'].'-'.$this->subject['subjectdb'].'-'.$this->currentbasic,json_encode(array($pointid => $lastquestion)));
        }
        if(\PHPEMSPRO\route::get('questionid'))
        {
            $i = array_search(array('questionid' => \PHPEMSPRO\route::get('questionid')),$questions);
            if($i !== false)
            {
                $number = $i+1;
            }
        }
        $questionid = $questions[$number - 1]['questionid'];
        $question = question::getQuestionById($this->subject['subjectdb'],$questionid);
        $parent = question::getQuestionRowsById($this->subject['subjectdb'],$question['questionparent'],false);
        $questypes = question::getQuestypesByArgs();
        $app = apps::getAppByCode('exam');
        $app['appsetting']['selectortype'] = explode(',',$app['appsetting']['selectortype']);
        $favors = favor::getFavorByQuestionIds($this->subject['subjectdb'],$questionid,$this->_user['username']);
        $record = favor::getRecordSession($this->subject['subjectdb'],$this->_user['username']);
        $right = $record['recordright'][$pointid];
        $wrong = $record['recordwrong'][$pointid];
        unset($record);
        $useranswer = array();
        foreach($right as $key => $p)
        {
            $useranswer[$key]['answer'] = $p;
            $useranswer[$key]['status'] = 'right';
        }
        foreach($wrong as $key => $p)
        {
            $useranswer[$key]['answer'] = $p;
            $useranswer[$key]['status'] = 'wrong';
        }
        $note = favor::getNoteByUserAndQuestionid($this->subject['subjectdb'],$this->_user['username'],$questionid);
        \PHPEMSPRO\tpl::getInstance()->assign('setting',$app['appsetting']);
        \PHPEMSPRO\tpl::getInstance()->assign('useranswer',$useranswer);
        \PHPEMSPRO\tpl::getInstance()->assign('point',$point);
        \PHPEMSPRO\tpl::getInstance()->assign('note',$note);
        \PHPEMSPRO\tpl::getInstance()->assign('number',$number);
        \PHPEMSPRO\tpl::getInstance()->assign('allnumber',$allnumber);
        \PHPEMSPRO\tpl::getInstance()->assign('favors',$favors);
        \PHPEMSPRO\tpl::getInstance()->assign('parent',$parent);
        \PHPEMSPRO\tpl::getInstance()->assign('question',$question);
        \PHPEMSPRO\tpl::getInstance()->assign('questions',$questions);
        \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
        \PHPEMSPRO\tpl::getInstance()->assign('questype',$questype);
        \PHPEMSPRO\tpl::getInstance()->display('point');
    }
}