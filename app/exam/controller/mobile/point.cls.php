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
use PHPEMSPRO\exam\model\faq;
use PHPEMSPRO\exam\model\favor;
use PHPEMSPRO\exam\model\points;
use PHPEMSPRO\exam\model\question;

class point extends mobile
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
            $faqs = faq::getFaqList($this->subject['subjectdb'],$args,$page,5);
            \PHPEMSPRO\tpl::getInstance()->assign('question',$question);
            \PHPEMSPRO\tpl::getInstance()->assign('parent',$parent);
            \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
            \PHPEMSPRO\tpl::getInstance()->assign('page',$page);
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

    public function clear()
    {
        $pointid = \PHPEMSPRO\route::get('pointid');
        $record = favor::getRecordSession($this->subject['subjectdb'],$this->_user['username']);
        unset($record['recordright'][$pointid]);
        unset($record['recordwrong'][$pointid]);
        unset($record['recordnumber'][$pointid]);
        \PHPEMSPRO\pedis::getInstance()->setHashData('records',$this->_user['username'].'-'.$this->subject['subjectdb'],json_encode($record));
        $message = array(
            'statusCode' => 200,
            'message' => '清理成功！',
            "callbackType" => "forward",
            "forwardUrl" => "index.php?exam-mobile-point-paper&pointid=".$pointid
        );
        \PHPEMSPRO\route::urlJump($message);
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

    public function savelastquestion()
    {
        $pointid = \PHPEMSPRO\route::get('pointid');
        $lastquestion = \PHPEMSPRO\route::get('lastquestion');
        \PHPEMSPRO\pedis::getInstance()->setHashData('process',$this->_user['username'].'-'.$this->subject['subjectdb'].'-'.$pointid,$lastquestion);
        $message = array(
            'statusCode' => 200
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function save()
    {
        $pointid = \PHPEMSPRO\route::get('pointid');
        $lastquestion = \PHPEMSPRO\route::get('lastquestion');
        $question = \PHPEMSPRO\route::get('question');
        foreach($question as $questionid => $v)
        {
            favor::saveSingleRecord($this->subject['subjectdb'],$this->_user['username'],$pointid,$questionid,$v);
        }
        $message = array(
            'statusCode' => 200,
            'useranswer' => $useranswer
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
            $note = favor::getNoteByUserAndQuestionid($this->subject['subjectdb'],$this->_user['username'],$questionid);
            $args = array();
            $args[] = array("and","noteusername != :noteusername","noteusername",$this->_user['username']);
            $args[] = array("and","notequestionid = :notequestionid","notequestionid",$questionid);
            $args[] = array("and","notestatus = 1");
            $notes = favor::getNoteList($this->subject['subjectdb'],$args,$page);
            \PHPEMSPRO\tpl::getInstance()->assign('page',$page);
            \PHPEMSPRO\tpl::getInstance()->assign('questionid',$questionid);
            \PHPEMSPRO\tpl::getInstance()->assign('note',$note);
            \PHPEMSPRO\tpl::getInstance()->assign('notes',$notes);
            \PHPEMSPRO\tpl::getInstance()->display('note');
        }
    }

    public function paper()
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
                    "forwardUrl" => "index.php?exam-mobile-basic-open"
                );
                \PHPEMSPRO\route::urlJump($message);
            }
        }
        $point = points::getPointById($pointid);
        $record = favor::getRecordSession($this->subject['subjectdb'],$this->_user['username']);
        $right = $record['recordright'][$pointid];
        $wrong = $record['recordwrong'][$pointid];
        unset($record);
        $useranswer = array();
        foreach($right as $key => $p)
        {
            $useranswer[$key] = $p;
        }
        foreach($wrong as $key => $p)
        {
            $useranswer[$key] = $p;
        }
        $ids = question::getQuestionidsByPointid($this->subject['subjectdb'],$point['pointid']);
        $ids = implode(',',$ids);
        $parent = array();
        $questions = question::getQuestionsByArgs($this->subject['subjectdb'],array(array("AND","questionid in ({$ids})"),array("AND","questionstatus = 1")));
        foreach($questions as $key => $question)
        {
            if($question['questionparent'])
            {
                if(!$parent[$question['questionparent']])
                {
                    $parent[$question['questionparent']] = question::getQuestionRowsById($this->subject['subjectdb'],$question['questionparent'],false);
                }
            }
        }
        $questypes = question::getQuestypesByArgs();
        $app = apps::getAppByCode('exam');
        $app['appsetting']['selectortype'] = explode(',',$app['appsetting']['selectortype']);
        $favors = favor::getFavorByQuestionIds($this->subject['subjectdb'],$ids,$this->_user['username']);
        $lastquestion = intval(\PHPEMSPRO\pedis::getInstance()->getHashData('process',$this->_user['username'].'-'.$this->subject['subjectdb'].'-'.$pointid));
        \PHPEMSPRO\tpl::getInstance()->assign('setting',$app['appsetting']);
        \PHPEMSPRO\tpl::getInstance()->assign('lastquestion',$lastquestion);
        \PHPEMSPRO\tpl::getInstance()->assign('useranswer',$useranswer);
        \PHPEMSPRO\tpl::getInstance()->assign('point',$point);
        \PHPEMSPRO\tpl::getInstance()->assign('favors',$favors);
        \PHPEMSPRO\tpl::getInstance()->assign('parent',$parent);
        \PHPEMSPRO\tpl::getInstance()->assign('questions',$questions);
        \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
        \PHPEMSPRO\tpl::getInstance()->display('point_paper');
    }

    public function points()
    {
        $sectionid = \PHPEMSPRO\route::get('sectionid');
        $points = points::getPointsBySubjectid($this->subject['subjectid']);
        $sections = $points['sections'];
        $points = $points['points'];
        $record = favor::getRecordSession($this->subject['subjectdb'],$this->_user['username']);
        $numbers = array();
        $right = array();
        $rate = array();
        foreach ($points[$sectionid] as $point)
        {
            if($this->basic['basicpoints'][$sectionid][$point['pointid']])
            {
                $numbers[$point['pointid']] = question::getQuestionNumberByPointid($this->subject['subjectdb'], $point['pointid']);
                $right[$point['pointid']] = intval($record['recordnumber'][$point['pointid']]['right'] + $record['recordnumber'][$point['pointid']]['wrong']);
                $rate[$point['pointid']] = intval(100 * $record['recordnumber'][$point['pointid']]['right'] / ($record['recordnumber'][$point['pointid']]['right'] + $record['recordnumber'][$point['pointid']]['wrong']));
            }
        }
        unset($record);
        \PHPEMSPRO\tpl::getInstance()->assign('right',$right);
        \PHPEMSPRO\tpl::getInstance()->assign('rate',$rate);
        \PHPEMSPRO\tpl::getInstance()->assign('numbers',$numbers);
        \PHPEMSPRO\tpl::getInstance()->assign('sectionid',$sectionid);
        \PHPEMSPRO\tpl::getInstance()->assign('points',$points[$sectionid]);
        \PHPEMSPRO\tpl::getInstance()->display('point_list');
    }

    public function index()
    {
        $points = points::getPointsBySubjectid($this->subject['subjectid']);
        $sections = $points['sections'];
        $points = $points['points'];
        $numbers = array();
        $record = favor::getRecordSession($this->subject['subjectdb'],$this->_user['username']);
        $lastquestion = json_decode(\PHPEMSPRO\pedis::getInstance()->getHashData('lastquestion',$this->_user['username'].'-'.$this->currentbasic),true);
        if(is_array($lastquestion))$pointid = key($lastquestion);
        else $pointid = $sections[0]['points'][0]['pointid'];
        $index = $lastquestion[$pointid];
        $right = array();
        $rate = array();
        foreach($points as $key => $point)
        {
            $number = 0;
            $rt = 0;
            $wg = 0;
            foreach($point as $p)
            {
                if($this->basic['basicpoints'][$key][$p['pointid']])
                {
                    $number += question::getQuestionNumberByPointid($this->subject['subjectdb'],$p['pointid']);
                    $rt += $record['recordnumber'][$p['pointid']]['right'];
                    $wg += $record['recordnumber'][$p['pointid']]['wrong'];
                    if($p['pointid'] == $pointid)
                    {
                        $thispoint = $p;
                    }
                }
            }
            $numbers[$key] = $number;
            $right[$key] = intval($rt + $wg);
            if(($rt + $wg) > 0)$rate[$key] = intval(100*$rt/($rt + $wg));
            else $rate[$key] = 0;
        }
        unset($record);
        \PHPEMSPRO\tpl::getInstance()->assign('right',$right);
        \PHPEMSPRO\tpl::getInstance()->assign('rate',$rate);
        \PHPEMSPRO\tpl::getInstance()->assign('numbers',$numbers);
        \PHPEMSPRO\tpl::getInstance()->assign('sections',$sections);
        \PHPEMSPRO\tpl::getInstance()->assign('points',$points);
        \PHPEMSPRO\tpl::getInstance()->assign('point',$thispoint);
        \PHPEMSPRO\tpl::getInstance()->assign('index',$index+1);
        \PHPEMSPRO\tpl::getInstance()->display('point');
    }
}