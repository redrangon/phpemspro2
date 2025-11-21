<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\exam\model;

class examsession
{
    static function getExamsessionById($dbid,$id)
    {
        $data = array(
            'table' => 'examsession',
            'query' => array(
                array("AND","esid = :esid","esid",$id)
            )
        );
        return \PHPEMSPRO\pepdo::getInstance($dbid)->getElement($data);
    }

    static function getExamsessionByArgs($dbid,$args = array())
    {
        $data = array(
            'table' => 'examsession',
            'query' => $args,
            'orderby' => 'esid desc'
        );
        return \PHPEMSPRO\pepdo::getInstance($dbid)->getElement($data);
    }

    static function getExamsessionsByArgs($dbid,$args = array())
    {
        $data = array(
            'table' => 'examsession',
            'query' => $args,
            'index' => 'espaperid',
            'limit' => false
        );
        return \PHPEMSPRO\pepdo::getInstance($dbid)->getElements($data);
    }

    static function getExamsessionsList($dbid,$args,$page,$number = \PHPEMSPRO\config::webpagenumber,$orderby = 'esid desc')
    {
        $data = array(
            'table' => 'examsession',
            'query' => $args,
            'orderby' => $orderby
        );
        return \PHPEMSPRO\pepdo::getInstance($dbid)->listElements($page,$number,$data);
    }

    static function modifyExamsessionById($dbid,$id,$args)
    {
        $data = array(
            'table' => 'examsession',
            'value' => $args,
            'query' => array(
                array("AND","esid = :esid","esid",$id)
            )
        );
        return \PHPEMSPRO\pepdo::getInstance($dbid)->updateElement($data);
    }

    static function addExamsession($dbid,$args)
    {
        $data = array(
            'table' => 'examsession',
            'query' => $args
        );
        return \PHPEMSPRO\pepdo::getInstance($dbid)->insertElement($data);
    }

    static function delExamsession($dbid,$id)
    {
        $data = array(
            'table' => 'examsession',
            'query' => array(
                array("AND","esid = :esid","esid",$id)
            )
        );
        return \PHPEMSPRO\pepdo::getInstance($dbid)->delElement($data);
    }

    static function delExamsessions($dbid,$args)
    {
        $data = array(
            'table' => 'examsession',
            'query' => $args
        );
        return \PHPEMSPRO\pepdo::getInstance($dbid)->delElement($data);
    }
}