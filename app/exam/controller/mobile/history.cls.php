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

class history extends mobile
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

    public function view()
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
        $ehid = \PHPEMSPRO\route::get('ehid');
        $questypes = question::getQuestypesByArgs();
        $history = favor::getExamHistoryById($this->subject['subjectdb'],$ehid);
        foreach($history['ehuseranswer'] as $key => $p)
        {
            if(is_array($p))
            $history['ehuseranswer'][$key] = implode('',$p);
        }
        $ids = array();
        foreach($history['ehquestion']['questions'] as $key => $ps)
        {
            foreach($ps as $q)
            {
                $ids[] = $q['questionid'];
            }
			$history['ehquestion']['questions'][$key] = question::formatQuestions($ps,$this->subject['subjectmodel']);
        }
        foreach($history['ehquestion']['questionrows'] as $key => $prs)
        {
            foreach($prs as $pr)
            {
                foreach($pr['data'] as $q)
                {
                    $ids[] = $q['questionid'];
                }
				$history['ehquestion']['questionrows'][$key][$nkey]['data'] = question::formatQuestions($pr['data'],$this->subject['subjectmodel']);
            }
        }
        $ids = implode(',',$ids);
        $app = apps::getAppByCode('exam');
        $app['appsetting']['selectortype'] = explode(',',$app['appsetting']['selectortype']);
        $favors = favor::getFavorByQuestionIds($this->subject['subjectdb'],$ids,$this->_user['username']);
        \PHPEMSPRO\tpl::getInstance()->assign('favors',$favors);
        \PHPEMSPRO\tpl::getInstance()->assign('setting',$app['appsetting']);
        \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
        \PHPEMSPRO\tpl::getInstance()->assign('history',$history);
        \PHPEMSPRO\tpl::getInstance()->display('history_view');
    }

    public function decide()
    {
        $ehid = \PHPEMSPRO\route::get('ehid');
        $history = favor::getExamHistoryById($this->subject['subjectdb'],$ehid);
        if(\PHPEMSPRO\route::get('makedecide'))
        {
            $score = \PHPEMSPRO\route::get('score');
            foreach($score as $key => $p)
            {
                $history['ehscorelist'][$key] = floatval($p);
            }
            $score = array_sum($history['ehscorelist']);
            favor::modifyExamHistory($this->subject['subjectdb'],$ehid,array('ehscorelist' => $history['ehscorelist'],'ehscore' => $score,'ehstatus' => 1));
            $message = array(
                'statusCode' => 200,
                'message' => '评分完成',
                "callbackType" => "forward",
                "forwardUrl" => "index.php?exam-mobile-history-detail&ehid=".$ehid
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        else
        {
            if($history['ehsetting']['paperdecider'])
            {
                $message = array(
                    'statusCode' => 200,
                    'message' => '本试卷需要教师评分后才能展示',
                    "callbackType" => "forward",
                    "forwardUrl" => "index.php?exam-mobile-history"
                );
                \PHPEMSPRO\route::urlJump($message);
            }
            if($history['ehstatus'])
            {
                $message = array(
                    'statusCode' => 200,
                    "callbackType" => "forward",
                    "forwardUrl" => "back"
                );
                \PHPEMSPRO\route::urlJump($message);
            }
            $questypes = question::getQuestypesByArgs();
            $needdecide = array();
            foreach($history['ehquestion']['questions'] as $key => $p)
            {
                if($questypes[$key]['questsort'])
                {
                    $needdecide[$key] = 1;
                }
            }
            foreach($history['ehquestion']['questionrows'] as $key => $p)
            {
                if($questypes[$key]['questsort'])
                {
                    $needdecide[$key] = 1;
                }
                else
                {
                    foreach($p as $q)
                    {
                        foreach($q['data'] as $qd)
                        {
                            if($questypes[$qd['questype']]['questsort'])
                            {
                                $needdecide[$key] = 1;
                            }
                        }
                    }
                }
            }
            $app = apps::getAppByCode('exam');
            $app['appsetting']['selectortype'] = explode(',',$app['appsetting']['selectortype']);
            \PHPEMSPRO\tpl::getInstance()->assign('setting',$app['appsetting']);
            \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
            \PHPEMSPRO\tpl::getInstance()->assign('needdecide',$needdecide);
            \PHPEMSPRO\tpl::getInstance()->assign('history',$history);
            \PHPEMSPRO\tpl::getInstance()->display('history_decide');
        }
    }

    public function detail()
    {
        $ehid = \PHPEMSPRO\route::get('ehid');
        $history = favor::getExamHistoryById($this->subject['subjectdb'],$ehid);
        $questypes = question::getQuestypesByArgs();
        $number = array();
        $right = array();
        $score = array();
        $allnumber = 0;
        $allright = 0;
        $qids = array();
        $qrids = array();
        foreach($questypes as $key => $q)
        {
            $number[$key] = 0;
            $right[$key] = 0;
            $score[$key] = 0;
            if($history['ehquestion']['questions'][$key])
            {
                foreach($history['ehquestion']['questions'][$key] as $p)
                {
                    $number[$key]++;
                    $allnumber++;
                    if($history['ehscorelist'][$p['questionid']] == $history['ehsetting']['papersetting']['questype'][$key]['score'])
                    {
                        $right[$key]++;
                        $allright++;
                    }
                    $score[$key] = $score[$key] + $history['ehscorelist'][$p['questionid']];
                    $qids[] = $p['questionid'];
                }
            }
            if($history['ehquestion']['questionrows'][$key])
            {
                foreach($history['ehquestion']['questionrows'][$key] as $v)
                {
                    $qrids[] = $v['qrid'];
                    foreach($v['data'] as $p)
                    {
                        $qids[] = $p['questionid'];
                        $number[$key]++;
                        $allnumber++;
                        if($history['ehscorelist'][$p['questionid']] == $history['ehsetting']['papersetting']['questype'][$key]['score'])
                        {
                            $right[$key]++;
                            $allright++;
                        }
                        $score[$key] = $score[$key]+$history['ehscorelist'][$p['questionid']];
                        if($number[$key] == $history['ehsetting']['papersetting']['questype'][$key]['number'])break;
                    }
                }
            }
        }
        \PHPEMSPRO\tpl::getInstance()->assign('allright',$allright);
        \PHPEMSPRO\tpl::getInstance()->assign('allnumber',$allnumber);
        \PHPEMSPRO\tpl::getInstance()->assign('right',$right);
        \PHPEMSPRO\tpl::getInstance()->assign('score',$score);
        \PHPEMSPRO\tpl::getInstance()->assign('number',$number);
        \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
        \PHPEMSPRO\tpl::getInstance()->assign('history',$history);
        \PHPEMSPRO\tpl::getInstance()->display('history_detail');
    }

    public function history()
    {
        $page = \PHPEMSPRO\route::get('page');
        $type = \PHPEMSPRO\route::get('type');
        if(!$type)$type = 1;
        $page = $page?$page:1;
        $args = array();
        $args[] = array("AND","ehbasicid = :basicid","basicid",$this->basic['basicid']);
        $args[] = array("AND","ehusername = :username","username",$this->_user['username']);
        $args[] = array("AND","ehtype = :ehtype","ehtype",$type);
        $histories = favor::getExamHistoryList($this->subject['subjectdb'],$args,$page);
        \PHPEMSPRO\tpl::getInstance()->assign('histories',$histories);
        \PHPEMSPRO\tpl::getInstance()->assign('type',$type);
        \PHPEMSPRO\tpl::getInstance()->assign('page',$page);
        \PHPEMSPRO\tpl::getInstance()->display('history_history');
    }

    public function index()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page?$page:1;
        $args = array();
        $args[] = array("AND","ehbasicid = :basicid","basicid",$this->basic['basicid']);
        $args[] = array("AND","ehusername = :username","username",$this->_user['username']);
        $histories = favor::getExamHistoryList($this->subject['subjectdb'],$args,$page);
        \PHPEMSPRO\tpl::getInstance()->assign('histories',$histories);
        \PHPEMSPRO\tpl::getInstance()->assign('page',$page);
        \PHPEMSPRO\tpl::getInstance()->display('history');
    }
}