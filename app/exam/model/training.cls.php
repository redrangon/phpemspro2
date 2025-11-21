<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\exam\model;

class training
{
    static function getTrainingById($id)
    {
        $data = array(
            'table' => 'training',
            'query' => array(
                array("AND","trid = :trid","trid",$id)
            )
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElement($data);
    }

    static function getTrainingsByArgs($args = array())
    {
        $data = array(
            'table' => 'training',
            'query' => $args,
            'orderby' => 'trid desc',
            'index' => 'trid',
            'limit' => false
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElements($data);
    }

    static function getTrainingList($args = array(),$page,$number = \PHPEMSPRO\config::webpagenumber)
    {
        $data = array(
            'table' => 'training',
            'query' => $args,
            'orderby' => 'trid desc'
        );
        return \PHPEMSPRO\pepdo::getInstance()->listElements($page,$number,$data);
    }

    static function modifyTrainingById($id,$args)
    {
        $data = array(
            'table' => 'training',
            'value' => $args,
            'query' => array(
                array("AND","trid = :trid","trid",$id)
            )
        );
        return \PHPEMSPRO\pepdo::getInstance()->updateElement($data);
    }

    static function addTraining($args)
    {
        $data = array(
            'table' => 'training',
            'query' => $args
        );
        return \PHPEMSPRO\pepdo::getInstance()->insertElement($data);
    }

    static function delTraining($id)
    {
        $data = array(
            'table' => 'training',
            'query' => array(
                array("AND","trid = :trid","trid",$id)
            )
        );
        return \PHPEMSPRO\pepdo::getInstance()->delElement($data);
    }

    static function getSearchById($id)
    {
        $data = array(
            'table' => 'search',
            'query' => array(
                array("AND","sid = :sid","sid",$id)
            )
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElement($data);
    }

    static function getSearchList($args = array(), $page = 1,$number = \PHPEMSPRO\config::webpagenumber)
    {
        $data = array(
            'table' => 'search',
            'query' => $args,
            'orderby' => 'sid desc',
            'index' => 'sid'
        );
        return \PHPEMSPRO\pepdo::getInstance()->listElements($page,$number,$data);
    }
}