<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\exam\controller\master;

use PHPEMSPRO\core\model\apps;
use PHPEMSPRO\exam\master;
use PHPEMSPRO\exam\model\examsession;
use PHPEMSPRO\exam\model\faq;
use PHPEMSPRO\exam\model\favor;
use PHPEMSPRO\exam\model\points;
use PHPEMSPRO\exam\model\question;
use PHPEMSPRO\exam\model\training;
use PHPEMSPRO\route;
use PHPEMSPRO\tpl;
use PHPEMSPRO\user\model\msg;
use PHPEMSPRO\user\model\users;

class exams extends master
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

    public function answerfaq()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $faqid = \PHPEMSPRO\route::get('faqid');
        $faq = faq::getFaqById($subject['subjectdb'],$faqid);
        if(\PHPEMSPRO\route::get('answerfaq'))
        {
            $args = \PHPEMSPRO\route::get('args');
            $args['faqteacher'] = $this->_user['username'];
            $args['faqanswertime'] = TIME;
            faq::modifyFaq($subject['subjectdb'],$faqid,$args);
            $args = array(
                'msgtitle' => "提问回答通知",
                'msgcontent' => "您提问的《{$subject['subjectname']}》科目下ID为{$faq['faqquestionid']}的试题已经被教师回复",
                'msgposter' => $this->_user['username'],
                'msgurl' => 'index.php?user-auto-msg-faq&subjectid='.$subject['subjectid'].'&faqid='.$faqid
            );
            $msgid = msg::addMsg($args);
            $args = array(
                'msrusername' => $faq['faqusername'],
                'msrmsgid' => $msgid,
                'msrstatus' => 0
            );
            msg::addMsgUser($args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "reload"
            );
            exit(json_encode($message));
        }
        else
        {
            $question = question::getQuestionById($subject['subjectdb'],$faq['faqquestionid']);
            $parent = question::getQuestionRowsById($subject['subjectdb'],$question['questionparent'],false);
            $questypes = question::getQuestypesByArgs();
            \PHPEMSPRO\tpl::getInstance()->assign('question',$question);
            \PHPEMSPRO\tpl::getInstance()->assign('parent',$parent);
            \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
            \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
            \PHPEMSPRO\tpl::getInstance()->assign('faq',$faq);
            \PHPEMSPRO\tpl::getInstance()->display('exams_answerfaq');
        }
    }

    public function passfaq()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $faqid = \PHPEMSPRO\route::get('faqid');
        faq::modifyFaq($subject['subjectdb'],$faqid,array('faqstatus' => 1));
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function delfaq()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $faqid = \PHPEMSPRO\route::get('faqid');
        faq::delFaq($subject['subjectdb'],$faqid);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function orderfaq()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $delids = \PHPEMSPRO\route::get('delids');
        $action = \PHPEMSPRO\route::get('action');
        switch($action)
        {
            case 'pass':
                foreach($delids as $key => $v)
                {
                    faq::modifyFaq($subject['subjectdb'],$key,array('faqstatus' => 1));
                }
                break;

            case 'delete':
                foreach($delids as $key => $v)
                {
                    faq::delFaq($subject['subjectdb'],$key);
                }
                break;

            default:
                break;
        }
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function faq()
    {
        if(\PHPEMSPRO\route::get('subjectid'))
        {
            $subjectid = \PHPEMSPRO\route::get('subjectid');
            $_SESSION['subjectid'] = $subjectid;
        }
        else
        {
            $subjectid = $_SESSION['subjectid'];
        }
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 1?$page:1;
        $subject = points::getSubjectById($subjectid);
        $page = \PHPEMSPRO\route::get('page');
        $args = array(
            array("AND","faqsubjectid = :faqsubjectid","faqsubjectid",$subjectid),
        );
        if($this->search['questionid'])
        {
            $args[] = array("AND","faqquestionid = :faqquestionid","faqquestionid",$this->search['questionid']);
        }
        if($this->search['username'])$args[] = array("AND","faqusername = :faqusername","faqusername",$this->search['username']);
        $faqs = faq::getFaqList($subject['subjectdb'],$args,$page);
        \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
        \PHPEMSPRO\tpl::getInstance()->assign('faqs',$faqs);
        \PHPEMSPRO\tpl::getInstance()->display('exams_faq');
    }

    public function ordernote()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $delids = \PHPEMSPRO\route::get('delids');
        $action = \PHPEMSPRO\route::get('action');
        switch($action)
        {
            case 'pass':
            foreach($delids as $key => $v)
            {
                favor::modifyNote($subject['subjectdb'],$key,array('notestatus' => 1));
            }
            break;

            case 'delete':
            foreach($delids as $key => $v)
            {
                favor::delNote($subject['subjectdb'],$key);
            }
            break;

            default:
            break;
        }
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function passnote()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $noteid = \PHPEMSPRO\route::get('noteid');
        favor::modifyNote($subject['subjectdb'],$noteid,array('notestatus' => 1));
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function delnote()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $noteid = \PHPEMSPRO\route::get('noteid');
        favor::delNote($subject['subjectdb'],$noteid);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function notes()
    {
        if(\PHPEMSPRO\route::get('subjectid'))
        {
            $subjectid = \PHPEMSPRO\route::get('subjectid');
            $_SESSION['subjectid'] = $subjectid;
        }
        else
        {
            $subjectid = $_SESSION['subjectid'];
        }
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 1?$page:1;
        $subject = points::getSubjectById($subjectid);
        $args = array();
        $args[] = array("AND","notesubject = :notesubject","notesubject",$subject['subjectid']);
        if($this->search['questionid'])$args[] = array("AND","notequestionid = :notequestionid","notequestionid",$this->search['questionid']);
        if($this->search['username'])$args[] = array("AND","noteusername = :noteusername","noteusername",$this->search['username']);
        $notes = favor::getNoteList($subject['subjectdb'],$args,$page,20,$orderby = 'notetime desc,noteid desc');
        \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
        \PHPEMSPRO\tpl::getInstance()->assign('notes',$notes);
        \PHPEMSPRO\tpl::getInstance()->display('exams_notes');
    }

    public function delbasics()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $delids = \PHPEMSPRO\route::get('delids');
        foreach($delids as $key => $p)
        {
            \PHPEMSPRO\exam\model\exams::delBasic($subject['subjectdb'],$key);
        }
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function addbasic()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        if(\PHPEMSPRO\route::get('addbasic'))
        {
            $args = \PHPEMSPRO\route::get('args');
            \PHPEMSPRO\exam\model\exams::addBasic($subject['subjectdb'],$args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "index.php?exam-master-exams-basics&subjectid=".$args['basicsubject']
            );
            exit(json_encode($message));
        }
        else
        {
            \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
            \PHPEMSPRO\tpl::getInstance()->display('exams_addbasic');
        }
    }

    public function modifybasic()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $basicid = \PHPEMSPRO\route::get('basicid');
        $basic = \PHPEMSPRO\exam\model\exams::getBasicById($subject['subjectdb'],$basicid);;
        if(\PHPEMSPRO\route::get('modifybasic'))
        {
            $args = \PHPEMSPRO\route::get('args');
            \PHPEMSPRO\exam\model\exams::modifyBasic($subject['subjectdb'],$basicid,$args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "reload"
            );
            exit(json_encode($message));
        }
        else
        {
            \PHPEMSPRO\tpl::getInstance()->assign('basic',$basic);
            \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
            \PHPEMSPRO\tpl::getInstance()->display('exams_modifybasic');
        }
    }

    public function settingbasic()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $basicid = \PHPEMSPRO\route::get('basicid');
        $basic = \PHPEMSPRO\exam\model\exams::getBasicById($subject['subjectdb'],$basicid);;
        if(\PHPEMSPRO\route::get('settingbasic'))
        {
            $args = \PHPEMSPRO\route::get('args');
            $args['basicsections'] = array();
            if(is_array($args['basicpoints']))
            {
                foreach($args['basicpoints'] as $key => $p)
                {
                    $args['basicsections'][] = $key;
                }
            }
            $args['basicexam']['opentime']['start'] = strtotime($args['basicexam']['opentime']['start']);
            $args['basicexam']['opentime']['end'] = strtotime($args['basicexam']['opentime']['end']);
            $args['basicsections'] = $args['basicsections'];
            $args['basicpoints'] = $args['basicpoints'];
            $args['basicexam'] = $args['basicexam'];
            \PHPEMSPRO\exam\model\exams::modifyBasic($subject['subjectdb'],$basicid,$args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "reload"
            );
            exit(json_encode($message));
        }
        else
        {
            $points = points::getPointsBySubjectid($basic['basicsubject']);
            $sections = $points['sections'];
            $points = $points['points'];
            $tpls = array();
            foreach(glob("app/exam/view/app/exampaper_paper*.tpl") as $p)
            {
                $tpls['ep'][] = substr(basename($p),0,-4);
            }
            foreach(glob("app/exam/view/app/exam_paper*.tpl") as $p)
            {
                $tpls['pp'][] = substr(basename($p),0,-4);
            }
            \PHPEMSPRO\tpl::getInstance()->assign('tpls',$tpls);
            \PHPEMSPRO\tpl::getInstance()->assign('sections',$sections);
            \PHPEMSPRO\tpl::getInstance()->assign('points',$points);
            \PHPEMSPRO\tpl::getInstance()->assign('basic',$basic);
            \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
            \PHPEMSPRO\tpl::getInstance()->display('exams_settingbasic');
        }
    }

    public function delbasic()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $basicid = \PHPEMSPRO\route::get('basicid');
        \PHPEMSPRO\exam\model\exams::delBasic($subject['subjectdb'],$basicid);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function basics()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 0?$page:1;
        if(\PHPEMSPRO\route::get('subjectid'))
        {
            $subjectid = \PHPEMSPRO\route::get('subjectid');
            $_SESSION['subjectid'] = $subjectid;
        }
        else
        {
            $subjectid = $_SESSION['subjectid'];
        }
        $subject = points::getSubjectById($subjectid);
        $args = array();
        $args[] = array("AND","basicsubject = :subjectid","subjectid",$subjectid);
        $basics = \PHPEMSPRO\exam\model\exams::getBasicsList($subject['subjectdb'],$args,$page);
        foreach($basics['data'] as $key => $basic)
        {
            $number = \PHPEMSPRO\exam\model\exams::getBasicMemberNumber($subject['subjectdb'],$basic['basicid']);
            $basics['data'][$key]['number'] = $number;
        }
        \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
        \PHPEMSPRO\tpl::getInstance()->assign('basics',$basics);
        \PHPEMSPRO\tpl::getInstance()->display('exams_basics');
    }

    public function sectionconsole()
    {
        switch (\PHPEMSPRO\route::get('action'))
        {
            case 'modify':
                $ids = \PHPEMSPRO\route::get('ids');
                foreach($ids as $key => $p)
                {
                    points::modifySection($key,array('sectionorder' => $p));
                }
                $message = array(
                    'statusCode' => 200,
                    "message" => "操作成功",
                    "callbackType" => "forward",
                    "forwardUrl" => "reload"
                );
                exit(json_encode($message));
                break;

            case 'delete':
                $delids = \PHPEMSPRO\route::get('delids');
                foreach($delids as $key => $p)
                {
                    if(points::getPointsNumber(array(array("AND","pointsection = :pointsection","pointsection",$key))))
                    {
                        $section = points::getSectionById($key);
                        $message = array(
                            'statusCode' => 300,
                            "message" => "操作失败，请先删除章节{$section['sectionname']}下所有知识点"
                        );
                        exit(json_encode($message));
                    }
                    points::delSection($key);
                }
                $message = array(
                    'statusCode' => 200,
                    "message" => "操作成功",
                    "callbackType" => "forward",
                    "forwardUrl" => "reload"
                );
                exit(json_encode($message));
                break;

            default:
                break;
        }
    }

    public function pointconsole()
    {
        switch (\PHPEMSPRO\route::get('action'))
        {
            case 'modify':
                $ids = \PHPEMSPRO\route::get('ids');
                foreach($ids as $key => $p)
                {
                    points::modifyPoint($key,array('pointorder' => $p));
                }
                $message = array(
                    'statusCode' => 200,
                    "message" => "操作成功",
                    "callbackType" => "forward",
                    "forwardUrl" => "reload"
                );
                exit(json_encode($message));
                break;
				
			case 'move':
                $delids = \PHPEMSPRO\route::get('delids');
				$targetsectionid = \PHPEMSPRO\route::get('targetsectionid');
				if(!$targetsectionid)
				{
					$message = array(
						'statusCode' => 300,
						"message" => "请选择目标章节"
					);
					exit(json_encode($message));
				}
                foreach($delids as $key => $p)
                {
                    points::modifyPoint($key,array('pointsection' => $targetsectionid));
                }
                $message = array(
                    'statusCode' => 200,
                    "message" => "操作成功",
                    "callbackType" => "forward",
                    "forwardUrl" => "reload"
                );
                exit(json_encode($message));
                break;

            case 'delete':
                $delids = \PHPEMSPRO\route::get('delids');
                foreach($delids as $key => $p)
                {
                    /*if(points::getPointsNumber(array(array("AND","pointsection = :pointsection","pointsection",$key))))
                    {
                        $section = points::getSectionById($key);
                        $message = array(
                            'statusCode' => 300,
                            "message" => "操作失败，请先删除章节{$section['sectionname']}下所有知识点"
                        );
                        exit(json_encode($message));
                    }*/
                    points::delPoint($key);
                }
                $message = array(
                    'statusCode' => 200,
                    "message" => "操作成功",
                    "callbackType" => "forward",
                    "forwardUrl" => "reload"
                );
                exit(json_encode($message));
                break;

            default:
                break;
        }
    }

    public function addpoint()
    {
        if(\PHPEMSPRO\route::get('addpoint'))
        {
            $args = \PHPEMSPRO\route::get('args');
            points::addPoint($args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "index.php?exam-master-exams-points&sectionid=".$args['pointsection']
            );
            exit(json_encode($message));
        }
        else
        {
            $sectionid = \PHPEMSPRO\route::get('sectionid');
            $section = points::getSectionById($sectionid);
            $subject = points::getSubjectById($section['sectionsubject']);
            \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
            \PHPEMSPRO\tpl::getInstance()->assign('section',$section);
            \PHPEMSPRO\tpl::getInstance()->display('exams_addpoint');
        }
    }

    public function modifypoint()
    {
        $pointid = \PHPEMSPRO\route::get('pointid');
        $point = points::getPointById($pointid);
        if(\PHPEMSPRO\route::get('modifypoint'))
        {
            $args = \PHPEMSPRO\route::get('args');
            points::modifyPoint($pointid,$args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "reload"
            );
            exit(json_encode($message));
        }
        else
        {
            $section = points::getSectionById($point['pointsection']);
            $subject = points::getSubjectById($section['sectionsubject']);
            \PHPEMSPRO\tpl::getInstance()->assign('point',$point);
            \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
            \PHPEMSPRO\tpl::getInstance()->assign('section',$section);
            \PHPEMSPRO\tpl::getInstance()->display('exams_modifypoint');
        }
    }

    public function refreshpoint()
    {
        $pointid = \PHPEMSPRO\route::get('pointid');
        $point = points::getPointById($pointid);
        $section = points::getSectionById($point['pointsection']);
        $subject = points::getSubjectById($section['sectionsubject']);
        \PHPEMSPRO\pedis::getInstance()->delHashData('subjectquestions',$subject['subjectdb'].'-'.$pointid);
        \PHPEMSPRO\pedis::getInstance()->delHashData('subjectquestionrows',$subject['subjectdb'].'-'.$pointid);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function delpoint()
    {
        $pointid = \PHPEMSPRO\route::get('pointid');
        points::delPoint($pointid);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function points()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 0?$page:1;
        $sectionid = \PHPEMSPRO\route::get('sectionid');
        $args = array();
        $args[] = array("AND","pointsection = :sectionid","sectionid",$sectionid);
        $section = points::getSectionById($sectionid);
        $subject = points::getSubjectById($section['sectionsubject']);
        $points = points::getPointsList($args,$page);
		$sections = points::getSectionsByArgs(array(array("AND","sectionsubject = :sectionsubject","sectionsubject",$section['sectionsubject'])));
        \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
        \PHPEMSPRO\tpl::getInstance()->assign('section',$section);
		\PHPEMSPRO\tpl::getInstance()->assign('sections',$sections);
        \PHPEMSPRO\tpl::getInstance()->assign('points',$points);
        \PHPEMSPRO\tpl::getInstance()->display('exams_points');
    }

    public function addsection()
    {
        if(\PHPEMSPRO\route::get('addsection'))
        {
            $args = \PHPEMSPRO\route::get('args');
            points::addSection($args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "index.php?exam-master-exams-sections&subjectid=".$args['sectionsubject']
            );
            exit(json_encode($message));
        }
        else
        {
            $subjectid = \PHPEMSPRO\route::get('subjectid');
            $subject = points::getSubjectById($subjectid);
            \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
            \PHPEMSPRO\tpl::getInstance()->display('exams_addsection');
        }
    }

    public function modifysection()
    {
        $sectionid = \PHPEMSPRO\route::get('sectionid');
        $section = points::getSectionById($sectionid);
        if(\PHPEMSPRO\route::get('modifysection'))
        {
            $args = \PHPEMSPRO\route::get('args');
            points::modifySection($sectionid,$args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "reload"
            );
            exit(json_encode($message));
        }
        else
        {
            $subject = points::getSubjectById($section['sectionsubject']);
            \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
            \PHPEMSPRO\tpl::getInstance()->assign('section',$section);
            \PHPEMSPRO\tpl::getInstance()->display('exams_modifysection');
        }
    }

    public function delsection()
    {
        $sectionid = \PHPEMSPRO\route::get('sectionid');
        if(points::getPointsNumber(array(array("AND","pointsection = :sectionid","sectionid",$sectionid))))
        {
            $message = array(
                'statusCode' => 300,
                "message" => "操作失败，请删除该章节下所有知识点"
            );
            exit(json_encode($message));
        }
        points::delSection($sectionid);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function sections()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 0?$page:1;
        $subjectid = \PHPEMSPRO\route::get('subjectid');
        $args = array();
        $args[] = array("AND","sectionsubject = :subjectid","subjectid",$subjectid);
        $subject = points::getSubjectById($subjectid);
        $sections = points::getSectionsList($args,$page);
        \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
        \PHPEMSPRO\tpl::getInstance()->assign('sections',$sections);
        \PHPEMSPRO\tpl::getInstance()->display('exams_sections');
    }

    public function addsubject()
    {
        if(\PHPEMSPRO\route::get('addsubject'))
        {
            $args = \PHPEMSPRO\route::get('args');
            points::addSubject($args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "index.php?exam-master-exams"
            );
            exit(json_encode($message));
        }
        else
        {
            $questypes = question::getQuestypesByArgs();
            $trainings = training::getTrainingsByArgs();
            \PHPEMSPRO\tpl::getInstance()->assign('trainings',$trainings);
            \PHPEMSPRO\tpl::getInstance()->assign('databases',\PHPEMSPRO\config::db);
            \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
            \PHPEMSPRO\tpl::getInstance()->display('exams_addsubject');
        }
    }

    public function modifysubject()
    {
        $subjectid = \PHPEMSPRO\route::get('subjectid');
        $subject = points::getSubjectById($subjectid);
        if(\PHPEMSPRO\route::get('modifysubject'))
        {
            $args = \PHPEMSPRO\route::get('args');
            points::modifySubject($subjectid,$args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "reload"
            );
            exit(json_encode($message));
        }
        else
        {
            $questypes = question::getQuestypesByArgs();
            $trainings = training::getTrainingsByArgs();
            \PHPEMSPRO\tpl::getInstance()->assign('trainings',$trainings);
            \PHPEMSPRO\tpl::getInstance()->assign('databases',\PHPEMSPRO\config::db);
            \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
            \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
            \PHPEMSPRO\tpl::getInstance()->display('exams_modifysubject');
        }
    }

    public function delsubjects()
    {
        switch (\PHPEMSPRO\route::get('action'))
        {
            case 'clear':
                $delids = \PHPEMSPRO\route::get('delids');
                foreach($delids as $subjectid => $p)
                {
                    $subject = points::getSubjectById($subjectid);
                    \PHPEMSPRO\pedis::getInstance()->delHashData('subjectquestionrows',$subject['subjectdb'].'-'.$subject['subjectid']);
                    \PHPEMSPRO\pedis::getInstance()->delHashData('subjectquestions',$subject['subjectdb'].'-'.$subject['subjectid']);
                    $record = array();
                    $record['questions'] = json_decode(\PHPEMSPRO\pedis::getInstance()->getHashData('questions',$subject['subjectdb']),true);
                    $record['questionrows'] = json_decode(\PHPEMSPRO\pedis::getInstance()->getHashData('questionrows',$subject['subjectdb']),true);
                    $record['number'] = json_decode(\PHPEMSPRO\pedis::getInstance()->getHashData('number',$subject['subjectdb']),true);
                    $points = points::getPointsBySubjectid($subject['subjectid']);
                    foreach($points['points'] as $key => $point)
                    {
                        foreach ($point as $p)
                        {
                            unset($record['questions'][$p['pointid']],$record['questionrows'][$p['pointid']],$record['number'][$p['pointid']]);
                        }
                    }
                    \PHPEMSPRO\pedis::getInstance()->setHashData('questions',$subject['subjectdb'],json_encode($record['questions'],JSON_UNESCAPED_UNICODE));
                    \PHPEMSPRO\pedis::getInstance()->setHashData('questionrows',$subject['subjectdb'],json_encode($record['questionrows'],JSON_UNESCAPED_UNICODE));
                    \PHPEMSPRO\pedis::getInstance()->setHashData('number',$subject['subjectdb'],json_encode($record['number'],JSON_UNESCAPED_UNICODE));
                }
                break;

            case 'delete':
                $delids = \PHPEMSPRO\route::get('delids');
                foreach($delids as $subjectid => $p)
                {
                    if(points::getSectionsNumber(array(array("AND","sectionsubject = :subjectid","subjectid",$subjectid))))
                    {
                        $subject = points::getSubjectById($subjectid);
                        $message = array(
                            'statusCode' => 300,
                            "message" => "操作失败，请删除科目《{$subject['subjectname']}》下所有章节"
                        );
                        exit(json_encode($message));
                    }
                    points::delSubject($subjectid);
                }
                break;

            default:break;
        }
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function delsubject()
    {
        $subjectid = \PHPEMSPRO\route::get('subjectid');
        if(points::getSectionsNumber(array(array("AND","sectionsubject = :subjectid","subjectid",$subjectid))))
        {
            $message = array(
                'statusCode' => 300,
                "message" => "操作失败，请删除该科目下所有章节"
            );
            exit(json_encode($message));
        }
        points::delSubject($subjectid);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function addpaper()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        if(\PHPEMSPRO\route::get('addpaper'))
        {
            $args = \PHPEMSPRO\route::get('args');
            $args['paperauthor'] = $this->_user['username'];
            $args['papertime'] = TIME;
            $totalscore = 0;
            foreach($args['papersetting']['questype'] as $key => $p)
            {
                if(!$args['papersetting']['questypelite'][$key])
                {
                    unset($args['papersetting']['questype'][$key],$args['paperquestions'][$key]);
                }
                $totalscore += $p['number'] * $p['score'];
            }
            if($args['papersetting']['score'] != $totalscore)
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "分数设置不正确，请检查"
                );
                exit(json_encode($message));
            }
            if($args['papertype'] == 3)
            {
                $uploadfile = \PHPEMSPRO\route::get('uploadfile');
                $args['paperquestions'] = question::importCsvQuestions($uploadfile);
            }
            \PHPEMSPRO\exam\model\exams::addPaper($subject['subjectdb'],$args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "index.php?exam-master-exams-papers&subjectid={$args['papersubject']}"
            );
            exit(json_encode($message));
        }
        else
        {
            $type = \PHPEMSPRO\route::get('type');
            $questypes = question::getQuestypesByArgs(array(array("AND","find_in_set(questcode,:questcode)","questcode",implode(',',$subject['subjectsetting']))));
            $points = points::getPointsBySubjectid($subjectid);
            $sections = $points['sections'];
            $points = $points['points'];
            \PHPEMSPRO\tpl::getInstance()->assign('sections',$sections);
            \PHPEMSPRO\tpl::getInstance()->assign('points',$points);
            \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
            \PHPEMSPRO\tpl::getInstance()->assign('type',$type);
            \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
            switch ($type)
            {
                case '2':
                    \PHPEMSPRO\tpl::getInstance()->display('exams_addselfpaper');
                    break;

                case '3':
                    \PHPEMSPRO\tpl::getInstance()->display('exams_addtemppaper');
                    break;

                default:
                    \PHPEMSPRO\tpl::getInstance()->display('exams_addpaper');
                    break;
            }
        }
    }

    public function outcsv()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $paperid = \PHPEMSPRO\route::get('paperid');
        $paper = \PHPEMSPRO\exam\model\exams::getPaperById($subject['subjectdb'],$paperid);
        $r = array();
        foreach($paper['paperquestions']['questions'] as $questions)
        {
            foreach($questions as $question)
            {
                $tmp = array();
                $tmp[] = iconv("UTF-8","GBK",$question['questiontype']);
                $tmp[] = iconv("UTF-8","GBK",$question['question']);
                $tmp[] = iconv("UTF-8","GBK",$question['questionselect']);
                $tmp[] = iconv("UTF-8","GBK",$question['questionselectnumber']);
                $tmp[] = iconv("UTF-8","GBK",$question['questionanswer']);
                $tmp[] = iconv("UTF-8","GBK",$question['questiondescribe']);
                $r[] = $tmp;
            }
        }
        foreach($paper['paperquestions']['questionrows'] as $questionrows)
        {
            foreach($questionrows as $questionrow) {
                $tmp = array();
                $tmp[] = iconv("UTF-8", "GBK", $question['qrtype']);
                $tmp[] = iconv("UTF-8", "GBK", $question['qrquestion']);
                $tmp[] = '';
                $tmp[] = '';
                $tmp[] = '';
                $tmp[] = '';
                $tmp[] = 1;
                $tmp[] = 1;
                $r[] = $tmp;
                foreach ($questionrow['data'] as $question) {
                    $tmp = array();
                    $tmp[] = iconv("UTF-8", "GBK", $question['questiontype']);
                    $tmp[] = iconv("UTF-8", "GBK", $question['question']);
                    $tmp[] = iconv("UTF-8", "GBK", $question['questionselect']);
                    $tmp[] = iconv("UTF-8", "GBK", $question['questionselectnumber']);
                    $tmp[] = iconv("UTF-8", "GBK", $question['questionanswer']);
                    $tmp[] = iconv("UTF-8", "GBK", $question['questiondescribe']);
                    $tmp[] = 1;
                    $tmp[] = 0;
                    $r[] = $tmp;
                }
            }
        }
        $filename = 'public/data/out/score/'.TIME.'.csv';
        if(\PHPEMSPRO\files::outCsv($filename,$r))
        {
            $message = array(
                'statusCode' => 200,
                "message" => "成绩导出成功，转入下载页面，如果浏览器没有相应，请<a href=\"{$filename}\">点此下载</a>",
                "callbackType" => 'forward',
                "forwardUrl" => "{$filename}"
            );
        }
        else
        {
            $message = array(
                'statusCode' => 300,
                "message" => "成绩导出失败"
            );
        }
        exit(json_encode($message));
    }

    public function modifypaper()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $paperid = \PHPEMSPRO\route::get('paperid');
        $paper = \PHPEMSPRO\exam\model\exams::getPaperById($subject['subjectdb'],$paperid);
        if(\PHPEMSPRO\route::get('modifypaper'))
        {
            $args = \PHPEMSPRO\route::get('args');
            $totalscore = 0;
            foreach($args['papersetting']['questype'] as $key => $p)
            {
                if(!$args['papersetting']['questypelite'][$key])
                {
                    unset($args['papersetting']['questype'][$key],$args['paperquestions'][$key]);
                }
                $totalscore += $p['number'] * $p['score'];
            }
            if($args['papersetting']['score'] != $totalscore)
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "分数设置不正确，请检查"
                );
                exit(json_encode($message));
            }
            if($paper['papertype'] == 3)
            {
                $uploadfile = \PHPEMSPRO\route::get('uploadfile');
                if($uploadfile)
                {
                    $args['paperquestions'] = question::importCsvQuestions($uploadfile);
                }
            }
            \PHPEMSPRO\exam\model\exams::modifyPaper($subject['subjectdb'],$paperid,$args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "reload"
            );
            exit(json_encode($message));
        }
        else
        {
            $questypes = question::getQuestypesByArgs(array(array("AND","find_in_set(questcode,:questcode)","questcode",implode(',',$subject['subjectsetting']))));
            $points = points::getPointsBySubjectid($paper['papersubject']);
            $sections = $points['sections'];
            $points = $points['points'];
            \PHPEMSPRO\tpl::getInstance()->assign('sections',$sections);
            \PHPEMSPRO\tpl::getInstance()->assign('points',$points);
            \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
            \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
            switch ($paper['papertype'])
            {
                case '2':
                    foreach($paper['paperquestions'] as $key => $p)
                    {
                        $paper['papernumber'][$key] = \PHPEMSPRO\exam\model\question::getExamQuestionNumber($subject['subjectdb'],$p);
                    }
                    \PHPEMSPRO\tpl::getInstance()->assign('paper',$paper);
                    \PHPEMSPRO\tpl::getInstance()->display('exams_modifyselfpaper');
                    break;

                case '3':
                    \PHPEMSPRO\tpl::getInstance()->assign('paper',$paper);
                    \PHPEMSPRO\tpl::getInstance()->display('exams_modifytemppaper');
                    break;

                default:
                    \PHPEMSPRO\tpl::getInstance()->assign('paper',$paper);
                    \PHPEMSPRO\tpl::getInstance()->display('exams_modifypaper');
                    break;
            }
        }
    }

    public function delpaper()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $paperid = \PHPEMSPRO\route::get('paperid');
        \PHPEMSPRO\exam\model\exams::delPaper($subject['subjectdb'],$paperid);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function papers()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 0?$page:1;
        if(\PHPEMSPRO\route::get('subjectid'))
        {
            $subjectid = \PHPEMSPRO\route::get('subjectid');
            $_SESSION['subjectid'] = $subjectid;
        }
        else
        {
            $subjectid = $_SESSION['subjectid'];
        }
        $subject = points::getSubjectById($subjectid);
        $args = array();
        $args[] = array("AND","papersubject = :subjectid","subjectid",$subjectid);
        $subject = points::getSubjectById($subjectid);
        $papers = \PHPEMSPRO\exam\model\exams::getPapersList($subject['subjectdb'],$args,$page);
        \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
        \PHPEMSPRO\tpl::getInstance()->assign('papers',$papers);
        \PHPEMSPRO\tpl::getInstance()->display('exams_papers');
    }
	
	public function outpaper()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $paperid = route::get('paperid');
        $paper = \PHPEMSPRO\exam\model\exams::getPaperById($subject['subjectdb'],$paperid);
        if($paper['papertype'] == 1)
        {
            $args = array();
            $points = array();
            $allpoints = points::getPointsBySubjectid($subjectid);
            foreach($allpoints['points'] as $section)
            {
                foreach($section as $point)
                {
                    $points[] = $point['pointid'];
                }
            }
            $questionids = question::selectquestions($subject['subjectdb'],$paper,$points);
            $questions = array();
            $questionrows = array();
            $number = array();
            foreach($questionids['ids'] as $key => $p)
            {
                if(count($p))
                {
                    $ids = implode(',', $p);
                    $questions[$key] = question::getQuestionsByArgs($subject['subjectdb'], array(array("AND", "questionid in ({$ids})"), array("AND", "questionstatus = 1")));
                    $number[$key] = count($questions[$key]);
                }
            }
            foreach($questionids['qrids'] as $key => $p)
            {
                foreach($p as $qrid)
                {
                    $qr = question::getQuestionRowsById($subject['subjectdb'],$qrid);
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
            $paper['question'] = array('questions'=>$questions,'questionrows'=>$questionrows);
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
                    $questions[$key] = question::getQuestionsByArgs($subject['subjectdb'],array(array("AND","questionid in ({$qids})"),array("AND","questionstatus = 1")));;
                }
                if($p['questionrows'])$qrids = implode(',',explode(',',trim($p['questionrows']," ,")));
                if($qrids)
                {
                    $qrids = explode(",",$qrids);
                    foreach($qrids as $t)
                    {
                        $qr = question::getQuestionRowsById($subject['subjectdb'],$t);
                        if($qr)
                        {
                            $questionrows[$key][$t] = $qr;
                        }
                    }
                }
            }
            $paper['question'] = array('questions'=>$questions,'questionrows'=>$questionrows);
        }
        else
        {
            $message = array(
                'statusCode' => 300,
                "message" => "不支持导出试卷"
            );
            route::urlJump($message);
        }
        $questypes = question::getQuestypesByArgs();
        tpl::getInstance()->assign('index',array(1=>'一','二','三','四','五','六','七','八','九','十','十一','十二','十三','十四','十五','十六','十七','十八','十九','二十'));
        tpl::getInstance()->assign('paper',$paper);
        tpl::getInstance()->assign('questypes',$questypes);
        $content = tpl::getInstance()->fetchExeCnt('exams_outpaper');
        if(!file_exists(PEP_PATH.'/public/data/word/'))\PHPEMSPRO\files::mdir(PEP_PATH.'/public/data/word/');
        $fname = 'public/data/word/'.uniqid().".doc";
        $content = \PHPEMSPRO\msdoc::getInstance()->WordMake($content);
        file_put_contents($fname,$content);
        $message = array(
            'statusCode' => 200,
            "message" => "成绩导出成功，转入下载页面，如果浏览器没有相应，请<a href=\"{$fname}\">点此下载</a>",
            "callbackType" => "forward",
            "forwardUrl" => $fname
        );
        route::urlJump($message);
    }

    public function modifyquestion()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $questionid = \PHPEMSPRO\route::get('questionid');
        $question = question::getQuestionById($subject['subjectdb'],$questionid);
        $questypes = question::getQuestypesByArgs(array(array("AND","find_in_set(questcode,:questcode)","questcode",implode(',',$subject['subjectsetting']))));
        if(\PHPEMSPRO\route::get('modifyquestion'))
        {
            $args = \PHPEMSPRO\route::get('args');
            if(!$args['questionpoints'])
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "请选择知识点"
                );
                exit(json_encode($message));
            }
            $targs = \PHPEMSPRO\route::get('targs');
            if($questypes[$args['questiontype']]['questsort'])$choice = 0;
            else $choice = $questypes[$args['questiontype']]['questchoice'];
            $args['questionanswer'] = $targs['questionanswer'.$choice];
            if(!$args['questionanswer'])
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "请填写答案"
                );
                exit(json_encode($message));
            }
            if(is_array($args['questionanswer']))$args['questionanswer'] = implode('',$args['questionanswer']);
            question::modifyQuestion($subject['subjectdb'],$questionid,$args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "reload"
            );
            exit(json_encode($message));
        }
        else
        {
            $points = points::getPointsBySubjectid($subjectid);
            $sections = $points['sections'];
            $points = $points['points'];
            $tmp = array();
            foreach($points as $pts)
            {
                foreach($pts as $p)
                {
                    $tmp[$p['pointid']] = $p;
                }
            }
            $points = $tmp;
            $app = apps::getAppByCode('exam');
            $app['appsetting']['selectornumbers'] = explode(',',$app['appsetting']['selectornumbers']);
            $app['appsetting']['selectortype'] = explode(',',$app['appsetting']['selectortype']);
            \PHPEMSPRO\tpl::getInstance()->assign('question',$question);
            \PHPEMSPRO\tpl::getInstance()->assign('setting',$app['appsetting']);
            \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
            \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
            \PHPEMSPRO\tpl::getInstance()->assign('sections',$sections);
            \PHPEMSPRO\tpl::getInstance()->assign('points',$points);
            \PHPEMSPRO\tpl::getInstance()->display('exams_modifyquestion');
        }
    }

    public function hidequestion()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $questionid = \PHPEMSPRO\route::get('questionid');
        question::hideQuestion($subject['subjectdb'],$questionid);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function hidequestions()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $delids = \PHPEMSPRO\route::get('delids');
        foreach($delids as $id => $p)
        {
            question::hideQuestion($subject['subjectdb'],$id);
        }
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function questionbatch()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        switch(\PHPEMSPRO\route::get('action'))
        {
            case 'level':
                $level = route::get('level');
                $delids = \PHPEMSPRO\route::get('delids');
                foreach($delids as $id => $p)
                {
                    question::modifyQuestion($subject['subjectdb'],$id,['questionlevel' => $level]);
                }
                break;

            case 'delete':
                $delids = \PHPEMSPRO\route::get('delids');
                foreach($delids as $id => $p)
                {
                    question::hideQuestion($subject['subjectdb'],$id);
                }
                break;

            case 'move':
                $delids = \PHPEMSPRO\route::get('delids');
                $ids = array();
                foreach($delids as $id => $p)
                {
                    $ids[] = $id;
                }
                $ids = implode(',',$ids);
                $points = points::getPointsBySubjectid($subjectid);
                $sections = $points['sections'];
                $points = $points['points'];
                \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
                \PHPEMSPRO\tpl::getInstance()->assign('sections',$sections);
                \PHPEMSPRO\tpl::getInstance()->assign('points',$points);
                \PHPEMSPRO\tpl::getInstance()->assign('ids',$ids);
                \PHPEMSPRO\tpl::getInstance()->display('exams_movequestion');
                exit();
                break;

            case 'setknows':
                $questionids = explode(',',\PHPEMSPRO\route::get('questionids'));
                $args = \PHPEMSPRO\route::get('args');
                foreach($questionids as $id)
                {
                    question::modifyQuestion($subject['subjectdb'],$id,array('questionpoints' => $args['questionpoints']));
                }
                break;
        }
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function addquestion()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $questypes = question::getQuestypesByArgs(array(array("AND","find_in_set(questcode,:questcode)","questcode",implode(',',$subject['subjectsetting']))));
        if(\PHPEMSPRO\route::get('addquestion'))
        {
            $args = \PHPEMSPRO\route::get('args');
            if(!$args['questionpoints'])
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "请选择知识点"
                );
                exit(json_encode($message));
            }
            $targs = \PHPEMSPRO\route::get('targs');
            if($questypes[$args['questiontype']]['questsort'])$choice = 0;
            else $choice = $questypes[$args['questiontype']]['questchoice'];
            $args['questionanswer'] = $targs['questionanswer'.$choice];
            $args["questionauthor"] = $this->_user['username'];
            if(!$args['questionanswer'])
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "请填写答案"
                );
                exit(json_encode($message));
            }
            if(is_array($args['questionanswer']))$args['questionanswer'] = implode('',$args['questionanswer']);
            question::addQuestion($subject['subjectdb'],$args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "index.php?exam-master-exams-questions"
            );
            exit(json_encode($message));
        }
        else
        {
            $points = points::getPointsBySubjectid($subjectid);
            $sections = $points['sections'];
            $points = $points['points'];
            $app = apps::getAppByCode('exam');
            $app['appsetting']['selectornumbers'] = explode(',',$app['appsetting']['selectornumbers']);
            $app['appsetting']['selectortype'] = explode(',',$app['appsetting']['selectortype']);
            \PHPEMSPRO\tpl::getInstance()->assign('setting',$app['appsetting']);
            \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
            \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
            \PHPEMSPRO\tpl::getInstance()->assign('sections',$sections);
            \PHPEMSPRO\tpl::getInstance()->assign('points',$points);
            \PHPEMSPRO\tpl::getInstance()->display('exams_addquestion');
        }
    }

    public function questions()
    {
        if(\PHPEMSPRO\route::get('subjectid'))
        {
            $subjectid = \PHPEMSPRO\route::get('subjectid');
            $_SESSION['subjectid'] = $subjectid;
        }
        else
        {
            $subjectid = $_SESSION['subjectid'];
        }
        $subject = points::getSubjectById($subjectid);
        $points = points::getPointsBySubjectid($subjectid);
        $sections = $points['sections'];
        $points = $points['points'];
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 0?$page:1;
        $args = array();
        if($this->search['questionid'])
        {
            $args[] = array("AND","questionid = :questionid",'questionid',$this->search['questionid']);
        }
        if($this->search['keyword'])
        {
            $args[] = array("AND","question LIKE :question",'question','%'.$this->search['keyword'].'%');
        }
        if($this->search['stime'])
        {
            $args[] = array("AND","questiontime >= :questioncreatetime",'questioncreatetime',strtotime($this->search['stime']));
        }
        if($this->search['etime'])
        {
            $args[] = array("AND","questiontime <= :questionendtime",'questionendtime',strtotime($this->search['etime']));
        }
        if($this->search['questiontype'])
        {
            $args[] = array("AND","questiontype = :questiontype",'questiontype',$this->search['questiontype']);
        }
        if($this->search['questionlevel'])
        {
            $args[] = array("AND","questionlevel = :questionlevel",'questionlevel',$this->search['questionlevel']);
        }
        if($this->search['questionsectionid'])
        {
            if($this->search['questionpointid'])
            {
                $args[] = array("AND","find_in_set(:questionpointid,questionpoints)","questionpointid",$this->search['questionpointid']);
            }
            else
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "请选择知识点"
                );
                exit(json_encode($message));
            }
            /*else
            {
                $args[] = array("AND","(");
                foreach($points[$this->search['questionsectionid']] as $key => $p)
                {
                    $args[] = array("OR","find_in_set(:questionpointid{$key},questionpoints)","questionpointid{$key}",$p['pointid']);
                }
                $args[] = array(")");
            }*/
        }
        else
        {
            $args[] = array("AND","questionsubject = :subjectid","subjectid",$subjectid);
        }
        $args[] = array("AND","questionstatus = 1");
        $args[] = array("AND","questionparent = 0");
        $questions = question::getQuestionList($subject['subjectdb'],$args,$page);
        $questypes = question::getQuestypesByArgs(array(array("AND","find_in_set(questcode,:questcode)","questcode",implode(',',$subject['subjectsetting']))));
        \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
        \PHPEMSPRO\tpl::getInstance()->assign('sections',$sections);
        \PHPEMSPRO\tpl::getInstance()->assign('points',$points);
        \PHPEMSPRO\tpl::getInstance()->assign('page',$page);
        \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
        \PHPEMSPRO\tpl::getInstance()->assign('questions',$questions);
        \PHPEMSPRO\tpl::getInstance()->display('exams_questions');
    }

    public function modifychildquestion()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $questionid = \PHPEMSPRO\route::get('questionid');
        $question = question::getQuestionById($subject['subjectdb'],$questionid);
        $questypes = question::getQuestypesByArgs(array(array("AND","find_in_set(questcode,:questcode)","questcode",implode(',',$subject['subjectsetting']))));
        if(\PHPEMSPRO\route::get('modifychildquestion'))
        {
            $args = \PHPEMSPRO\route::get('args');
            $targs = \PHPEMSPRO\route::get('targs');
            if($questypes[$args['questiontype']]['questsort'])$choice = 0;
            else $choice = $questypes[$args['questiontype']]['questchoice'];
            $args['questionanswer'] = $targs['questionanswer'.$choice];
            if(!$args['questionanswer'])
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "请填写答案"
                );
                exit(json_encode($message));
            }
            if(is_array($args['questionanswer']))$args['questionanswer'] = implode('',$args['questionanswer']);
            question::modifyQuestion($subject['subjectdb'],$questionid,$args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "reload"
            );
            exit(json_encode($message));
        }
        else
        {
            $points = points::getPointsBySubjectid($subjectid);
            $sections = $points['sections'];
            $points = $points['points'];
            $tmp = array();
            foreach($points as $pts)
            {
                foreach($pts as $p)
                {
                    $tmp[$p['pointid']] = $p;
                }
            }
            $points = $tmp;
            $app = apps::getAppByCode('exam');
            $app['appsetting']['selectornumbers'] = explode(',',$app['appsetting']['selectornumbers']);
            $app['appsetting']['selectortype'] = explode(',',$app['appsetting']['selectortype']);
            \PHPEMSPRO\tpl::getInstance()->assign('question',$question);
            \PHPEMSPRO\tpl::getInstance()->assign('setting',$app['appsetting']);
            \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
            \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
            \PHPEMSPRO\tpl::getInstance()->assign('sections',$sections);
            \PHPEMSPRO\tpl::getInstance()->assign('points',$points);
            \PHPEMSPRO\tpl::getInstance()->display('exams_modifychildquestion');
        }
    }

    public function delquestion()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $questionid = \PHPEMSPRO\route::get('questionid');
        question::delQuestion($subject['subjectdb'],$questionid);
        $question = question::getQuestionById($subject['subjectdb'],$questionid);
        if($question['questionparent'])
        question::resetRowsQuestionNumber($subject['subjectdb'],$question['questionparent']);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function orderchildquestion()
    {
        $action = \PHPEMSPRO\route::get('action');
        $delids = \PHPEMSPRO\route::get('delids');
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        switch ($action)
        {
            case 'sequence':
                foreach ($delids as $key => $p)
                {
                    question::modifyQuestion($subject['subjectdb'],$key,array('questionorder' => $p));
                }
                $question = question::getQuestionById($subject['subjectdb'],$key);
                if($question['questionparent'])
                question::resetRowsQuestionNumber($subject['subjectdb'],$question['questionparent']);
                break;

            default:
                break;
        }
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function addchildquestion()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $qrid = \PHPEMSPRO\route::get('qrid');
        $questypes = question::getQuestypesByArgs(array(array("AND","find_in_set(questcode,:questcode)","questcode",implode(',',$subject['subjectsetting']))));
        if(\PHPEMSPRO\route::get('addchildquestion'))
        {
            $args = \PHPEMSPRO\route::get('args');
            $targs = \PHPEMSPRO\route::get('targs');
            if($questypes[$args['questiontype']]['questsort'])$choice = 0;
            else $choice = $questypes[$args['questiontype']]['questchoice'];
            $args['questionanswer'] = $targs['questionanswer'.$choice];
            if(!$args['questionanswer'])
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "请填写答案"
                );
                exit(json_encode($message));
            }
            if(is_array($args['questionanswer']))$args['questionanswer'] = implode('',$args['questionanswer']);
            question::addQuestion($subject['subjectdb'],$args);
            question::resetRowsQuestionNumber($subject['subjectdb'],$args['questionparent']);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "index.php?exam-master-exams-questionrowsdetail&qrid={$qrid}"
            );
            exit(json_encode($message));
        }
        else
        {
            $points = points::getPointsBySubjectid($subjectid);
            $sections = $points['sections'];
            $points = $points['points'];
            $app = apps::getAppByCode('exam');
            $app['appsetting']['selectornumbers'] = explode(',',$app['appsetting']['selectornumbers']);
            $app['appsetting']['selectortype'] = explode(',',$app['appsetting']['selectortype']);
            \PHPEMSPRO\tpl::getInstance()->assign('setting',$app['appsetting']);
            \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
            \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
            \PHPEMSPRO\tpl::getInstance()->assign('sections',$sections);
            \PHPEMSPRO\tpl::getInstance()->assign('points',$points);
            \PHPEMSPRO\tpl::getInstance()->assign('qrid',$qrid);
            \PHPEMSPRO\tpl::getInstance()->display('exams_addchildquestion');
        }
    }

    public function questionrowsdetail()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $qrid = \PHPEMSPRO\route::get('qrid');
        $questionrows = question::getQuestionRowsById($subject['subjectdb'],$qrid);
        $questypes = question::getQuestypesByArgs(array(array("AND","find_in_set(questcode,:questcode)","questcode",implode(',',$subject['subjectsetting']))));
        $points = points::getPointsBySubjectid($subjectid);
        $sections = $points['sections'];
        $points = $points['points'];
        $tmp = array();
        foreach($points as $pts)
        {
            foreach($pts as $p)
            {
                $tmp[$p['pointid']] = $p;
            }
        }
        $points = $tmp;
        \PHPEMSPRO\tpl::getInstance()->assign('questionrows',$questionrows);
        \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
        \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
        \PHPEMSPRO\tpl::getInstance()->assign('sections',$sections);
        \PHPEMSPRO\tpl::getInstance()->assign('points',$points);
        \PHPEMSPRO\tpl::getInstance()->display('exams_questionrowsdetail');
    }

    public function hidequestionrow()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $questionid = \PHPEMSPRO\route::get('qrid');
        question::hideQuestionrows($subject['subjectdb'],$questionid);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function hidequestionrows()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $delids = \PHPEMSPRO\route::get('delids');
        foreach($delids as $id => $p)
        {
            question::hideQuestionrows($subject['subjectdb'],$id);
        }
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function questionrowsbatch()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        switch(\PHPEMSPRO\route::get('action'))
        {
            case 'delete':
                $delids = \PHPEMSPRO\route::get('delids');
                foreach($delids as $id => $p)
                {
                    question::hideQuestionrows($subject['subjectdb'],$id);
                }
                break;

            case 'move':
                $delids = \PHPEMSPRO\route::get('delids');
                $ids = array();
                foreach($delids as $id => $p)
                {
                    $ids[] = $id;
                }
                $ids = implode(',',$ids);
                $points = points::getPointsBySubjectid($subjectid);
                $sections = $points['sections'];
                $points = $points['points'];
                \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
                \PHPEMSPRO\tpl::getInstance()->assign('sections',$sections);
                \PHPEMSPRO\tpl::getInstance()->assign('points',$points);
                \PHPEMSPRO\tpl::getInstance()->assign('ids',$ids);
                \PHPEMSPRO\tpl::getInstance()->display('exams_movequestionrows');
                exit();
                break;

            case 'setknows':
                $questionids = explode(',',\PHPEMSPRO\route::get('questionids'));
                $args = \PHPEMSPRO\route::get('args');
                foreach($questionids as $id)
                {
                    $questionrows = question::getQuestionRowsById($subject['subjectdb'],$id);
                    question::modifyQuestionRows($subject['subjectdb'],$id,array('qrpoints' => $args['questionpoints']));
                    foreach($questionrows['data'] as $question)
                    {
                        question::modifyQuestion($subject['subjectdb'],$question['questionid'],array('questionpoints' => $args['questionpoints']));
                    }
                }
                break;
        }
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function modifyquestionrows()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $qrid = \PHPEMSPRO\route::get('qrid');
        $questionrows = question::getQuestionRowsById($subject['subjectdb'],$qrid);
        $questypes = question::getQuestypesByArgs(array(array("AND","find_in_set(questcode,:questcode)","questcode",implode(',',$subject['subjectsetting']))));
        if(\PHPEMSPRO\route::get('modifyquestionrows'))
        {
            $args = \PHPEMSPRO\route::get('args');
            $args['qrpoints'] = $args['questionpoints'];
            unset($args['questionpoints']);
            if(!$args['qrpoints'])
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "请选择知识点"
                );
                exit(json_encode($message));
            }
            question::modifyQuestionRows($subject['subjectdb'],$qrid,$args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "reload"
            );
            exit(json_encode($message));
        }
        else
        {
            $points = points::getPointsBySubjectid($subjectid);
            $sections = $points['sections'];
            $points = $points['points'];
            $tmp = array();
            foreach($points as $pts)
            {
                foreach($pts as $p)
                {
                    $tmp[$p['pointid']] = $p;
                }
            }
            $points = $tmp;
            \PHPEMSPRO\tpl::getInstance()->assign('questionrows',$questionrows);
            \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
            \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
            \PHPEMSPRO\tpl::getInstance()->assign('sections',$sections);
            \PHPEMSPRO\tpl::getInstance()->assign('points',$points);
            \PHPEMSPRO\tpl::getInstance()->display('exams_modifyquestionrows');
        }
    }

    public function addquestionrows()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $questypes = question::getQuestypesByArgs(array(array("AND","find_in_set(questcode,:questcode)","questcode",implode(',',$subject['subjectsetting']))));
        if(\PHPEMSPRO\route::get('addquestionrows'))
        {
            $args = \PHPEMSPRO\route::get('args');
            $args['qrpoints'] = $args['questionpoints'];
            $args["qrauthor"] = $this->_user['username'];
            unset($args['questionpoints']);
            if(!$args['qrpoints'])
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "请选择知识点"
                );
                exit(json_encode($message));
            }
            question::addQuestionRows($subject['subjectdb'],$args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "index.php?exam-master-exams-questionrows"
            );
            exit(json_encode($message));
        }
        else
        {
            $points = points::getPointsBySubjectid($subjectid);
            $sections = $points['sections'];
            $points = $points['points'];
            \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
            \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
            \PHPEMSPRO\tpl::getInstance()->assign('sections',$sections);
            \PHPEMSPRO\tpl::getInstance()->assign('points',$points);
            \PHPEMSPRO\tpl::getInstance()->display('exams_addquestionrows');
        }
    }

    public function questionrows()
    {
        if(\PHPEMSPRO\route::get('subjectid'))
        {
            $subjectid = \PHPEMSPRO\route::get('subjectid');
            $_SESSION['subjectid'] = $subjectid;
        }
        else
        {
            $subjectid = $_SESSION['subjectid'];
        }
        $subject = points::getSubjectById($subjectid);
        $points = points::getPointsBySubjectid($subjectid);
        $sections = $points['sections'];
        $points = $points['points'];
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 0?$page:1;
        $args = array();
        if($this->search['qrid'])
        {
            $args[] = array("AND","qrid = :qrid",'qrid',$this->search['qrid']);
        }
        if($this->search['keyword'])
        {
            $args[] = array("AND","qrquestion LIKE :qrquestion",'qrquestion','%'.$this->search['keyword'].'%');
        }
        if($this->search['stime'])
        {
            $args[] = array("AND","qrtime >= :questioncreatetime",'questioncreatetime',strtotime($this->search['stime']));
        }
        if($this->search['etime'])
        {
            $args[] = array("AND","qrtime <= :questionendtime",'questionendtime',strtotime($this->search['etime']));
        }
        if($this->search['questiontype'])
        {
            $args[] = array("AND","qrtype = :questiontype",'questiontype',$this->search['questiontype']);
        }
        if($this->search['questionlevel'])
        {
            $args[] = array("AND","qrlevel = :questionlevel",'questionlevel',$this->search['questionlevel']);
        }
        if($this->search['questionsectionid'])
        {
            if($this->search['questionpointid'])
            {
                $args[] = array("AND","find_in_set(:qrpointid,qrpoints)","qrpointid",$this->search['questionpointid']);
            }
            else
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "请选择知识点"
                );
                exit(json_encode($message));
            }
        }
        else
        {
            $args[] = array("AND","qrsubject = :subjectid","subjectid",$subjectid);
        }
        $args[] = array("AND","qrstatus = 1");
        $questionrows = question::getQuestionRowsList($subject['subjectdb'],$args,$page);
        $questypes = question::getQuestypesByArgs(array(array("AND","find_in_set(questcode,:questcode)","questcode",implode(',',$subject['subjectsetting']))));
        \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
        \PHPEMSPRO\tpl::getInstance()->assign('sections',$sections);
        \PHPEMSPRO\tpl::getInstance()->assign('points',$points);
        \PHPEMSPRO\tpl::getInstance()->assign('page',$page);
        \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
        \PHPEMSPRO\tpl::getInstance()->assign('questionrows',$questionrows);
        \PHPEMSPRO\tpl::getInstance()->display('exams_questionrows');
    }

    public function exportquestionrows()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $args = array();
        if($this->search['qrid'])
        {
            $args[] = array("AND","qrid = :qrid",'qrid',$this->search['qrid']);
        }
        if($this->search['keyword'])
        {
            $args[] = array("AND","qrquestion LIKE :qrquestion",'qrquestion','%'.$this->search['keyword'].'%');
        }
        if($this->search['stime'])
        {
            $args[] = array("AND","qrtime >= :questioncreatetime",'questioncreatetime',strtotime($this->search['stime']));
        }
        if($this->search['etime'])
        {
            $args[] = array("AND","qrtime <= :questionendtime",'questionendtime',strtotime($this->search['etime']));
        }
        if($this->search['questiontype'])
        {
            $args[] = array("AND","qrtype = :questiontype",'questiontype',$this->search['questiontype']);
        }
        if($this->search['questionlevel'])
        {
            $args[] = array("AND","qrlevel = :questionlevel",'questionlevel',$this->search['questionlevel']);
        }
        if($this->search['questionsectionid'])
        {
            if($this->search['questionpointid'])
            {
                $args[] = array("AND","find_in_set(:qrpointid,qrpoints)","qrpointid",$this->search['questionpointid']);
            }
            else
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "请选择知识点"
                );
                exit(json_encode($message));
            }
        }
        else
        {
            $args[] = array("AND","qrsubject = :subjectid","subjectid",$subjectid);
        }
        $args[] = array("AND","qrstatus = 1");
        $fname = 'public/data/out/questions/'.TIME.'.csv';
        if(\PHPEMSPRO\files::outCsv($fname,question::exportQuestionRows($subject['subjectdb'],$args)))
        {
            $message = array(
                'statusCode' => 200,
                "message" => "试题导出成功，转入下载页面，如果浏览器没有相应，请<a href=\"{$fname}\">点此下载</a>",
                "callbackType" => 'forward',
                "forwardUrl" => "{$fname}"
            );
        }
        else
        {
            $message = array(
                'statusCode' => 300,
                "message" => "试题导出失败"
            );
        }
        exit(json_encode($message));
    }

    public function exportquestions()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $args = array();
        if($this->search['questionid'])
        {
            $args[] = array("AND","questionid = :questionid",'questionid',$this->search['questionid']);
        }
        if($this->search['keyword'])
        {
            $args[] = array("AND","question LIKE :question",'question','%'.$this->search['keyword'].'%');
        }
        if($this->search['stime'])
        {
            $args[] = array("AND","questiontime >= :questioncreatetime",'questioncreatetime',strtotime($this->search['stime']));
        }
        if($this->search['etime'])
        {
            $args[] = array("AND","questiontime <= :questionendtime",'questionendtime',strtotime($this->search['etime']));
        }
        if($this->search['questiontype'])
        {
            $args[] = array("AND","questiontype = :questiontype",'questiontype',$this->search['questiontype']);
        }
        if($this->search['questionlevel'])
        {
            $args[] = array("AND","questionlevel = :questionlevel",'questionlevel',$this->search['questionlevel']);
        }
        if($this->search['questionsectionid'])
        {
            if($this->search['questionpointid'])
            {
                $args[] = array("AND","find_in_set(:questionpointid,questionpoints)","questionpointid",$this->search['questionpointid']);
            }
            else
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "请选择知识点"
                );
                exit(json_encode($message));
            }
        }
        else
        {
            $args[] = array("AND","questionsubject = :subjectid","subjectid",$subjectid);
        }
        $args[] = array("AND","questionstatus = 1");
        $args[] = array("AND","questionparent = 0");
        $fname = 'public/data/out/questions/'.TIME.'.csv';
        if(\PHPEMSPRO\files::outCsv($fname,question::exportQuestions($subject['subjectdb'],$args)))
        {
            $message = array(
                'statusCode' => 200,
                "message" => "试题导出成功，转入下载页面，如果浏览器没有相应，请<a href=\"{$fname}\">点此下载</a>",
                "callbackType" => 'forward',
                "forwardUrl" => "{$fname}"
            );
        }
        else
        {
            $message = array(
                'statusCode' => 300,
                "message" => "试题导出失败"
            );
        }
        exit(json_encode($message));
    }

    public function importquestions()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        if(\PHPEMSPRO\route::get('importquestions'))
        {
            $uploadfile = \PHPEMSPRO\route::get('uploadfile');
            if(!file_exists($uploadfile))
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "上传文件不存在"
                );
                exit(json_encode($message));
            }
            else
            {
                $number = question::importQuestions($uploadfile,$subject,$this->_user['username']);
                $message = array(
                    'statusCode' => 200,
                    "message" => "操作成功,导入普通试题{$number['question']}道,题帽题{$number['questionrows']}道",
                    "callbackType" => "forward",
                    "forwardUrl" => "index.php?exam-master-exams-questions"
                );
                exit(json_encode($message));
            }
        }
        else
        {
            \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
            \PHPEMSPRO\tpl::getInstance()->display('exams_importquestions');
        }
    }

    public function removemember()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $obid = \PHPEMSPRO\route::get('obid');
        \PHPEMSPRO\exam\model\exams::delBasicMember($subject['subjectdb'],$obid);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function addmember()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $basicid = \PHPEMSPRO\route::get('basicid');
        $basic = \PHPEMSPRO\exam\model\exams::getBasicById($subject['subjectdb'],$basicid);
        if(\PHPEMSPRO\route::get('addmember'))
        {
            $delids = \PHPEMSPRO\route::get('delids');
            $days = \PHPEMSPRO\route::get('days');
            $number = array('new' => 0,'old' => 0);
            foreach($delids as $key => $p)
            {
                $user = \PHPEMSPRO\user\model\users::getUserById($key);
                if($user['username'])
                {
                    $r = \PHPEMSPRO\exam\model\exams::getIsMember($subject['subjectdb'],$user['username'],$basicid);
                    if(!$r)
                    {
                        $args = array(
                            'obbasicid' => $basicid,
                            'obusername' => $user['username'],
                            'obtime' => TIME,
                            'obendtime' => TIME + 24*3600*$days
                        );
                        \PHPEMSPRO\exam\model\exams::addBasicMember($subject['subjectdb'],$args);
                        $number['new']++;
                    }
                    elseif(strtotime($r['obendtime']) < TIME)
                    {
                        $args = array(
                            'obendtime' => TIME + 24*3600*$days
                        );
                        \PHPEMSPRO\exam\model\exams::modifyBasicMember($subject['subjectdb'],$r['obid'],$args);
                        $number['old']++;
                    }
                    elseif(strtotime($r['obendtime']) >= TIME)
                    {
                        $args = array(
                            'obendtime' => strtotime($r['obendtime']) + 24*3600*$days
                        );
                        \PHPEMSPRO\exam\model\exams::modifyBasicMember($subject['subjectdb'],$r['obid'],$args);
                        $number['old']++;
                    }
                }
            }
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功，新开通{$number['new']}人，延长时间{$number['old']}人",
                "callbackType" => "forward",
                "forwardUrl" => "reload"
            );
            exit(json_encode($message));
        }
        else
        {
            $page = \PHPEMSPRO\route::get('page');
            $page = $page >= 1?$page:1;
            $args = array();
            if($this->search['userid'])$args[] = array('AND',"userid = :userid",'userid',$this->search['userid']);
            if($this->search['username'])$args[] = array('AND',"username LIKE :username",'username','%'.$this->search['username'].'%');
            if($this->search['useremail'])$args[] = array('AND',"useremail  LIKE :useremail",'useremail','%'.$this->search['useremail'].'%');
            if($this->search['userphone'])$args[] = array('AND',"userphone  LIKE :userphone",'userphone','%'.$this->search['userphone'].'%');
            if($this->search['groupcode'])$args[] = array('AND',"usergroupcode = :usergroupcode",'usergroupcode',$this->search['groupcode']);
            if($this->search['stime'] || $this->search['etime'])
            {
                if(!is_array($args))$args = array();
                if($this->search['stime']){
                    $stime = strtotime($this->search['stime']);
                    $args[] = array('AND',"userregtime >= :stime",'stime',$stime);
                }
                if($this->search['etime']){
                    $etime = strtotime($this->search['etime']);
                    $args[] = array('AND',"userregtime <= :etime",'etime',$etime);
                }
            }
            $users = \PHPEMSPRO\user\model\users::getUsersList($args,$page);
            $groups = \PHPEMSPRO\user\model\users::getGroups();
            \PHPEMSPRO\tpl::getInstance()->assign('basic',$basic);
            \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
            \PHPEMSPRO\tpl::getInstance()->assign('users',$users);
            \PHPEMSPRO\tpl::getInstance()->assign('groups',$groups);
            \PHPEMSPRO\tpl::getInstance()->display('exams_addmember');
        }
    }

    public function monitoroperate()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        switch(\PHPEMSPRO\route::get('operate'))
        {
            case 'submit':
                $esid = \PHPEMSPRO\route::get('esid');
                $es = examsession::getExamsessionById($subject['subjectdb'],$esid);
                $sessionvars = json_decode(\PHPEMSPRO\pedis::getInstance()->getHashData('examsession_'.$es['esusername'],$es['estoken']),true);
                if(!$sessionvars)$sessionvars = $es['esdata'];
                question::monitorpaper($subject['subjectdb'],$sessionvars);
                examsession::delExamsession($subject['subjectdb'],$esid);
                \PHPEMSPRO\pedis::getInstance()->delHashData('examsession_'.$es['esusername'],$es['estoken']);
                break;

            case 'delete':
                $esid = \PHPEMSPRO\route::get('esid');
                examsession::delExamsession($subject['subjectdb'],$esid);
                break;
				
			case 'delaytime':
                $username = \PHPEMSPRO\route::get('username');
				$token = \PHPEMSPRO\route::get('token');
				$delaytime = intval(\PHPEMSPRO\route::get('delaytime'));
				if($delaytime < 0)$delaytime = 0;
                \PHPEMSPRO\pedis::getInstance()->setHashData('delaytime_'.$username,$token,$delaytime);
                break;

            default:
                break;
        }
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => 'forward',
            "forwardUrl" => "reload"
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function monitor()
    {
        $page = \PHPEMSPRO\route::get('page');
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $basicid = \PHPEMSPRO\route::get('basicid');
        $basic = \PHPEMSPRO\exam\model\exams::getBasicById($subject['subjectdb'],$basicid);
        $args = array(
            array("AND","essubjectid = :essubjectid","essubjectid",$subjectid),
            array("AND","esbasicid = :esbasicid","esbasicid",$basicid),
            array("AND","estype = 2")
        );
        $examsessions = examsession::getExamsessionsList($subject['subjectdb'],$args,$page);
		foreach($examsessions['data'] as $key => $es)
		{
			$delaytime = intval(\PHPEMSPRO\pedis::getInstance()->getHashData('delaytime_'.$es['esusername'],$es['estoken']));
			$examsessions['data'][$key]['delaytime'] = $delaytime;
		}
        \PHPEMSPRO\tpl::getInstance()->assign('basic',$basic);
        \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
        \PHPEMSPRO\tpl::getInstance()->assign('examsessions',$examsessions);
        \PHPEMSPRO\tpl::getInstance()->display('exams_monitor');
    }

    public function batopen()
    {
        if(\PHPEMSPRO\route::get('subjectid'))
        {
            $subjectid = \PHPEMSPRO\route::get('subjectid');
            $_SESSION['subjectid'] = $subjectid;
        }
        else
        {
            $subjectid = $_SESSION['subjectid'];
        }
        $subject = points::getSubjectById($subjectid);
        $basicid = \PHPEMSPRO\route::get('basicid');
        $basic = \PHPEMSPRO\exam\model\exams::getBasicById($subject['subjectdb'],$basicid);
        if(\PHPEMSPRO\route::get('batopen'))
        {
            $groupcode = \PHPEMSPRO\route::get('groupcode');
            $users = users::getUserByArgs(array(
                array("AND","usergroupcode = :usergroupcode","usergroupcode",$groupcode)
            ));
            $days = \PHPEMSPRO\route::get('days');
            $number = array('new' => 0,'old' => 0);
            foreach($users as $user)
            {
                $r = \PHPEMSPRO\exam\model\exams::getIsMember($subject['subjectdb'],$user['username'],$basicid);
                if(!$r)
                {
                    $args = array(
                        'obbasicid' => $basicid,
                        'obusername' => $user['username'],
                        'obtime' => TIME,
                        'obendtime' => TIME + 24*3600*$days
                    );
                    \PHPEMSPRO\exam\model\exams::addBasicMember($subject['subjectdb'],$args);
                    $number['new']++;
                }
                elseif(strtotime($r['obendtime']) < TIME)
                {
                    $args = array(
                        'obendtime' => TIME + 24*3600*$days
                    );
                    \PHPEMSPRO\exam\model\exams::modifyBasicMember($subject['subjectdb'],$r['obid'],$args);
                    $number['old']++;
                }
                elseif(strtotime($r['obendtime']) >= TIME)
                {
                    $args = array(
                        'obendtime' => strtotime($r['obendtime']) + 24*3600*$days
                    );
                    \PHPEMSPRO\exam\model\exams::modifyBasicMember($subject['subjectdb'],$r['obid'],$args);
                    $number['old']++;
                }
            }
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功，新开通{$number['new']}人，延长时间{$number['old']}人",
                "callbackType" => "forward",
                "forwardUrl" => "index.php?exam-master-exams-members&basicid={$basicid}"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        else
        {
            \PHPEMSPRO\tpl::getInstance()->assign('basic',$basic);
            \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
            \PHPEMSPRO\tpl::getInstance()->display('exams_batopen');
        }
    }

    public function members()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 0?$page:1;
        $basicid = \PHPEMSPRO\route::get('basicid');
        $basic = \PHPEMSPRO\exam\model\exams::getBasicById($subject['subjectdb'],$basicid);
        $groups = \PHPEMSPRO\user\model\users::getGroups();
        $args = array();
        $args[] = array("AND","obbasicid = :obbasicid","obbasicid",$basicid);
        $args[] = array("AND","obusername = username");
        if($this->search['stime'])
        {
            $args[] = array("AND",'obtime >= :stime','stime',strtotime($this->search['stime']));
        }
        if($this->search['etime'])
        {
            $args[] = array("AND",'obtime >= :etime','etime',strtotime($this->search['etime']));
        }
        if($this->search['username'])
        {
            $args[] = array("AND",'obusername LIKE :username','username','%'.$this->search['username'].'%');
        }
        $members = \PHPEMSPRO\exam\model\exams::getBasicMember($subject['subjectdb'],$args,$page);
        \PHPEMSPRO\tpl::getInstance()->assign('groups',$groups);
        \PHPEMSPRO\tpl::getInstance()->assign('basic',$basic);
        \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
        \PHPEMSPRO\tpl::getInstance()->assign('members',$members);
        \PHPEMSPRO\tpl::getInstance()->display('exams_members');
    }

    public function stats()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $search = \PHPEMSPRO\route::get('search');
        $page = \PHPEMSPRO\route::get('page');
        if($page < 1)$page = 1;
        $args = array();
        $basicid = \PHPEMSPRO\route::get('basicid');
        $args[] =  array('AND',"ehbasicid = :ehbasicid",'ehbasicid',$basicid);
        if($search['stime'])
        {
            $stime = strtotime($search['stime']);
            $args[] = array('AND',"ehstarttime >= :stime",'stime',$stime);
        }
        if($search['etime'])
        {
            $etime = strtotime($search['etime']);
            $args[] = array('AND',"ehstarttime <= :etime",'etime',$etime);
        }
        if($search['sscore'])
        {
            $args[] = array('AND',"ehscore >= :sscore",'sscore',$search['sscore']);
        }
        if($search['escore'])
        {
            $args[] = array('AND',"ehscore <= :escore",'escore',$search['escore']);
        }
        if($search['examid'])
        {
            $args[] = array('AND',"ehpaperid = :ehpaperid",'ehpaperid',$search['ehpaperid']);
        }
        $rs = favor::getExamHistoriesByArgs($subject['subjectdb'],$args);
        $number = count($rs);
        $basic = \PHPEMSPRO\exam\model\exams::getBasicById($subject['subjectdb'],$basicid);
        $stats = array();
        $os = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H');
        $questiontype = question::getQuestypesByArgs();
        foreach ($rs as $p) {
            foreach ($p['ehquestion']['questions'] as $questions) {
                foreach ($questions as $key => $question) {
                    $stats[$question['questionid']]['title'] = $question['question'];
                    $stats[$question['questionid']]['id'] = $question['questionid'];
                    if ($p['ehscorelist'][$question['questionid']] > 0)
                        $stats[$question['questionid']]['right'] = intval($stats[$question['questionid']]['right']) + 1;
                    $stats[$question['questionid']]['number'] = intval($stats[$question['questionid']]['number']) + 1;
                    if ($p['ehuseranswer'][$question['questionid']] && $questiontype[$question['questiontype']]['questsort'] == 0 && $questiontype[$question['questiontype']]['questchoice'] < 5) {
                        $p['ehuseranswer'][$question['questionid']] = implode("", $p['ehuseranswer'][$question['questionid']]);
                        foreach ($os as $o) {
                            if (strpos($p['ehuseranswer'][$question['questionid']], $o) !== false)
                                $stats[$question['questionid']][$o] = intval($stats[$question['questionid']][$o]) + 1;
                        }
                    }
                }
            }
            foreach ($p['ehquestion']['questionrows'] as $questionrows)
            {
                foreach ($questionrows as $questionrow)
                {
                    foreach ($questionrow['data'] as $key => $question)
                    {
                        $stats[$question['questionid']]['title'] = $questionrow['qrquestion'] . '<br />' . $question['question'];
                        $stats[$question['questionid']]['id'] = $question['questionid'];
                        if ($p['ehscorelist'][$question['questionid']] > 0)
                        {
                            $stats[$question['questionid']]['right'] = intval($stats[$question['questionid']]['right']) + 1;
                        }
                        $stats[$question['questionid']]['number'] = intval($stats[$question['questionid']]['number']) + 1;
                        if ($p['ehuseranswer'][$question['questionid']] && $questiontype[$question['questiontype']]['questsort'] == 0 && $questiontype[$question['questiontype']]['questchoice'] < 5)
                        {
                            $p['ehuseranswer'][$question['questionid']] = implode("", $p['ehuseranswer'][$question['questionid']]);
                            foreach ($os as $o)
                            {
                                if (strpos($p['ehuseranswer'][$question['questionid']], $o) !== false)
                                {
                                    $stats[$question['questionid']][$o] = intval($stats[$question['questionid']][$o]) + 1;
                                }
                            }
                        }
                    }
                }
            }
        }
        ksort($stats);
        $start = $page - 1;
        $start = $start >= 0 ? $start : 0;
        $tmp = array_slice($stats, $start * 20, 20);
        $pages = \PHPEMSPRO\pg::outPage(\PHPEMSPRO\pg::getPagesNumber(count($stats), 20), $page);
        \PHPEMSPRO\tpl::getInstance()->assign('stats', array('data' => $tmp, 'pages' => $pages));
        \PHPEMSPRO\tpl::getInstance()->assign('basic', $basic);
        \PHPEMSPRO\tpl::getInstance()->assign('subject', $subject);
        \PHPEMSPRO\tpl::getInstance()->assign('page',$page);
        \PHPEMSPRO\tpl::getInstance()->display('exam_stats');
    }

    public function scorelist()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 0?$page:1;
        $basicid = \PHPEMSPRO\route::get('basicid');
        $basic = \PHPEMSPRO\exam\model\exams::getBasicById($subject['subjectdb'],$basicid);
        $args = array();
        $args[] = array("AND","ehbasicid = :ehbasicid","ehbasicid",$basicid);
        //$args[] = array('AND',"ehtype = '2'");
        $args[] = array('AND',"ehstatus = '1'");
        if($this->search['stime'])
        {
            $stime = strtotime($this->search['stime']);
            $args[] = array('AND',"ehstarttime >= :stime",'stime',$stime);
        }
        if($this->search['etime'])
        {
            $etime = strtotime($this->search['etime']);
            $args[] = array('AND',"ehstarttime <= :etime",'etime',$etime);
        }
        if($this->search['sscore'])
        {
            $args[] = array('AND',"ehscore >= :sscore",'sscore',$this->search['sscore']);
        }
        if($this->search['escore'])
        {
            $args[] = array('AND',"ehscore <= :escore",'escore',$this->search['escore']);
        }
        if($this->search['paperid'])
        {
            $args[] = array('AND',"ehpaperid = :ehpaperid",'ehpaperid',$this->search['paperid']);
        }
        $scores = favor::getUserExamHistoryList($subject['subjectdb'],$args,$page);
        $paperids = implode(',',explode(',',str_replace(' ','',trim($basic['basicexam']['self'],' ,').trim($basic['basicexam']['auto'],' ,'))));
        $papers = \PHPEMSPRO\exam\model\exams::getPapersByArgs($subject['subjectdb'],array(array("AND","find_in_set(paperid,:paperid)","paperid",$paperids)));
        \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
        \PHPEMSPRO\tpl::getInstance()->assign('basic',$basic);
        \PHPEMSPRO\tpl::getInstance()->assign('scores',$scores);
        \PHPEMSPRO\tpl::getInstance()->assign('papers',$papers);
        \PHPEMSPRO\tpl::getInstance()->assign('fields',array(
            array('fieldtitle' => '真实姓名','field' => 'userrealname')
        ));
        \PHPEMSPRO\tpl::getInstance()->display('exams_scorelist');
    }

    public function outscore()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 0?$page:1;
        $basicid = \PHPEMSPRO\route::get('basicid');
        $basic = \PHPEMSPRO\exam\model\exams::getBasicById($subject['subjectdb'],$basicid);
        $app = apps::getAppByCode('exam');
        $fields = explode(',',str_replace(' ','',$app['appsetting']['outfields']));
        $args = array();
        $args[] = array("AND","ehbasicid = :ehbasicid","ehbasicid",$basicid);
        //$args[] = array('AND',"ehtype = '2'");
        $args[] = array('AND',"ehstatus = '1'");
        if($this->search['stime'])
        {
            $stime = strtotime($this->search['stime']);
            $args[] = array('AND',"ehstarttime >= :stime",'stime',$stime);
        }
        if($this->search['etime'])
        {
            $etime = strtotime($this->search['etime']);
            $args[] = array('AND',"ehstarttime <= :etime",'etime',$etime);
        }
        if($this->search['sscore'])
        {
            $args[] = array('AND',"ehscore >= :sscore",'sscore',$this->search['sscore']);
        }
        if($this->search['escore'])
        {
            $args[] = array('AND',"ehscore <= :escore",'escore',$this->search['escore']);
        }
        if($this->search['examid'])
        {
            $args[] = array('AND',"ehexamid = :ehexamid",'ehexamid',$this->search['examid']);
        }
        if(!$fields)
        {
            $fstr = 'ehusername,ehstarttime,ehtime';
            $fields = explode(',',$fstr);
        }
        else
        {
            $fstr = implode(',',$fields);
        }
        $scores = favor::outUserExamHistory($subject['subjectdb'],$args,$fstr);
        $r = array();
        foreach($scores as $score)
        {
            $tmp = array();
            foreach($fields as $field)
            {
                $tmp[$field] = iconv("UTF-8","GBK",$score[$field]);
            }
            $r[] = $tmp;
        }
        $filename = 'public/data/out/score/'.TIME.'.csv';
        if(\PHPEMSPRO\files::outCsv($filename,$r))
        {
            $message = array(
                'statusCode' => 200,
                "message" => "成绩导出成功，转入下载页面，如果浏览器没有相应，请<a href=\"{$filename}\">点此下载</a>",
                "callbackType" => 'forward',
                "forwardUrl" => "{$filename}"
            );
        }
        else
        {
            $message = array(
                'statusCode' => 300,
                "message" => "成绩导出失败"
            );
        }
        exit(json_encode($message));
    }

    public function decide()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 0?$page:1;
        $basicid = \PHPEMSPRO\route::get('basicid');
        $basic = \PHPEMSPRO\exam\model\exams::getBasicById($subject['subjectdb'],$basicid);
        $args = array();
        $args[] = array("AND","ehbasicid = :ehbasicid","ehbasicid",$basicid);
        $args[] = array('AND',"ehstatus = '0'");
        $scores = favor::getUserExamHistoryList($subject['subjectdb'],$args,$page);
        \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
        \PHPEMSPRO\tpl::getInstance()->assign('basic',$basic);
        \PHPEMSPRO\tpl::getInstance()->assign('scores',$scores);
        \PHPEMSPRO\tpl::getInstance()->display('exams_decide');
    }

    public function savescore()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $ehid = \PHPEMSPRO\route::get('ehid');
        $history = favor::getExamHistoryById($subject['subjectdb'],$ehid);
        $basic = \PHPEMSPRO\exam\model\exams::getBasicById($subject['subjectdb'],$history['ehbasicid']);
        if(\PHPEMSPRO\route::get('makedecide'))
        {
            $score = \PHPEMSPRO\route::get('score');
            foreach($score as $key => $p)
            {
                $history['ehscorelist'][$key] = floatval($p);
            }
            $score = array_sum($history['ehscorelist']);
            favor::modifyExamHistory($subject['subjectdb'],$ehid,array('ehscorelist' => $history['ehscorelist'],'ehscore' => $score,'ehstatus' => 1));
            $message = array(
                'statusCode' => 200,
                'message' => '评分完成',
                "callbackType" => "forward",
                "forwardUrl" => "back"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        else {
            if (!$history['ehstatus'] && !$history['ehteacher'])
            {
                $args = array();
                $sessionvars['ehteacher'] = $args['ehteacher'] = $this->_user['username'];
                $sessionvars['ehdecidetime'] = $args['ehdecidetime'] = TIME;
                favor::modifyExamHistory($subject['subjectdb'], $ehid, $args);
            }
            elseif($history['ehteacher'] != $this->_user['username'])
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "本试卷已被{$history['ehteacher']}锁定批改中"
                );
                \PHPEMSPRO\route::urlJump($message);
            }
            $questypes = question::getQuestypesByArgs();
            $needdecide = array();
            foreach ($history['ehquestion']['questions'] as $key => $p) {
                if ($questypes[$key]['questsort']) {
                    $needdecide[$key] = 1;
                }
            }
            foreach ($history['ehquestion']['questionrows'] as $key => $p) {
                if ($questypes[$key]['questsort']) {
                    $needdecide[$key] = 1;
                } else {
                    foreach ($p as $q) {
                        foreach ($q['data'] as $qd) {
                            if ($questypes[$qd['questype']]['questsort']) {
                                $needdecide[$key] = 1;
                            }
                        }
                    }
                }
            }
            \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
            \PHPEMSPRO\tpl::getInstance()->assign('basic',$basic);
            \PHPEMSPRO\tpl::getInstance()->assign('questypes', $questypes);
            \PHPEMSPRO\tpl::getInstance()->assign('needdecide', $needdecide);
            \PHPEMSPRO\tpl::getInstance()->assign('history', $history);
            \PHPEMSPRO\tpl::getInstance()->assign('questypes', $questypes);
            \PHPEMSPRO\tpl::getInstance()->display('exam_savescore');
        }
    }

    public function paperview()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $ehid = \PHPEMSPRO\route::get('ehid');
        $history = favor::getExamHistoryById($subject['subjectdb'],$ehid);
        $basic = \PHPEMSPRO\exam\model\exams::getBasicById($subject['subjectdb'],$history['ehbasicid']);
        $questypes = question::getQuestypesByArgs();
        \PHPEMSPRO\tpl::getInstance()->assign('basic',$basic);
        \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
        \PHPEMSPRO\tpl::getInstance()->assign('questypes', $questypes);
        \PHPEMSPRO\tpl::getInstance()->assign('history', $history);
        \PHPEMSPRO\tpl::getInstance()->assign('questypes', $questypes);
        \PHPEMSPRO\tpl::getInstance()->display('exam_view');
    }

    public function dorecyle()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $type = \PHPEMSPRO\route::get('type');
        if($type != 'questionrows')
        {
            $type = 'questions';
        }
        $action = \PHPEMSPRO\route::get('action');
        if($type == 'questions')
        {
            switch($action)
            {
                case 'recover':
                    $delids = \PHPEMSPRO\route::get('delids');
                    foreach($delids as $id => $p)
                    {
                        question::recoverQuestion($subject['subjectdb'],$id);
                    }
                    break;
                case 'clearprocess':
                    $delids = \PHPEMSPRO\route::get('delids');
                    $basic = \PHPEMSPRO\exam\model\exams::getBasicById($subject['subjectdb'],$basicid);
                    foreach($delids as $obid => $p)
                    {
                        $ob = \PHPEMSPRO\exam\model\exams::getOpenBasicById($obid);
                        $record = favor::getRecordSession($subject['subjectdb'],$ob['obusername']);
                        foreach($basic['basicpoints'] as $points)
                        {
                            foreach($points as $point)
                            {
                                \PHPEMSPRO\pedis::getInstance()->delHashData('process',$ob['obusername'].'-'.$subject['subjectdb'].'-'.$point);
                                unset($record['recordnumber'][$point],$record['recordright'][$point],$record['recordwrong'][$point]);
                            }
                        }
                        favor::setRecordSession($subject['subjectdb'],$ob['obusername'],$record);
                    }
                    break;

                case 'delete':
                    $delids = \PHPEMSPRO\route::get('delids');
                    foreach($delids as $id => $p)
                    {
                        question::delQuestion($subject['subjectdb'],$id);
                    }
                    break;

                default:
                    break;
            }
        }
        else
        {
            switch($action)
            {
                case 'recover':
                    $delids = \PHPEMSPRO\route::get('delids');
                    foreach($delids as $id => $p)
                    {
                        question::recoverQuestionrows($subject['subjectdb'],$id);
                    }
                    break;

                case 'delete':
                    $delids = \PHPEMSPRO\route::get('delids');
                    foreach($delids as $id => $p)
                    {
                        question::delQuestionRows($subject['subjectdb'],$id);
                    }
                    break;

                default:
                    break;
            }
        }
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function clearrecyle()
    {
        if(\PHPEMSPRO\route::get('subjectid'))
        {
            $subjectid = \PHPEMSPRO\route::get('subjectid');
            $_SESSION['subjectid'] = $subjectid;
        }
        else
        {
            $subjectid = $_SESSION['subjectid'];
        }
        $subject = points::getSubjectById($subjectid);
        question::clearRecyle($subject['subjectdb'],$subject['subjectid']);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function recyle()
    {
        if(\PHPEMSPRO\route::get('subjectid'))
        {
            $subjectid = \PHPEMSPRO\route::get('subjectid');
            $_SESSION['subjectid'] = $subjectid;
        }
        else
        {
            $subjectid = $_SESSION['subjectid'];
        }
        $subject = points::getSubjectById($subjectid);
        $type = \PHPEMSPRO\route::get('type');
        if($type != 'questionrows')
        {
            $type = 'questions';
        }
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 0?$page:1;
        $args = array();
        if($type == 'questionrows')
        {
            $args[] = array("AND","qrsubject = :qrsubject","qrsubject",$subject['subjectid']);
			$args[] = array("AND","qrstatus = 0");
            $questionrows = question::getQuestionRowsList($subject['subjectdb'],$args,$page);
            \PHPEMSPRO\tpl::getInstance()->assign('questionrows',$questionrows);
        }
        else
        {
            $args[] = array("AND","questionsubject = :questionsubject","questionsubject",$subject['subjectid']);
			$args[] = array("AND","questionstatus = 0");
            $questions = question::getQuestionList($subject['subjectdb'],$args,$page);
            \PHPEMSPRO\tpl::getInstance()->assign('questions',$questions);
        }
        $questypes = question::getQuestypesByArgs(array(array("AND","find_in_set(questcode,:questcode)","questcode",implode(',',$subject['subjectsetting']))));
        \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
        \PHPEMSPRO\tpl::getInstance()->assign('type',$type);
        \PHPEMSPRO\tpl::getInstance()->assign('page',$page);
        \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
        \PHPEMSPRO\tpl::getInstance()->display('exams_recyle'.$type);
    }

    public function index()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 0?$page:1;
        $args = array();
        $subjects = points::getSubjectsList($args,$page);
        $trainings = training::getTrainingsByArgs();
        \PHPEMSPRO\tpl::getInstance()->assign('trainings',$trainings);
        \PHPEMSPRO\tpl::getInstance()->assign('subjects',$subjects);
        \PHPEMSPRO\tpl::getInstance()->display('exams');
    }
}