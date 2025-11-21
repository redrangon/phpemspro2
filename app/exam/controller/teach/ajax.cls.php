<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/12/8
 * Time: 10:47
 */

namespace PHPEMSPRO\exam\controller\teach;


use PHPEMSPRO\exam\model\points;
use PHPEMSPRO\exam\model\question;
use PHPEMSPRO\exam\teach;

class ajax extends teach
{
    public function getpointsbysectionid()
    {
        $sectionid = \PHPEMSPRO\route::get('sectionid');
        $points = points::getPoints(array(array("AND","pointsection = :sectionid","sectionid",$sectionid)));
        foreach ($points as $p) {
            echo "<option value='{$p['pointid']}'>{$p['pointname']}</option>";
        }
        return;
    }

    public function selected()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $show = \PHPEMSPRO\route::get('show');
        $questionids = trim(\PHPEMSPRO\route::get('questionids')," ,");
        $rowsquestionids = trim(\PHPEMSPRO\route::get('questionrowsids')," ,");
        if(!$questionids)$questionids = '0';
        if(!$rowsquestionids)$rowsquestionids = '0';
        $questions = \PHPEMSPRO\exam\model\question::getQuestionsByArgs($subject['subjectdb'],array(array("AND","questionstatus = 1"),array("AND","find_in_set(questionid,:questionid)",'questionid',$questionids)));
        $questionrows = array();
        $rowsquestionids = explode(',',$rowsquestionids);
        foreach($rowsquestionids as $p)
        {
            if($p)
            {
                $r = \PHPEMSPRO\exam\model\question::getQuestionRowsById($subject['subjectdb'],$p);
                if($r['qrstatus'])
                {
                    $questionrows[$p] = $r;
                }
            }
        }
        \PHPEMSPRO\tpl::getInstance()->assign('questionrows',$questionrows);
        \PHPEMSPRO\tpl::getInstance()->assign('questions',$questions);
        \PHPEMSPRO\tpl::getInstance()->assign('show',$show);
        \PHPEMSPRO\tpl::getInstance()->display('ajax_selected');
    }

    public function selectquestions()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 0?$page:1;
        $search = \PHPEMSPRO\route::get('search');
        \PHPEMSPRO\pg::setUrlTarget('modal-body" class="ajax');
        if(!$search['questionisrows'])
        {
            $args = array(array("AND","questionstatus = '1'"),array("AND","questionparent = 0"));
            if($search['keyword'])
            {
                $args[] = array("AND","question LIKE :question",'question','%'.$search['keyword'].'%');
            }
            if($search['questionpointid'])
            {
                $args[] = array("AND","find_in_set(:questionpoints,questionpoints)",'questionpoints',$search['questionpointid']);
            }
            if($search['stime'])
            {
                $args[] = array("AND","questiontime >= :stime",'stime',strtotime($search['stime']));
            }
            if($search['etime'])
            {
                $args[] = array("AND","questiontime <= :etime",'etime',strtotime($search['etime']));
            }
            if($search['questiontype'])
            {
                $args[] = array("AND","questiontype = :questiontype",'questiontype',$search['questiontype']);
            }
            if($search['questionlevel'])
            {
                $args[] = array("AND","questionlevel = :questionlevel",'questionlevel',$search['questionlevel']);
            }
            $questions = question::getQuestionList($subject['subjectdb'],$args,$page);
        }
        else
        {
            $args = array(array("AND","qrstatus = '1'"));
            if($search['keyword'])
            {
                $args[] = array("AND","qrquestion LIKE :qrquestion",'qrquestion','%'.$search['keyword'].'%');
            }
            if($search['questionpointid'])
            {
                $args[] = array("AND","find_in_set(:qrpoints,qrpoints)",'qrpoints',$search['questionpointid']);
            }
            if($search['questiontype'])
            {
                $args[] = array("AND","qrtype = :qrtype",'qrtype',$search['questiontype']);
            }
            if($search['stime'])
            {
                $args[] = array("AND","qrtime >= :stime",'stime',strtotime($search['stime']));
            }
            if($search['etime'])
            {
                $args[] = array("AND","qrtime <= :etime",'etime',strtotime($search['etime']));
            }
            if($search['qrlevel'])
            {
                $args[] = array("AND","qrlevel = :qrlevel",'qrlevel',$search['qrlevel']);
            }
            $questions = question::getQuestionRowsList($subject['subjectdb'],$args,$page);
        }
        $points = points::getPointsBySubjectid($subjectid);
        $sections = $points['sections'];
        $points = $points['points'];
        \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
        \PHPEMSPRO\tpl::getInstance()->assign('search',$search);
        \PHPEMSPRO\tpl::getInstance()->assign('sections',$sections);
        \PHPEMSPRO\tpl::getInstance()->assign('points',$points);
        \PHPEMSPRO\tpl::getInstance()->assign('questions',$questions);
        \PHPEMSPRO\tpl::getInstance()->display('ajax_selectquestions');
    }

    public function selectpapers()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 0?$page:1;
        $target = \PHPEMSPRO\route::get('target');
        \PHPEMSPRO\pg::setUrlTarget('select-modal-body" class="ajax');
        $papers = \PHPEMSPRO\exam\model\exams::getPapersList($subject['subjectdb'],array(array("AND","papersubject = :papersubject","papersubject",$subjectid)),$page);
        \PHPEMSPRO\tpl::getInstance()->assign('papers',$papers);
        \PHPEMSPRO\tpl::getInstance()->assign('target',$target);
        \PHPEMSPRO\tpl::getInstance()->display('ajax_selectpapers');
    }

    public function selectgroups()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 0?$page:1;
        $target = \PHPEMSPRO\route::get('target');
        \PHPEMSPRO\pg::setUrlTarget('select-modal-body" class="ajax');
        $groups = \PHPEMSPRO\user\model\users::getGroups();
        \PHPEMSPRO\tpl::getInstance()->assign('groups',$groups);
        \PHPEMSPRO\tpl::getInstance()->assign('target',$target);
        \PHPEMSPRO\tpl::getInstance()->display('ajax_selectgroups');
    }

    public function questiondetail()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $questionid = \PHPEMSPRO\route::get('questionid');
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
        $question = question::getQuestionById($subject['subjectdb'],$questionid);
        \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
        \PHPEMSPRO\tpl::getInstance()->assign('question',$question);
        \PHPEMSPRO\tpl::getInstance()->assign('sections',$sections);
        \PHPEMSPRO\tpl::getInstance()->assign('points',$points);
        \PHPEMSPRO\tpl::getInstance()->display('ajax_questiondetail');
    }

    public function questionrowsdetail()
    {
        $subjectid = $_SESSION['subjectid'];
        $subject = points::getSubjectById($subjectid);
        $qrid = \PHPEMSPRO\route::get('qrid');
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
        $questionrows = question::getQuestionRowsById($subject['subjectdb'],$qrid);
        \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
        \PHPEMSPRO\tpl::getInstance()->assign('questionrows',$questionrows);
        \PHPEMSPRO\tpl::getInstance()->assign('sections',$sections);
        \PHPEMSPRO\tpl::getInstance()->assign('points',$points);
        \PHPEMSPRO\tpl::getInstance()->display('ajax_questionrowsdetail');
    }

    public function index()
    {
        return;
    }
}