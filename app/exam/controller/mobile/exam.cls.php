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
use PHPEMSPRO\files;
use PHPEMSPRO\route;

class exam extends mobile
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
        if(!$this->basic['basicexam']['model'])
        {
            $message = array(
                'statusCode' => 200,
                "callbackType" => "forward",
                "forwardUrl" => "index.php?exam-mobile-basic"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        $this->intime = true;
        if($this->basic['basicexam']['opentime']['start'] > TIME)
        {
            $this->intime = false;
        }
        if($this->basic['basicexam']['opentime']['end'] && ($this->basic['basicexam']['opentime']['end'] < TIME))
        {
            $this->intime = false;
        }
        \PHPEMSPRO\tpl::getInstance()->assign('intime',$this->intime);
        $this->maxnumber = true;
        if($this->basic['basicexam']['examnumber'])
        {
            $args = array();
            $args[] = array("AND","ehusername = :ehusername","ehusername",$this->_user['username']);
            $args[] = array("AND","ehbasicid = :ehbasicid","ehbasicid",$this->basic['basicid']);
            $args[] = array("AND","ehtype = 2");
            $number = favor::getExamHistoryNumber($this->subject['subjectdb'],$args);
            if($number >= $this->basic['basicexam']['examnumber'])
            {
                //$this->maxnumber = $number - $this->basic['basicexam']['examnumber'];
                $this->maxnumber = false;
            }
        }
        \PHPEMSPRO\tpl::getInstance()->assign('maxnumber',$this->maxnumber);
    }

    public function recover()
    {
        $args = array(
            array("AND","essubjectid = :essubjectid","essubjectid",$this->subject['subjectid']),
            array("AND","esbasicid = :esbasicid","esbasicid",$this->basic['basicid']),
            array("AND","estype = 2"),
            array("AND","esusername = :esusername","esusername",$this->_user['username'])
        );
        $examsession = examsession::getExamsessionByArgs($this->subject['subjectdb'],$args);
        if($examsession)
        {
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
                "forwardUrl" => "index.php?exam-mobile-exam-paper&token={$token}&sign={$sign}"
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

    public function cancel()
    {
        $args = array(
            array("AND","essubjectid = :essubjectid","essubjectid",$this->subject['subjectid']),
            array("AND","esbasicid = :esbasicid","esbasicid",$this->basic['basicid']),
            array("AND","estype = 2"),
            array("AND","esusername = :esusername","esusername",$this->_user['username'])
        );
        $examsession = examsession::getExamsessionByArgs($this->subject['subjectdb'],$args);
        if($examsession)
        {
            examsession::delExamsessions($this->subject['subjectdb'],$examsession['esid']);
        }
        $message = array(
            'statusCode' => 201,
            "callbackType" => "forward",
            "forwardUrl" => "index.php?exam-mobile-exam"
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function isintime()
    {
        if($this->intime)
        {
            $message = array(
                'statusCode' => 200,
                "callbackType" => "forward",
                "forwardUrl" => "reload"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        else
        {
            $message = array(
                'statusCode' => 200
            );
            \PHPEMSPRO\route::urlJump($message);
        }
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
                "forwardUrl" => "index.php?exam-phone-exam-detail&ehid=".$ehid
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
                    "forwardUrl" => "index.php?exam-phone-exam-history"
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
            \PHPEMSPRO\tpl::getInstance()->display('exam_decide');
        }
    }

    public function history()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page?$page:1;
        $args = array();
        $args[] = array("AND","ehbasicid = :basicid","basicid",$this->basic['basicid']);
        $args[] = array("AND","ehusername = :username","username",$this->_user['username']);
        $args[] = array("AND","ehtype = 2");
        $histories = favor::getExamHistoryList($this->subject['subjectdb'],$args,$page);
        \PHPEMSPRO\tpl::getInstance()->assign('histories',$histories);
        \PHPEMSPRO\tpl::getInstance()->assign('page',$page);
        \PHPEMSPRO\tpl::getInstance()->display('exam_history');
    }

    public function detail()
    {
        $ehid = \PHPEMSPRO\route::get('ehid');
        $history = favor::getExamHistoryById($this->subject['subjectdb'],$ehid);
        if(!$history['ehstatus'] || $this->basic['basicexam']['notviewscore'])
        {
            $message = array(
                'statusCode' => 200,
                "callbackType" => "forward",
                "forwardUrl" => "index.php?exam-mobile-exam-history"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
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
        \PHPEMSPRO\tpl::getInstance()->display('exam_detail');
    }

    public function lefttime()
    {
        $token = \PHPEMSPRO\route::get('token');
        $paper = json_decode(\PHPEMSPRO\pedis::getInstance()->getHashData('examsession_'.$this->_user['username'],$token),true);
        $lefttime = 0;
		$delaytime = intval(\PHPEMSPRO\pedis::getInstance()->getHashData('delaytime_'.$this->_user['username'],$token));
        if($paper['starttime'])
        {
            $lefttime = TIME - $paper['starttime'] - $delaytime;
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
                'statusCode' => 300,
                'message' => '非法操作！签名验证不通过！'
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        $answers = \PHPEMSPRO\route::get('answers');
        $paper['useranswer'] = json_decode(route::stripSlashes($answers),true);
        \PHPEMSPRO\pedis::getInstance()->setHashData('examsession_'.$this->_user['username'],$token,json_encode($paper));
        $args = array(
            array("AND","essubjectid = :essubjectid","essubjectid",$this->subject['subjectid']),
            array("AND","esbasicid = :esbasicid","esbasicid",$this->basic['basicid']),
            array("AND","estype = 2"),
            array("AND","esusername = :esusername","esusername",$this->_user['username']),
	        array("AND","estoken = :estoken","estoken",$token)
        );
        $examsession = examsession::getExamsessionByArgs($this->subject['subjectdb'],$args);
        if($examsession['esid'])
        {
            $args = array(
                'esdata' => $paper
            );
            examsession::modifyExamsessionById($this->subject['subjectdb'],$examsession['esid'],$args);
        }
        else
        {
            $args = array(
                'esusername' => $this->_user['username'],
                'essubjectid' => $this->subject['subjectid'],
                'esbasicid' => $this->basic['basicid'],
                'espaperid' => $paper['key'],
                'estype' => 2,
                'estime' => TIME,
                'esdata' => $paper,
		        'estoken' => $token
            );
            examsession::addExamsession($this->subject['subjectdb'],$args);
        }
        $message = array(
            'statusCode' => 200
        );
        exit(json_encode($message));
    }

    public function save()
    {
        if(!$this->maxnumber)
        {
            $message = array(
                'statusCode' => 300,
                'message' => '您已经完成考试了！'
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        $token = \PHPEMSPRO\route::get('token');
        $answers = \PHPEMSPRO\route::get('answers');
        $question = json_decode(route::stripSlashes($answers),true);
        $paper = json_decode(\PHPEMSPRO\pedis::getInstance()->getHashData('examsession_'.$this->_user['username'],$token),true);
        $rs = question::submitpaper($this->subject['subjectdb'],$question,$paper,$this->_user['username']);
        \PHPEMSPRO\pedis::getInstance()->delHashData('examsession_'.$this->_user['username'],$token);
        $args = array(
            array("AND","essubjectid = :essubjectid","essubjectid",$this->subject['subjectid']),
            array("AND","esbasicid = :esbasicid","esbasicid",$this->basic['basicid']),
            array("AND","estype = 2"),
            array("AND","esusername = :esusername","esusername",$this->_user['username']),
	        array("AND","estoken = :estoken","estoken",$token)
        );
        examsession::delExamsessions($this->subject['subjectdb'],$args);
        $message = array(
            'statusCode' => 200,
            'message' => '保存成功！',
            "callbackType" => "forward",
            "forwardUrl" => "index.php?exam-mobile-exam-detail&ehid=".$rs['ehid']
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
        \PHPEMSPRO\tpl::getInstance()->assign('fixtime',rand(120,240)*100);
        \PHPEMSPRO\tpl::getInstance()->assign('paper',$paper);
        \PHPEMSPRO\tpl::getInstance()->assign('token',$token);
        \PHPEMSPRO\tpl::getInstance()->assign('sign',$sign);
        \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
        \PHPEMSPRO\tpl::getInstance()->display('exam_paper');
    }

    public function selectquestions()
    {
        if(!$this->intime)
        {
            $message = array(
                'statusCode' => 300,
                'message' => '不在考试时间范围内，不能抽卷！'
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        if(!$this->maxnumber)
        {
            $message = array(
                'statusCode' => 300,
                'message' => '您已经完成考试了！'
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        if($this->basic['basicexam']['faceverify'])
        {
            $photo = \PHPEMSPRO\route::get('photo');
            if(!file_exists($this->_user['userface']))
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "该学生没有上传照片"
                );
                \PHPEMSPRO\route::urlJump($message);
            }
            else
            {
                $userphoto = $this->_user['userface'];
                if(!file_exists($userphoto))
                {
                    $message = array(
                        'statusCode' => 300,
                        "message" => "该学生没有上传照片"
                    );
                    \PHPEMSPRO\route::urlJump($message);
                }
                $photo = explode('base64,',$photo,2);
                $photo = files::saveBase64Img($photo[1]);
                $score = \PHPEMSPRO\face::verify($photo,$userphoto,'baidu');
                if(!$score)
                {
                    $message = array(
                        'statusCode' => 300,
                        "message" => "人脸对比不符，请重新验证"
                    );
                    \PHPEMSPRO\route::urlJump($message);
                }
            }
        }
		if($this->basic['basicexam']['opentime']['end'])
		{
			if(($this->basic['basicexam']['opentime']['end'] - TIME) < 300)
			{
				$message = array(
					'statusCode' => 300,
					'message' => '考试已经关闭！请联系管理员补考'
				);
				\PHPEMSPRO\route::urlJump($message);
			}
		}
        $paperid = \PHPEMSPRO\route::get('paperid');
        if($this->basic['basicexam']['selectrule'])
        {
            $ids = explode(',',trim($this->basic['basicexam']['self'],', '));
            $p = rand(0,count($ids)-1);
            $paperid = $ids[$p];
        }
        $paper = exams::getPaperById($this->subject['subjectdb'],$paperid);
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
                    $ids = implode(',', $p);
                    $questions[$key] = question::getQuestionsByArgs($this->subject['subjectdb'], array(array("AND", "questionid in ({$ids})"), array("AND", "questionstatus = 1")));
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
			if($this->basic['basicexam']['opentime']['end'])
			{
				$tmptime = intval(($this->basic['basicexam']['opentime']['end'] - TIME)/60)-5;
				if($tmptime < $args['time'])
				{
					$args['time'] = $tmptime;
					if($args['time'] < 0)$args['time'] = 0;
				}
			}
            $args['status'] = 0;
            $args['type'] = 2;
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
			if($this->basic['basicexam']['opentime']['end'])
			{
				$tmptime = intval(($this->basic['basicexam']['opentime']['end'] - TIME)/60)-5;
				if($tmptime > 0 && $tmptime < $args['time'])
				{
					$args['time'] = $tmptime;
				}
			}
            $args['status'] = 0;
            $args['type'] = 2;
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
            if($this->basic['basicexam']['opentime']['end'])
            {
                $tmptime = intval(($this->basic['basicexam']['opentime']['end'] - TIME)/60);
                if($tmptime > 0 && $tmptime < $args['time'])
                {
                    $args['time'] = $tmptime;
                }
            }
            $args['status'] = 0;
            $args['type'] = 2;
            $args['key'] = $paper['paperid'];
            $args['basic'] = $this->basic['basicid'];
            $args['username'] = $this->_user['username'];
            $args['token'] = md5(serialize($args));
            $args['sign'] = uniqid();
        }
        else
        {
            $message = array(
                'statusCode' => 300,
                'message' => '配置错误，请联系管理员！'
            );
            \PHPEMSPRO\route::urlJump($message);
        }
	    $token = $args['token'].'-'.$args['sign'];
        $sign = md5($args['token'].'-'.$args['sign'].'-'.$this->_user['username'].'-'.\PHPEMSPRO\config::secretkey);
        \PHPEMSPRO\pedis::getInstance()->setHashData('examsession_'.$this->_user['username'],$token,json_encode($args));
        $message = array(
            'statusCode' => 200,
            "callbackType" => 'forward',
            "forwardUrl" => "index.php?exam-mobile-exam-paper&token={$token}&sign={$sign}"
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function index()
    {
        $paperids = $this->basic['basicexam']['self'];
        if($paperids)
        {
            $args = array();
            $args[] = array("AND","find_in_set(paperid,:paperid)","paperid",$paperids);
            $papers = exams::getPapersByArgs($this->subject['subjectdb'],$args);
            \PHPEMSPRO\tpl::getInstance()->assign('papers',$papers);
        }
        $args = array(
            array("AND","essubjectid = :essubjectid","essubjectid",$this->subject['subjectid']),
            array("AND","esbasicid = :esbasicid","esbasicid",$this->basic['basicid']),
            array("AND","estype = 2"),
            array("AND","esusername = :esusername","esusername",$this->_user['username'])
        );
        $examsession = examsession::getExamsessionByArgs($this->subject['subjectdb'],$args);
        \PHPEMSPRO\tpl::getInstance()->assign('examsession',$examsession);
        if($this->basic['basicexam']['faceverify'])
        {
            \PHPEMSPRO\tpl::getInstance()->assign('isios',route::isIos());
            \PHPEMSPRO\tpl::getInstance()->display('exam_verify');
        }
        else
        {
            \PHPEMSPRO\tpl::getInstance()->display('exam');
        }
    }
}