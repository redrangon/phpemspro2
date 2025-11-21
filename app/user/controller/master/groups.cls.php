<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/11/25
 * Time: 21:24
 */

namespace PHPEMSPRO\user\controller\master;

use PHPEMSPRO\user\master;

class groups extends master
{
    protected function _start()
    {
        $search = \PHPEMSPRO\route::get('search');
        if($search)
        {
            $this->u = '';
            $this->search = $search;
            $this->tpl->assign('search',$search);
            foreach($search as $key => $arg)
            {
                $this->u .= "&search[{$key}]={$arg}";
            }
            unset($search);
        }
    }

    public function add()
    {
        if(\PHPEMSPRO\route::get('addgroup'))
        {
            $args = \PHPEMSPRO\route::get('args');
            $group = \PHPEMSPRO\user\model\users::getGroupByCode($args['groupcode']);
            if($group)
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "操作成功"
                );
                exit(json_encode($message));
            }
            $id = \PHPEMSPRO\user\model\users::addGroup($args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "index.php?user-master-groups"
            );
            exit(json_encode($message));
        }
        else
        {
            $models = \PHPEMSPRO\database\model\model::getModelsByApp(\PHPEMSPRO\route::getUrl('app'));
            \PHPEMSPRO\tpl::getInstance()->assign('models',$models);
            \PHPEMSPRO\tpl::getInstance()->display('groups_add');
        }
    }

    public function modify()
    {
        $groupid = \PHPEMSPRO\route::get('groupid');
        $group = \PHPEMSPRO\user\model\users::getGroupById($groupid);
        if(\PHPEMSPRO\route::get('modifygroup'))
        {
            $args = \PHPEMSPRO\route::get('args');
            \PHPEMSPRO\user\model\users::modifyGroup($groupid,$args);
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
            $models = \PHPEMSPRO\database\model\model::getModelsByApp(\PHPEMSPRO\route::getUrl('app'));
            \PHPEMSPRO\tpl::getInstance()->assign('group',$group);
            \PHPEMSPRO\tpl::getInstance()->assign('models',$models);
            \PHPEMSPRO\tpl::getInstance()->display('groups_modify');
        }
    }

    public function setdefault()
    {
        $groupid = \PHPEMSPRO\route::get('groupid');
        \PHPEMSPRO\user\model\users::setDefaultGroup($groupid);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function del()
    {
        $groupid = \PHPEMSPRO\route::get('groupid');
        $group = \PHPEMSPRO\user\model\users::getGroupById($groupid);
        if($group)
        {
            $args = array(
                array("AND","usergroupcode = :usergroupcode","usergroupcode",$group['groupcode'])
            );
            if(\PHPEMSPRO\user\model\users::getUserNumberByArgs($groupid))
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "删除失败，请先删除本用户组所有用户"
                );
                exit(json_encode($message));
            }
            \PHPEMSPRO\user\model\users::delGroup($groupid);
        }
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function index()
    {
        $groups = \PHPEMSPRO\user\model\users::getGroups();
        \PHPEMSPRO\tpl::getInstance()->assign('groups',$groups);
        \PHPEMSPRO\tpl::getInstance()->display('groups');
    }
}