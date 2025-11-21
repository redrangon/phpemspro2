<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\exam\model;

class faq
{

    static function getFaqNumberByArgs($dbid,$args)
    {
        $data = array(
            'select' => 'count(*) as number',
            'table' => 'faq',
            'query' => $args
        );
        $r = \PHPEMSPRO\pepdo::getInstance($dbid)->getElement($data);
        return $r['number'];
    }

    static function getFaqById($dbid,$faqid)
    {
        $data = array(
            'table' => 'faq',
            'query' => array(
                array("AND","faqid = :faqid","faqid",$faqid)
            )
        );
        return \PHPEMSPRO\pepdo::getInstance($dbid)->getElement($data);
    }

    static function getLastFaqByUsername($dbid,$username)
    {
        $data = array(
            'table' => 'faq',
            'query' => array(
                array("AND","faqusername = :faqusername","faqusername",$username)
            ),
            'orderby' => 'faqid desc'
        );
        return \PHPEMSPRO\pepdo::getInstance($dbid)->getElement($data);
    }

    static function getFaqList($dbid,$args,$page,$number = \PHPEMSPRO\config::webpagenumber,$orderby = 'faqid desc')
    {
        $data = array(
            'table' => 'faq',
            'query' => $args,
            'orderby' => $orderby
        );
        return \PHPEMSPRO\pepdo::getInstance($dbid)->listElements($page,$number,$data);
    }

    static function saveFaq($dbid,$args)
    {
        $data = array(
            'table' => 'faq',
            'query' => $args
        );
        \PHPEMSPRO\pepdo::getInstance($dbid)->insertElement($data);
    }

    static function modifyFaq($dbid,$faqid,$args)
    {
        $data = array(
            'table' => 'faq',
            'value' => $args,
            'query' => array(array("AND","faqid = :faqid","faqid",$faqid))
        );
        \PHPEMSPRO\pepdo::getInstance($dbid)->updateElement($data);
    }

    static function delFaq($dbid,$faqid)
    {
        $data = array(
            'table' => 'faq',
            'query' => array(array("AND","faqid = :faqid","faqid",$faqid))
        );
        \PHPEMSPRO\pepdo::getInstance($dbid)->delElement($data);
    }

    static function delFaqByUsername($dbid,$username)
    {
        $data = array(
            'table' => 'faq',
            'query' => array(
                array("AND","faqusername = :faqusername","faqusername",$username)
            )
        );
        return \PHPEMSPRO\pepdo::getInstance($dbid)->delElement($data);
    }
}