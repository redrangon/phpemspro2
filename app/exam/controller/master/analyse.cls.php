<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\exam\controller\master;

use PHPEMSPRO\config;
use PHPEMSPRO\exam\master;
use PHPEMSPRO\exam\model\favor;
use PHPEMSPRO\exam\model\points;

class analyse extends master
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

    public function image()
    {
        $username = \PHPEMSPRO\route::get('username');
    }

    public function scores()
    {
        $page = \PHPEMSPRO\route::get('page');
        $db = \PHPEMSPRO\route::get('db');
        if(!config::db[$db])$db = 'default';
        $page = $page > 0?$page:1;
        $username = \PHPEMSPRO\route::get('username');
        $args = array();
        $args[] = array("AND","ehusername = :ehusername","ehusername",$username);
        $args[] = array('AND',"ehtype = '2'");
        $args[] = array('AND',"ehstatus = 1");
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
        $scores = favor::getUserExamHistoryList($db,$args,$page);
        $subjects = points::getSubjects(array(
            array("AND","subjectdb = :subjectdb","subjectdb",$db)
        ));
        \PHPEMSPRO\tpl::getInstance()->assign('subjects',$subjects);
        \PHPEMSPRO\tpl::getInstance()->assign('dbs',config::db);
        \PHPEMSPRO\tpl::getInstance()->assign('scores',$scores);
        \PHPEMSPRO\tpl::getInstance()->display('analyse_scores');
    }

    public function index()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page >= 1?$page:1;
        $args = array();
        if($this->search)
        {
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
                    $args[] = array('AND',"userregtime >= :userregtime",'userregtime',$stime);
                }
                if($this->search['etime']){
                    $etime = strtotime($this->search['etime']);
                    $args[] = array('AND',"userregtime <= :userregtime",'userregtime',$etime);
                }
            }
        }
        $users = \PHPEMSPRO\user\model\users::getUsersList($args,$page);
        $groups = \PHPEMSPRO\user\model\users::getGroups();
        \PHPEMSPRO\tpl::getInstance()->assign('users',$users);
        \PHPEMSPRO\tpl::getInstance()->assign('groups',$groups);
        \PHPEMSPRO\tpl::getInstance()->display('analyse');
    }
}