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

class favor extends app
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
    }

    public function save()
    {
        $pointid = \PHPEMSPRO\route::get('pointid');
        $question = \PHPEMSPRO\route::get('question');
        $lastquestion = \PHPEMSPRO\route::get('lastquestion');
        \PHPEMSPRO\pedis::getInstance()->setHashData('process',$this->_user['username'].'_'.$this->subject['subjectdb'].'_'.$pointid,$lastquestion);
        \PHPEMSPRO\pedis::getInstance()->setHashData('lastquestion',$this->_user['username'].'_'.$this->currentsubject,json_encode(array($pointid => $lastquestion)));
        \PHPEMSPRO\exam\model\favor::saveRecord($this->subject['subjectdb'],$this->_user['username'],$pointid,$question);
        $message = array(
            'statusCode' => 200,
            'message' => '保存成功！',
            'callback' => 'pep.goPrePage'
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function notepaper()
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
        $point = points::getPointById($pointid);
        $section = points::getSectionById($point['pointsection']);
        $parent = array();
        $questions = \PHPEMSPRO\exam\model\favor::getNoteQuestionsByPointid($this->subject['subjectdb'],$this->_user['username'],$pointid,'questionid');
        $ids = array();
        foreach($questions as $key => $p)
        {
            $ids[] = $p['questionid'];
        }
        $allnumber = count($questions);
        if($number < 1)$number = 1;
        if($number > $allnumber)$number = $allnumber;
        $questionid = $questions[$number - 1]['questionid'];
        $question = question::getQuestionById($this->subject['subjectdb'],$questionid);
        if($question['questionparent'])
        {
            if(!$parent[$question['questionparent']])
            {
                $parent[$question['questionparent']] = question::getQuestionRowsById($this->subject['subjectdb'],$question['questionparent'],false);
            }
        }
        $questypes = question::getQuestypesByArgs();
        $app = apps::getAppByCode('exam');
        $app['appsetting']['selectortype'] = explode(',',$app['appsetting']['selectortype']);
        $favors = \PHPEMSPRO\exam\model\favor::getFavorByQuestionIds($this->subject['subjectdb'],$ids,$this->_user['username']);
        $note = \PHPEMSPRO\exam\model\favor::getNoteByUserAndQuestionid($this->subject['subjectdb'],$this->_user['username'],$questionid);
        \PHPEMSPRO\tpl::getInstance()->assign('section',$section);
        \PHPEMSPRO\tpl::getInstance()->assign('note',$note);
        \PHPEMSPRO\tpl::getInstance()->assign('setting',$app['appsetting']);
        \PHPEMSPRO\tpl::getInstance()->assign('point',$point);
        \PHPEMSPRO\tpl::getInstance()->assign('favors',$favors);
        \PHPEMSPRO\tpl::getInstance()->assign('parent',$parent);
        \PHPEMSPRO\tpl::getInstance()->assign('number',$number);
        \PHPEMSPRO\tpl::getInstance()->assign('allnumber',$allnumber);
        \PHPEMSPRO\tpl::getInstance()->assign('section',$section);
        \PHPEMSPRO\tpl::getInstance()->assign('question',$question);
        \PHPEMSPRO\tpl::getInstance()->assign('questions',$questions);
        \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
        \PHPEMSPRO\tpl::getInstance()->display('note_paper');
    }

    public function wrongpaper()
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
        $point = points::getPointById($pointid);
        $section = points::getSectionById($point['pointsection']);
        $record = \PHPEMSPRO\exam\model\favor::getRecordSession($this->subject['subjectdb'],$this->_user['username']);
        $wrong = $record['recordwrong'][$pointid];
        unset($record);
        $ids = array();
        $useranswer = array();
        foreach($wrong as $key => $p)
        {
            $ids[] = $key;
            $useranswer[$key] = $p;
        }
        $ids = implode(',',$ids);
        $parent = array();
        $args = array(array("AND","questionid in ({$ids})"),array("AND","questionstatus = 1"));
        $questions = question::getQuestionsByArgs($this->subject['subjectdb'],$args,"questionparent asc,questionorder asc,questionid desc",'questionid');
        $allnumber = count($questions);
        if($number < 1)$number = 1;
        if($number > $allnumber)$number = $allnumber;
        $questionid = $questions[$number - 1]['questionid'];
        $question = question::getQuestionById($this->subject['subjectdb'],$questionid);
        if($question['questionparent'])
        {
            if(!$parent[$question['questionparent']])
            {
                $parent[$question['questionparent']] = question::getQuestionRowsById($this->subject['subjectdb'],$question['questionparent'],false);
            }
        }
        $questypes = question::getQuestypesByArgs();
        $app = apps::getAppByCode('exam');
        $app['appsetting']['selectortype'] = explode(',',$app['appsetting']['selectortype']);
        $favors = \PHPEMSPRO\exam\model\favor::getFavorByQuestionIds($this->subject['subjectdb'],$ids,$this->_user['username']);
        $note = \PHPEMSPRO\exam\model\favor::getNoteByUserAndQuestionid($this->subject['subjectdb'],$this->_user['username'],$questionid);
        \PHPEMSPRO\tpl::getInstance()->assign('section',$section);
        \PHPEMSPRO\tpl::getInstance()->assign('note',$note);
        \PHPEMSPRO\tpl::getInstance()->assign('setting',$app['appsetting']);
        \PHPEMSPRO\tpl::getInstance()->assign('useranswer',$useranswer);
        \PHPEMSPRO\tpl::getInstance()->assign('point',$point);
        \PHPEMSPRO\tpl::getInstance()->assign('number',$number);
        \PHPEMSPRO\tpl::getInstance()->assign('allnumber',$allnumber);
        \PHPEMSPRO\tpl::getInstance()->assign('favors',$favors);
        \PHPEMSPRO\tpl::getInstance()->assign('parent',$parent);
        \PHPEMSPRO\tpl::getInstance()->assign('question',$question);
        \PHPEMSPRO\tpl::getInstance()->assign('questions',$questions);
        \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
        \PHPEMSPRO\tpl::getInstance()->display('wrong_paper');
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
                    "forwardUrl" => "index.php?exam-app-basic-open"
                );
                \PHPEMSPRO\route::urlJump($message);
            }
        }
        $number = \PHPEMSPRO\route::get('number');
        $allnumber = intval(\PHPEMSPRO\pedis::getInstance()->getHashData('favornumber',$this->_user['username'].'-'.$this->subject['subjectdb'].'-'.$pointid));
        if($number < 1)$number = 1;
        if($number > $allnumber)$number = $allnumber;
        $point = points::getPointById($pointid);
        $section = points::getSectionById($point['pointsection']);
        $parent = array();
        $questions = \PHPEMSPRO\exam\model\favor::getFavorQuestionsByPointid($this->subject['subjectdb'],$this->_user['username'],$pointid,'questionid');
        $ids = array();
        foreach($questions as $p)
        {
            $ids[] = $p['questionid'];
        }
        $question = question::getQuestionById($this->subject['subjectdb'],$ids[$number - 1]);
        if($question['questionparent'])
        {
            if(!$parent[$question['questionparent']])
            {
                $parent[$question['questionparent']] = question::getQuestionRowsById($this->subject['subjectdb'],$question['questionparent'],false);
            }
        }
        $record = \PHPEMSPRO\exam\model\favor::getRecordSession($this->subject['subjectdb'],$this->_user['username']);
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
        $questypes = question::getQuestypesByArgs();
        $app = apps::getAppByCode('exam');
        $app['appsetting']['selectortype'] = explode(',',$app['appsetting']['selectortype']);
        $note = \PHPEMSPRO\exam\model\favor::getNoteByUserAndQuestionid($this->subject['subjectdb'],$this->_user['username'],$questionid);
        \PHPEMSPRO\tpl::getInstance()->assign('section',$section);
        \PHPEMSPRO\tpl::getInstance()->assign('note',$note);
        \PHPEMSPRO\tpl::getInstance()->assign('setting',$app['appsetting']);
        \PHPEMSPRO\tpl::getInstance()->assign('useranswer',$useranswer);
        \PHPEMSPRO\tpl::getInstance()->assign('number',$number);
        \PHPEMSPRO\tpl::getInstance()->assign('allnumber',$allnumber);
        \PHPEMSPRO\tpl::getInstance()->assign('point',$point);
        \PHPEMSPRO\tpl::getInstance()->assign('parent',$parent);
        \PHPEMSPRO\tpl::getInstance()->assign('question',$question);
        \PHPEMSPRO\tpl::getInstance()->assign('questions',$questions);
        \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
        \PHPEMSPRO\tpl::getInstance()->display('favor_paper');
    }

    public function index()
    {
        $points = points::getPointsBySubjectid($this->subject['subjectid']);
        $sections = $points['sections'];
        $points = $points['points'];
        $record = \PHPEMSPRO\exam\model\favor::getRecordSession($this->subject['subjectdb'],$this->_user['username']);
        $wrong = array();
        $right = array();
        $favor = array();
        $note = array();
        $numbers = array();
        foreach($points as $key => $point)
        {
            foreach($point as $p)
            {
                $numbers[$p['pointid']] = question::getQuestionNumberByPointid($this->subject['subjectdb'],$p['pointid']);
                $favor[$p['pointid']] = intval(\PHPEMSPRO\pedis::getInstance()->getHashData('favornumber',$this->_user['username'].'-'.$this->subject['subjectdb'].'-'.$p['pointid']));
                $note[$p['pointid']] = intval(\PHPEMSPRO\pedis::getInstance()->getHashData('notenumber',$this->_user['username'].'-'.$this->subject['subjectdb'].'-'.$p['pointid']));
                $wrong[$p['pointid']] = intval($record['recordnumber'][$p['pointid']]['wrong']);
                $right[$p['pointid']] = intval($record['recordnumber'][$p['pointid']]['right']);
            }
        }
        unset($record);
        \PHPEMSPRO\tpl::getInstance()->assign('sections',$sections);
        \PHPEMSPRO\tpl::getInstance()->assign('wrong',$wrong);
        \PHPEMSPRO\tpl::getInstance()->assign('favor',$favor);
        \PHPEMSPRO\tpl::getInstance()->assign('note',$note);
        \PHPEMSPRO\tpl::getInstance()->assign('points',$points);
        \PHPEMSPRO\tpl::getInstance()->display('favor');
    }
}