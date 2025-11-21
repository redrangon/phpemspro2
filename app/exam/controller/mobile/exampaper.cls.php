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
use PHPEMSPRO\exam\model\examsession;
use PHPEMSPRO\exam\model\favor;
use PHPEMSPRO\exam\model\points;
use PHPEMSPRO\exam\model\question;
use PHPEMSPRO\route;

class exampaper extends mobile
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

    public function recover()
    {
        $paperid = \PHPEMSPRO\route::get('paperid');
        $examsession = examsession::getExamsessionById($this->subject['subjectdb'],$paperid);
        if($examsession)
        {
            examsession::delExamsession($this->subject['subjectdb'],$paperid);
            $args = $examsession['esdata'];
            unset($args['token']);
            $args['time'] = $args['time'] + intval((TIME - strtotime($examsession['estime']))/60);
            $args['token'] = md5(serialize($args));
            $args['sign'] = uniqid();
            $token = $args['token'].'-'.$args['sign'];
            $sign = md5($args['token'].'-'.$args['sign'].'-'.$this->_user['username'].'-'.\PHPEMSPRO\config::secretkey);
            \PHPEMSPRO\pedis::getInstance()->setHashData('examsession_'.$this->_user['username'],$token,json_encode($args));
            $message = array(
                'statusCode' => 200,
                'message' => '恢复成功！',
                "callbackType" => "forward",
                "forwardUrl" => "index.php?exam-mobile-exampaper-paper&token={$token}&sign={$sign}"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        else
        {
            $message = array(
                'statusCode' => 300,
                "message" => "非法操作"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
    }

    public function lefttime()
    {
        $token = \PHPEMSPRO\route::get('token');
        $paper = json_decode(\PHPEMSPRO\pedis::getInstance()->getHashData('examsession_'.$this->_user['username'],$token),true);
        $lefttime = 0;
        if($paper['starttime'])
        {
            $lefttime = TIME - $paper['starttime'];
        }
        echo $lefttime;
        exit;
    }

    public function saveanswer()
    {
        $token = \PHPEMSPRO\route::get('token');
        $sign = \PHPEMSPRO\route::get('sign');
        $paper = json_decode(\PHPEMSPRO\pedis::getInstance()->getHashData('examsession_'.$this->_user['username'],$token),true);
        if($sign != md5($paper['token'].'-'.$paper['sign'].'-'.$this->_user['username'].'-'.\PHPEMSPRO\config::secretkey))
        {
            $message = array(
                'statusCode' => 200,
                'message' => '非法操作！签名验证不通过！'
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        $answers = \PHPEMSPRO\route::get('answers');
        $paper['useranswer'] = json_decode(route::stripSlashes($answers),true);;
        \PHPEMSPRO\pedis::getInstance()->setHashData('examsession_'.$this->_user['username'],$token,json_encode($paper));
        $message = array(
            'statusCode' => 200
        );
        exit(json_encode($message));
    }

    public function save()
    {
        $token = \PHPEMSPRO\route::get('token');
        $sign = \PHPEMSPRO\route::get('sign');
        $answers = \PHPEMSPRO\route::get('answers');
        $question = json_decode(route::stripSlashes($answers),true);
        $paper = json_decode(\PHPEMSPRO\pedis::getInstance()->getHashData('examsession_'.$this->_user['username'],$token),true);
        $rs = question::submitpaper($this->subject['subjectdb'],$question,$paper,$this->_user['username']);
        \PHPEMSPRO\pedis::getInstance()->delHashData('examsession_'.$this->_user['username'],$token);
        if($rs['needteacher'])
        {
            $message = array(
                'statusCode' => 200,
                'message' => '保存成功！',
                "callbackType" => "forward",
                "forwardUrl" => "index.php?exam-mobile-history-decide&ehid=".$rs['ehid']
            );
        }
        else
        {
            $message = array(
                'statusCode' => 200,
                'message' => '保存成功！',
                "callbackType" => "forward",
                "forwardUrl" => "index.php?exam-mobile-history-detail&ehid=".$rs['ehid']
            );
        }
        \PHPEMSPRO\route::urlJump($message);
    }

    public function savesession()
    {
        $token = \PHPEMSPRO\route::get('token');
        $sign = \PHPEMSPRO\route::get('sign');
        $paper = json_decode(\PHPEMSPRO\pedis::getInstance()->getHashData('examsession_'.$this->_user['username'],$token),true);
        if(!$paper || ($sign != md5($paper['token'].'-'.$paper['sign'].'-'.$this->_user['username'].'-'.\PHPEMSPRO\config::secretkey)))
        {
            $message = array(
                'statusCode' => 200,
                'message' => '非法操作！'
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        $answers = \PHPEMSPRO\route::get('answers');
        $question = json_decode(route::stripSlashes($answers),true);
        $paper['useranswer'] = $question;
        \PHPEMSPRO\pedis::getInstance()->delHashData('examsession_'.$this->_user['username'],$token);
        $args = array(
            'esusername' => $this->_user['username'],
            'essubjectid' => $this->subject['subjectid'],
            'esbasicid' => $this->basic['basicid'],
            'espaperid' => $paper['key'],
            'estype' => 1,
            'estime' => TIME,
            'esdata' => $paper
        );
        examsession::addExamsession($this->subject['subjectdb'],$args);
        $message = array(
            'statusCode' => 200,
            'message' => '保存成功！',
            "callbackType" => "forward",
            "forwardUrl" => "back"
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function paper()
    {
        $token = \PHPEMSPRO\route::get('token');
        $sign = \PHPEMSPRO\route::get('sign');
        $paper = json_decode(\PHPEMSPRO\pedis::getInstance()->getHashData('examsession_'.$this->_user['username'],$token),true);
        if(!$paper || ($sign != md5($paper['token'].'-'.$paper['sign'].'-'.$this->_user['username'].'-'.\PHPEMSPRO\config::secretkey)))
        {
            $message = array(
                'statusCode' => 200,
                "callbackType" => "forward",
                "forwardUrl" => "back"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        $questypes = question::getQuestypesByArgs();
        $app = apps::getAppByCode('exam');
        $app['appsetting']['selectortype'] = explode(',',$app['appsetting']['selectortype']);
        if($paper['useranswer'])
        {
            \PHPEMSPRO\tpl::getInstance()->assign('useranswer',json_encode($paper['useranswer'],256));
        }
        \PHPEMSPRO\tpl::getInstance()->assign('setting',$app['appsetting']);
        \PHPEMSPRO\tpl::getInstance()->assign('savetime',rand(210,360)*1000);
        \PHPEMSPRO\tpl::getInstance()->assign('fixtime',rand(120,240)*1000);
        \PHPEMSPRO\tpl::getInstance()->assign('paper',$paper);
        \PHPEMSPRO\tpl::getInstance()->assign('token',$token);
        \PHPEMSPRO\tpl::getInstance()->assign('sign',$sign);
        \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
        \PHPEMSPRO\tpl::getInstance()->display('exampaper_paper');
    }

    public function selectquestions()
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

        $paperid = \PHPEMSPRO\route::get('paperid');
        $paper = exams::getPaperById($this->subject['subjectdb'],$paperid);
        examsession::delExamsessions($this->subject['subjectdb'],array(
            array("AND","esusername = :esusername","esusername",$this->_user['username']),
            array("AND","essubjectid = :essubjectid","essubjectid",$this->subject['subjectid']),
            array("AND","esbasicid = :esbasicid","esbasicid",$this->basic['basicid']),
            array("AND","espaperid = :espaperid","espaperid",$paperid),
            array("AND","estype = 1")
        ));
        if($paper['papertype'] == 1)
        {
            $args = array();
            $points = array();
            foreach($this->basic['basicpoints'] as $section)
            {
                foreach($section as $point)
                {
                    $points[] = $point;
                }
            }
            $questionids = question::selectquestions($this->subject['subjectdb'],$paper,$points);
            $questions = array();
            $questionrows = array();
            $number = array();
            foreach($questionids['ids'] as $key => $p)
            {
                if(count($p))
                {
                    $questions[$key] = question::getQuestionsByArgs($this->subject['subjectdb'], array(array("AND", "questionid in (:questionid)","questionid",$p), array("AND", "questionstatus = 1")));
                    $number[$key] = count($questions[$key]);
                }
            }
            foreach($questionids['qrids'] as $key => $p)
            {
                foreach($p as $qrid)
                {
                    $qr = question::getQuestionRowsById($this->subject['subjectdb'],$qrid);
                    $number[$key] += $qr['qrnumber'];
                    if($number[$key] >= $paper['papersetting']['questype'][$key]['number'])
                    {
                        $t = $number[$key] - $paper['papersetting']['questype'][$key]['number'];
                        while($t > 0)
                        {
                            array_pop($qr['data']);
                            $t--;
                        }
                        $questionrows[$key][$qrid] = $qr;
                        break;
                    }
                    $questionrows[$key][$qrid] = $qr;
                }
            }
            $args['question'] = array('questions'=>$questions,'questionrows'=>$questionrows);
            unset($paper['paperquestions']);
            $args['setting'] = $paper;
            $args['starttime'] = TIME;
            $args['name'] = $paper['papername'];
            $args['score'] = 0;
            $args['useranswer'] = '';
            $args['scorelist'] = '';
            $args['sign'] = '';
            $args['time'] = $paper['papersetting']['papertime'];
            $args['status'] = 0;
            $args['type'] = 1;
            $args['key'] = $paper['paperid'];
            $args['basic'] = $this->basic['basicid'];
            $args['username'] = $this->_user['username'];
            $args['token'] = md5(serialize($args));
            $args['sign'] = uniqid();
        }
        elseif($paper['papertype'] == 2)
        {
            $args = array();
            $questions = array();
            $questionrows = array();
            foreach($paper['paperquestions'] as $key => $p)
            {
                $qids = '';
                $qrids = '';
                if($p['questions'])$qids = implode(',',explode(',',trim($p['questions']," ,")));
                if($qids)
                {
                    $questions[$key] = question::getQuestionsByArgs($this->subject['subjectdb'],array(array("AND","questionid in ({$qids})"),array("AND","questionstatus = 1")));;
                }
                if($p['questionrows'])$qrids = implode(',',explode(',',trim($p['questionrows']," ,")));
                if($qrids)
                {
                    $qrids = explode(",",$qrids);
                    foreach($qrids as $t)
                    {
                        $qr = question::getQuestionRowsById($this->subject['subjectdb'],$t);
                        if($qr)
                        {
                            $questionrows[$key][$t] = $qr;
                        }
                    }
                }
            }
            $args['question'] = array('questions'=>$questions,'questionrows'=>$questionrows);
            unset($paper['paperquestions']);
            $args['setting'] = $paper;
            $args['starttime'] = TIME;
            $args['name'] = $paper['papername'];
            $args['score'] = 0;
            $args['useranswer'] = '';
            $args['scorelist'] = '';
            $args['sign'] = '';
            $args['time'] = $paper['papersetting']['papertime'];
            $args['status'] = 0;
            $args['type'] = 1;
            $args['key'] = $paper['paperid'];
            $args['basic'] = $this->basic['basicid'];
            $args['username'] = $this->_user['username'];
            $args['token'] = md5(serialize($args));
            $args['sign'] = uniqid();
        }
        elseif($paper['papertype'] == 3)
        {
            $args = array();
            $args['question'] = $paper['paperquestions'];
            unset($paper['paperquestions']);
            $args['setting'] = $paper;
            $args['starttime'] = TIME;
            $args['name'] = $paper['papername'];
            $args['score'] = 0;
            $args['useranswer'] = '';
            $args['scorelist'] = '';
            $args['sign'] = '';
            $args['time'] = $paper['papersetting']['papertime'];
            $args['status'] = 0;
            $args['type'] = 1;
            $args['key'] = $paper['paperid'];
            $args['basic'] = $this->basic['basicid'];
            $args['username'] = $this->_user['username'];
            $args['token'] = md5(serialize($args));
            $args['sign'] = uniqid();
        }
        $token = $args['token'].'-'.$args['sign'];
        $sign = md5($args['token'].'-'.$args['sign'].'-'.$this->_user['username'].'-'.\PHPEMSPRO\config::secretkey);
        \PHPEMSPRO\pedis::getInstance()->setHashData('examsession_'.$this->_user['username'],$token,json_encode($args));
        $message = array(
            'statusCode' => 200,
            "callbackType" => 'forward',
            "forwardUrl" => "index.php?exam-mobile-exampaper-paper&token={$token}&sign={$sign}"
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function index()
    {
        $paperids = $this->basic['basicexam']['auto'];
        if($paperids)
        {
            $examsessions = examsession::getExamsessionsByArgs($this->subject['subjectdb'],array(
                array("AND","esusername = :esusername","esusername",$this->_user['username']),
                array("AND","essubjectid = :essubjectid","essubjectid",$this->subject['subjectid']),
                array("AND","esbasicid = :esbasicid","esbasicid",$this->basic['basicid']),
                array("AND","find_in_set(espaperid,:espaperid)","espaperid",$paperids),
                array("AND","estype = 1")
            ));
            $args = array();
            $args[] = array("AND","find_in_set(paperid,:paperid)","paperid",$paperids);
            $papers = exams::getPapersByArgs($this->subject['subjectdb'],$args);
            \PHPEMSPRO\tpl::getInstance()->assign('examsessions',$examsessions);
            \PHPEMSPRO\tpl::getInstance()->assign('papers',$papers);
        }
        \PHPEMSPRO\tpl::getInstance()->display('exampaper');
    }
}