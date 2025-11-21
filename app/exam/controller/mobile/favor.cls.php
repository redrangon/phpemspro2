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
use PHPEMSPRO\exam\model\points;
use PHPEMSPRO\exam\model\question;

class favor extends mobile
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
        $point = points::getPointById($pointid);
        $parent = array();
        $questions = \PHPEMSPRO\exam\model\favor::getNoteQuestionsByPointid($this->subject['subjectdb'],$this->_user['username'],$pointid);
        $ids = array();
        foreach($questions as $key => $question)
        {
            $ids[] = $question['questionid'];
            if($question['questionparent'])
            {
                if(!$parent[$question['questionparent']])
                {
                    $parent[$question['questionparent']] = question::getQuestionRowsById($this->subject['subjectdb'],$question['questionparent'],false);
                }
            }
        }
        $ids = implode(',',$ids);
        $questypes = question::getQuestypesByArgs();
        $app = apps::getAppByCode('exam');
        $app['appsetting']['selectortype'] = explode(',',$app['appsetting']['selectortype']);
        $favors = \PHPEMSPRO\exam\model\favor::getFavorByQuestionIds($this->subject['subjectdb'],$ids,$this->_user['username']);
        \PHPEMSPRO\tpl::getInstance()->assign('setting',$app['appsetting']);
        \PHPEMSPRO\tpl::getInstance()->assign('point',$point);
        \PHPEMSPRO\tpl::getInstance()->assign('favors',$favors);
        \PHPEMSPRO\tpl::getInstance()->assign('parent',$parent);
        \PHPEMSPRO\tpl::getInstance()->assign('questions',$questions);
        \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
        \PHPEMSPRO\tpl::getInstance()->display('favor_notepaper');
    }

    public function wrongpaper()
    {
        $pointid = \PHPEMSPRO\route::get('pointid');
        $point = points::getPointById($pointid);
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
        $favors = \PHPEMSPRO\exam\model\favor::getFavorByQuestionIds($this->subject['subjectdb'],$ids,$this->_user['username']);
        \PHPEMSPRO\tpl::getInstance()->assign('setting',$app['appsetting']);
        \PHPEMSPRO\tpl::getInstance()->assign('useranswer',$useranswer);
        \PHPEMSPRO\tpl::getInstance()->assign('point',$point);
        \PHPEMSPRO\tpl::getInstance()->assign('favors',$favors);
        \PHPEMSPRO\tpl::getInstance()->assign('parent',$parent);
        \PHPEMSPRO\tpl::getInstance()->assign('questions',$questions);
        \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
        \PHPEMSPRO\tpl::getInstance()->display('favor_wrongpaper');
    }

    public function paper()
    {
        $pointid = \PHPEMSPRO\route::get('pointid');
        $point = points::getPointById($pointid);
        $parent = array();
        $questions = \PHPEMSPRO\exam\model\favor::getFavorQuestionsByPointid($this->subject['subjectdb'],$this->_user['username'],$pointid);
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
        \PHPEMSPRO\tpl::getInstance()->assign('setting',$app['appsetting']);
        \PHPEMSPRO\tpl::getInstance()->assign('point',$point);
        \PHPEMSPRO\tpl::getInstance()->assign('parent',$parent);
        \PHPEMSPRO\tpl::getInstance()->assign('questions',$questions);
        \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
        \PHPEMSPRO\tpl::getInstance()->display('favor_paper');
    }

    public function note()
    {
        $sectionid = \PHPEMSPRO\route::get('sectionid');
        $points = points::getPointsBySubjectid($this->subject['subjectid']);
        $sections = $points['sections'];
        $points = $points['points'];
        $note = array();
        foreach ($points[$sectionid] as $point)
        {
            $note[$point['pointid']] += intval(\PHPEMSPRO\pedis::getInstance()->getHashData('notenumber',$this->_user['username'].'-'.$this->subject['subjectdb'].'-'.$point['pointid']));
        }
        \PHPEMSPRO\tpl::getInstance()->assign('note',$note);
        \PHPEMSPRO\tpl::getInstance()->assign('sectionid',$sectionid);
        \PHPEMSPRO\tpl::getInstance()->assign('points',$points[$sectionid]);
        \PHPEMSPRO\tpl::getInstance()->display('favor_note');
    }

    public function wrong()
    {
        $sectionid = \PHPEMSPRO\route::get('sectionid');
        $points = points::getPointsBySubjectid($this->subject['subjectid']);
        $sections = $points['sections'];
        $points = $points['points'];
        $record = \PHPEMSPRO\exam\model\favor::getRecordSession($this->subject['subjectdb'],$this->_user['username']);
        $wrong = array();
        foreach ($points[$sectionid] as $point)
        {
            $wrong[$point['pointid']] = intval($record['recordnumber'][$point['pointid']]['wrong']);
        }
        unset($record);
        \PHPEMSPRO\tpl::getInstance()->assign('wrong',$wrong);
        \PHPEMSPRO\tpl::getInstance()->assign('sectionid',$sectionid);
        \PHPEMSPRO\tpl::getInstance()->assign('points',$points[$sectionid]);
        \PHPEMSPRO\tpl::getInstance()->display('favor_wrong');
    }

    public function index()
    {
        $sectionid = \PHPEMSPRO\route::get('sectionid');
        $points = points::getPointsBySubjectid($this->subject['subjectid']);
        $sections = $points['sections'];
        $points = $points['points'];
        $favor = array();
        foreach ($points[$sectionid] as $point)
        {
            $favor[$point['pointid']] += intval(\PHPEMSPRO\pedis::getInstance()->getHashData('favornumber',$this->_user['username'].'-'.$this->subject['subjectdb'].'-'.$point['pointid']));
        }
        \PHPEMSPRO\tpl::getInstance()->assign('favor',$favor);
        \PHPEMSPRO\tpl::getInstance()->assign('sectionid',$sectionid);
        \PHPEMSPRO\tpl::getInstance()->assign('points',$points[$sectionid]);
        \PHPEMSPRO\tpl::getInstance()->display('favor');
    }
}