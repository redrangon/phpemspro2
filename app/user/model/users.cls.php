<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\user\model;

class users
{
    static function autoLoginWxUser($openid)
    {
        $user = self::getUserByOpenid($openid);
        if(!$user)return false;
        $sessionuser = array(
            'sessionuserid' => $user['userid'],
            'sessionpassword' => $user['userpassword'],
            'sessionip' => \PHPEMSPRO\route::getClientIp(),
            'sessiongroupcode' => $user['usergroupcode'],
            'sessionlogintime' => TIME,
            'sessionusername' => $user['username']
        );
        \PHPEMSPRO\session::getInstance()->setSessionUser($sessionuser);
        return true;
    }

    static function getUserById($userid)
    {
        $data = array(
            'table' => 'users',
            'query' => array(
                array("AND","userid = :userid","userid",$userid)
            )
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElement($data);
    }

    static function getUserByUsername($username)
    {
        $data = array(
            'table' => 'users',
            'query' => array(
                array("AND","username = :username","username",$username)
            )
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElement($data);
    }

    static function getUserByEmail($email)
    {
        $data = array(
            'table' => 'users',
            'query' => array(
                array("AND","useremail = :useremail","useremail",$email)
            )
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElement($data);
    }

    static function getUserByOpenid($openid)
    {
        $data = array(
            'table' => 'users',
            'query' => array(
                array("AND","useropenid = :useropenid","useropenid",$openid)
            )
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElement($data);
    }

    static function getUserByPhone($phone)
    {
        $data = array(
            'table' => 'users',
            'query' => array(
                array("AND","userphone = :userphone","userphone",$phone)
            )
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElement($data);
    }

    static function getUsersList($args,$page,$number = \PHPEMSPRO\config::webpagenumber,$orderby = 'userid desc')
    {
        $data = array(
            'table' => 'users',
            'query' => $args,
            'orderby' => $orderby
        );
        return \PHPEMSPRO\pepdo::getInstance()->listElements($page,$number,$data);
    }

    static function getUserByArgs($args)
    {
        $data = array(
            'table' => 'users',
            'query' => $args,
            'limit' => false
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElements($data);
    }

    static function getUserNumberByArgs($args)
    {
        $data = array(
            'count(*) as number',
            'users',
            'query' => $args
        );
        $sql = \PHPEMSPRO\pepdo::getInstance()->makeSelect($data);
        $r = \PHPEMSPRO\pepdo::getInstance()->fetch($sql);
        return intval($r['number']);
    }

    static function getGroupById($groupid)
    {
        $data = array(
            'table' => 'groups',
            'query' => array(
                array("AND","groupid = :groupid","groupid",$groupid)
            )
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElement($data);
    }

    static function getGroupByCode($groupcode)
    {
        $data = array(
            'table' => 'groups',
            'query' => array(
                array("AND","groupcode = :groupcode","groupcode",$groupcode)
            )
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElement($data);
    }

    static function getGroupsByModelCode($modelcode)
    {
        $data = array(
            'table' => 'groups',
            'query' => array(
                array("AND","groupmodel = :groupmodel","groupmodel",$modelcode)
            )
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElements($data);
    }

    static function getGroups()
    {
        $data = array(
            'table' => 'groups',
            'index' => 'groupcode'
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElements($data);
    }

    static public function getDefaultGroup()
    {
        $data = array(
            'table' => 'groups',
            'query' => array(
                array("AND","groupdefault = 1")
            )
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElement($data);
    }

    static public function setDefaultGroup($groupid)
    {
        $data = array(
            'table' => 'groups',
            'value' => array('groupdefault' => 0),
            'query' => array()
        );
        \PHPEMSPRO\pepdo::getInstance()->updateElement($data);
        $data = array(
            'table' => 'groups',
            'value' => array('groupdefault' => 1),
            'query' => array(
                array("AND","groupid = :groupid","groupid",$groupid)
            )
        );
        return \PHPEMSPRO\pepdo::getInstance()->updateElement($data);
    }

    static function addUser($args)
    {
        $args['userregtime'] = TIME;
        $data = array(
            'table' => 'users',
            'query' => $args
        );
        return \PHPEMSPRO\pepdo::getInstance()->insertElement($data);
    }

    static function addGroup($args)
    {
        $data = array(
            'table' => 'groups',
            'query' => $args
        );
        return \PHPEMSPRO\pepdo::getInstance()->insertElement($data);
    }

    static function modifyUser($userid,$args)
    {
        $data = array(
            'table' => 'users',
            'value' => $args,
            'query' => array(
                array("AND","userid = :userid","userid",$userid)
            )
        );
        \PHPEMSPRO\pepdo::getInstance()->updateElement($data);
    }

    static function modifyGroup($groupid,$args)
    {
        $data = array(
            'table' => 'groups',
            'value' => $args,
            'query' => array(
                array("AND","groupid = :groupid","groupid",$groupid)
            )
        );
        \PHPEMSPRO\pepdo::getInstance()->updateElement($data);
    }

    static function delUser($userid)
    {
        $data = array(
            'table' => 'users',
            'query' => array(
                array("AND","userid = :userid","userid",$userid)
            )
        );
        \PHPEMSPRO\pepdo::getInstance()->delElement($data);
    }

    static function delGroup($groupid)
    {
        $data = array(
            'table' => 'groups',
            'query' => array(
                array("AND","groupid = :groupid","groupid",$groupid)
            )
        );
        \PHPEMSPRO\pepdo::getInstance()->delElement($data);
    }
}