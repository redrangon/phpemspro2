<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/11/25
 * Time: 21:24
 */

namespace PHPEMSPRO\user\controller\master;

use PHPEMSPRO\excel;
use PHPEMSPRO\files;
use PHPEMSPRO\route;
use PHPEMSPRO\user\model\users;
use PHPEMSPRO\user\master;
use PHPEMSPRO\user\model\elem;

class verify extends master
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
            \PHPEMSPRO\tpl::getInstance()->assign('u',$this->u);
        }
    }

    public function unverify()
    {
        $delids = \PHPEMSPRO\route::get('delids');
        foreach($delids as $userid => $p)
        {
            users::modifyUser($userid,array('userverify' => 0));
        }
        $message = array(
            'statusCode' => 200,
            "message" => "撤回成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function verify()
    {
        $delids = \PHPEMSPRO\route::get('delids');
        foreach($delids as $userid => $p)
        {
            users::modifyUser($userid,array('userverify' => 1));
        }
        $message = array(
            'statusCode' => 200,
            "message" => "审核成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function detail()
    {
        $username = route::get('username');
        $user = \PHPEMSPRO\user\model\users::getUserByUsername($username);
        $group = \PHPEMSPRO\user\model\users::getGroupByCode($user['usergroupcode']);
        $fields = elem::$fields;
        $modelcode = $group['groupmodel'];
        $properties = \PHPEMSPRO\database\model\model::getAssignPropertiesByModelcode($modelcode,$fields['user']);
        $forms = \PHPEMSPRO\html::buildHtml($properties,$user);
        \PHPEMSPRO\tpl::getInstance()->assign('user',$user);
        \PHPEMSPRO\tpl::getInstance()->assign('forms',$forms);
        \PHPEMSPRO\tpl::getInstance()->display('verify_detail');
    }

    public function export()
    {
        $args = array();
        $args[] = array("AND","userverify = 1");
        if($this->search['userid'])$args[] = array('AND',"userid = :userid",'userid',$this->search['userid']);
        if($this->search['username'])$args[] = array('AND',"username LIKE :username",'username','%'.$this->search['username'].'%');
        if($this->search['useremail'])$args[] = array('AND',"useremail  LIKE :useremail",'useremail','%'.$this->search['useremail'].'%');
        if($this->search['userphone'])$args[] = array('AND',"userphone  LIKE :userphone",'userphone','%'.$this->search['userphone'].'%');
        if($this->search['groupcode'])$args[] = array('AND',"usergroupcode = :usergroupcode",'usergroupcode',$this->search['groupcode']);
        if($this->search['stime']){
            $stime = strtotime($this->search['stime']);
            $args[] = array('AND',"userregtime >= :userregtime",'userregtime',$stime);
        }
        if($this->search['etime']){
            $etime = strtotime($this->search['etime']);
            $args[] = array('AND',"userregtime <= :userregtime",'userregtime',$etime);
        }
        $users = \PHPEMSPRO\user\model\users::getUsersByArgs($args);
        $rs = array();
        $rs[] = array(
            '用户名','姓名','身份证号','邮箱','电话'
        );
        foreach($users as $user)
        {
            $rs[] = array(
                $user['username'],
                $user['userrealname'],
                $user['userpassport'],
                $user['useremail'],
                $user['userphone']
            );
        }
        $fname = 'public/data/out/mamager/'.TIME.'.xlsx';
        files::mdir('public/data/out/mamager/');
        excel::putExcelContent($fname,$rs);
        if(file_exists($fname))
        {
            $message = array(
                'statusCode' => 200,
                "message" => "导出成功，转入下载页面，如果浏览器没有相应，请<a href=\"{$fname}\">点此下载</a>",
                "callbackType" => 'forward',
                "forwardUrl" => "{$fname}"
            );
        }
        else
        {
            $message = array(
                'statusCode' => 300,
                "message" => "用户导出失败"
            );
        }
        exit(json_encode($message));
    }

    public function members()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page >= 1?$page:1;
        $args = array();
        $args[] = array("AND","userverify = 1");
        if($this->search['userid'])$args[] = array('AND',"userid = :userid",'userid',$this->search['userid']);
        if($this->search['userrealname'])$args[] = array('AND',"userrealname LIKE :userrealname",'userrealname','%'.$this->search['userrealname'].'%');
        if($this->search['useremail'])$args[] = array('AND',"useremail  LIKE :useremail",'useremail','%'.$this->search['useremail'].'%');
        if($this->search['userphone'])$args[] = array('AND',"userphone  LIKE :userphone",'userphone','%'.$this->search['userphone'].'%');
        if($this->search['userpassport'])$args[] = array('AND',"userpassport = :userpassport",'userpassport',$this->search['userpassport']);
        if($this->search['stime']){
            $stime = strtotime($this->search['stime']);
            $args[] = array('AND',"userregtime >= :suserregtime",'suserregtime',$stime);
        }
        if($this->search['etime']){
            $etime = strtotime($this->search['etime']);
            $args[] = array('AND',"userregtime <= :euserregtime",'euserregtime',$etime);
        }
        $users = \PHPEMSPRO\user\model\users::getUsersList($args,$page);
        $groups = \PHPEMSPRO\user\model\users::getGroups();
        \PHPEMSPRO\tpl::getInstance()->assign('users',$users);
        \PHPEMSPRO\tpl::getInstance()->assign('groups',$groups);
        \PHPEMSPRO\tpl::getInstance()->display('verify_members');
    }

    public function index()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page >= 1?$page:1;
        $args = array();
        $args[] = array("AND","userverify <> 1");
        if($this->search['userid'])$args[] = array('AND',"userid = :userid",'userid',$this->search['userid']);
        if($this->search['username'])$args[] = array('AND',"username LIKE :username",'username','%'.$this->search['username'].'%');
        if($this->search['useremail'])$args[] = array('AND',"useremail  LIKE :useremail",'useremail','%'.$this->search['useremail'].'%');
        if($this->search['userphone'])$args[] = array('AND',"userphone  LIKE :userphone",'userphone','%'.$this->search['userphone'].'%');
        if($this->search['groupcode'])$args[] = array('AND',"usergroupcode = :usergroupcode",'usergroupcode',$this->search['groupcode']);
        if($this->search['stime']){
            $stime = strtotime($this->search['stime']);
            $args[] = array('AND',"userregtime >= :suserregtime",'suserregtime',$stime);
        }
        if($this->search['etime']){
            $etime = strtotime($this->search['etime']);
            $args[] = array('AND',"userregtime <= :euserregtime",'euserregtime',$etime);
        }
        $users = \PHPEMSPRO\user\model\users::getUsersList($args,$page);
        $groups = \PHPEMSPRO\user\model\users::getGroups();
        \PHPEMSPRO\tpl::getInstance()->assign('users',$users);
        \PHPEMSPRO\tpl::getInstance()->assign('groups',$groups);
        \PHPEMSPRO\tpl::getInstance()->display('verify');
    }
}