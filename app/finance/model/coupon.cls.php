<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/11/20
 * Time: 23:10
 */

namespace PHPEMSPRO\finance\model;


class coupon
{

    static function getCouponBySn($sn)
    {
        $args = array(array("AND","cpnsn = :cpnsn","cpnsn",$sn));
        $data = array(
            'select' => false,
            'table' => 'coupons',
            'query' => $args
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElement($data);
    }

    static function getCouponById($id)
    {
        $args = array(array("AND","cpnid = :cpnid","cpnid",$id));
        $data = array(
            'select' => false,
            'table' => 'coupons',
            'query' => $args
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElement($data);
    }

    static function getCouponsByArgs($args)
    {
        $data = array(
            'select' => false,
            'table' => 'coupons',
            'query' => $args,
            'limit' => false
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElements($data);
    }

    static function modifyCoupon($id,$args)
    {
        $data = array(
            'table' => 'coupons',
            'value' => $args,
            'query' => array(
                array("AND","cpnid = :cpnid","cpnid",$id)
            )
        );
        \PHPEMSPRO\pepdo::getInstance()->updateElement($data);
    }

    static function delCoupon($id)
    {
        $data = array(
            'table' => 'coupons',
            'query' => array(
                array("AND","cpnid = :cpnid","cpnid",$id)
            )
        );
        \PHPEMSPRO\pepdo::getInstance()->delElement($data);
    }

    public function clearCancelCoupons($subjectid,$basicid)
    {
        $data = array(
            'table' => 'coupons',
            'query' => array(
                array("AND","cpncanceltime <= :cpncanceltime","cpncanceltime",TIME),
                array("AND","cpnsubjectid = :cpnsubjectid","cpnsubjectid",$subjectid),
                array("AND","cpnbasicid = :cpnbasicid","cpnbasicid",$basicid),
                array("AND","cpntype = :cpntype","cpntype",'exam')
            )
        );
        \PHPEMSPRO\pepdo::getInstance()->delElement($data);
    }

    public function clearCancellCoupons($lessonid)
    {
        $data = array(
            'table' => 'coupons',
            'query' => array(
                array("AND","cpncanceltime <= :cpncanceltime","cpncanceltime",TIME),
                array("AND","cpnlessonid = :cpnlessonid","cpnlessonid",$lessonid),
                array("AND","cpntype = :cpntype","cpntype",'lesson')
            )
        );
        \PHPEMSPRO\pepdo::getInstance()->delElement($data);
    }

    static function delCouponBySn($sn)
    {
        $data = array(
            'table' => 'coupons',
            'query' => array(
                array("AND","cpnsn = :cpnsn","cpnsn",$sn)
            )
        );
        \PHPEMSPRO\pepdo::getInstance()->delElement($data);
    }

    static function addCoupon($args)
    {
        $data = array('table' => 'coupons','query' => $args);
        return \PHPEMSPRO\pepdo::getInstance()->insertElement($data);
    }

    static function batAddCoupon($number,$subjectid,$basicid,$days,$canceltime)
    {
        for($i = 0;$i < $number;$i++)
        {
            $sn = strtoupper(md5(uniqid().rand(1000,9999)));
            $args = array(
                'cpnsn' => $sn,
                'cpnsubjectid' => $subjectid,
                'cpnbasicid' => $basicid,
                'cpndays' => $days,
                'cpntime' => TIME,
                'cpntype' => 'exam',
                'cpncanceltime' => $canceltime
            );
            self::addCoupon($args);
        }
        return true;
    }

    static function batAddlCoupon($number,$lessonid,$days,$canceltime)
    {
        for($i = 0;$i < $number;$i++)
        {
            $sn = strtoupper(md5(uniqid().rand(1000,9999)));
            $args = array(
                'cpnsn' => $sn,
                'cpnlessonid' => $lessonid,
                'cpndays' => $days,
                'cpntime' => TIME,
                'cpntype' => 'lesson',
                'cpncanceltime' => $canceltime
            );
            self::addCoupon($args);
        }
        return true;
    }

    static function getCouponList($args = array(),$page,$number = \PHPEMSPRO\config::webpagenumber)
    {
        $data = array(
            'table' => 'coupons',
            'query' => $args,
            'orderby' => 'cpnid desc'
        );
        return \PHPEMSPRO\pepdo::getInstance()->listElements($page,$number,$data);
    }
}