<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\user\model;

class msg
{

    static function getMsgById($msgid)
    {
        $data = array(
            'table' => 'msg',
            'query' => array(
                array("AND","msgid = :msgid","msgid",$msgid)
            )
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElement($data);
    }

    static function getUserMsgById($msrid)
    {
        $data = array(
            'table' => array('msg','msg_user'),
            'query' => array(
                array("AND","msrid = :msrid","msrid",$msrid),
                array("AND","msrmsgid = msgid")
            )
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElement($data);
    }

    static function getNewUserMsgNumber($username)
    {
        $data = array(
            'select' => 'count(*) as number',
            'table' => 'msg_user',
            'query' => array(
                array("AND","msrusername = :msrusername","msrusername",$username),
                array("AND","msrstatus = 0")
            )
        );
        $r = \PHPEMSPRO\pepdo::getInstance()->getElement($data);
        return intval($r['number']);
    }

    static function getUserMsgList($args,$page,$number = 20,$orderby = "msrid desc")
    {
        $args[] = array("AND","msrmsgid = msgid");
        $data = array(
            'table' => array('msg','msg_user'),
            'query' => $args,
            'orderby' => $orderby
        );
        return \PHPEMSPRO\pepdo::getInstance()->listElements($page,$number,$data);
    }

    static function getMsgList($args,$page,$number = 20,$orderby = "msgid desc")
    {
        $data = array(
            'table' => 'msg',
            'query' => $args,
            'orderby' => $orderby
        );
        return \PHPEMSPRO\pepdo::getInstance()->listElements($page,$number,$data);
    }

    static function addMsg($args)
    {
        $args['msgtime'] = TIME;
        $data = array(
            'table' => 'msg',
            'query' => $args
        );
        return \PHPEMSPRO\pepdo::getInstance()->insertElement($data);
    }

    static function addMsgUser($args)
    {
        $data = array(
            'table' => 'msg_user',
            'query' => $args
        );
        return \PHPEMSPRO\pepdo::getInstance()->insertElement($data);
    }

    static function modifyMsgUser($msrid,$args)
    {
        $data = array(
            'table' => 'msg_user',
            'value' => $args,
            'query' => array(
                array("AND","msrid = :msrid","msrid",$msrid)
            )
        );
        \PHPEMSPRO\pepdo::getInstance()->updateElement($data);
    }

    static function delMsg($msgid)
    {
        $data = array(
            'table' => 'msg',
            'query' => array(
                array("AND","msgid = :msgid","msgid",$msgid)
            )
        );
        \PHPEMSPRO\pepdo::getInstance()->delElement($data);
        $data = array(
            'table' => 'msg_user',
            'query' => array(
                array("AND","msrmsgid = :msrmsgid","msrmsgid",$msgid)
            )
        );
        \PHPEMSPRO\pepdo::getInstance()->delElement($data);
        return true;
    }

    static function delMsgUser($msrid)
    {
        $data = array(
            'table' => 'msg_user',
            'query' => array(
                array("AND","msrid = :msrid","msrid",$msrid)
            )
        );
        \PHPEMSPRO\pepdo::getInstance()->delElement($data);
    }
}