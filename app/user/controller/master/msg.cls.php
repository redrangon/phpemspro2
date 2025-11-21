<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/11/25
 * Time: 21:24
 */

namespace PHPEMSPRO\user\controller\master;

use PHPEMSPRO\user\master;

class msg extends master
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

    public function selectusers()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page >= 1?$page:1;
        \PHPEMSPRO\pg::setUrlTarget('modal-body" class="ajax');
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
        \PHPEMSPRO\tpl::getInstance()->display('msg_selectusers');
    }

    public function batdelmsr()
    {
        $delids = \PHPEMSPRO\route::get('delids');
        foreach($delids as $msrid => $p)
        {
            \PHPEMSPRO\user\model\msg::delMsgUser($msrid);
        }
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function delmsr($msrid)
    {
        $msrid = \PHPEMSPRO\route::get('msrid');
        \PHPEMSPRO\user\model\msg::delMsgUser($msrid);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function user()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 1?$page:1;
        $msgid = \PHPEMSPRO\route::get('msgid');
        $msg = \PHPEMSPRO\user\model\msg::getMsgById($msgid);
        $args = array();
        $args[] = array(
            "AND","msrmsgid = :msrmsgid","msrmsgid",$msgid
        );
        if($this->search['username'])$args[] = array('AND',"msrusername = :msrusername",'msrusername',$this->search['username']);
        $msrs = \PHPEMSPRO\user\model\msg::getUserMsgList($args,$page);
        \PHPEMSPRO\tpl::getInstance()->assign('msg',$msg);
        \PHPEMSPRO\tpl::getInstance()->assign('msrs',$msrs);
        \PHPEMSPRO\tpl::getInstance()->display('msg_user');
    }

    public function add()
    {
        if(\PHPEMSPRO\route::get('addmsg'))
        {
            $args = \PHPEMSPRO\route::get('args');
            $args['msgposter'] = $this->_user['username'];
            $msgid = \PHPEMSPRO\user\model\msg::addMsg($args);
            $users = \PHPEMSPRO\route::get('users');
            $users = explode(',',$users);
            foreach($users as $username)
            {
                if($username)
                {
                    $args = array(
                        'msrusername' => $username,
                        'msrmsgid' => $msgid,
                        'msrstatus' => 0
                    );
                    \PHPEMSPRO\user\model\msg::addMsgUser($args);
                }
            }
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "index.php?user-master-msg"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        else
        {
            \PHPEMSPRO\tpl::getInstance()->display('msg_add');
        }
    }

    public function batdel()
    {
        $delids = \PHPEMSPRO\route::get('delids');
        foreach($delids as $msgid => $p)
        {
            \PHPEMSPRO\user\model\msg::delMsg($msgid);
        }
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function del()
    {
        $msgid = \PHPEMSPRO\route::get('msgid');
        \PHPEMSPRO\user\model\msg::delMsg($msgid);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function index()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 1?$page:1;
        $args = array();
        if($this->search['stime'])
        {
            $stime = strtotime($this->search['stime']);
            $args[] = array('AND',"msgtime >= :msgstime",'msgstime',$stime);
        }
        if($this->search['etime'])
        {
            $etime = strtotime($this->search['etime']);
            $args[] = array('AND',"msgtime <= :msgetime",'msgetime',$etime);
        }
        if($this->search['keyword'])$args[] = array('AND',"msgtitle LIKE :keyword",'keyword','%'.$this->search['keyword'].'%');
        $msgs = \PHPEMSPRO\user\model\msg::getMsgList($args,$page);
        \PHPEMSPRO\tpl::getInstance()->assign('msgs',$msgs);
        \PHPEMSPRO\tpl::getInstance()->display('msg');
    }
}