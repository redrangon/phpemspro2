<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/11/20
 * Time: 23:10
 */

namespace PHPEMSPRO\finance\model;


use PHPEMSPRO\pepdo;
use PhpOffice\PhpWord\Exception\Exception;

class orders
{
    static function getAgentUsersList($args = array(),$page = 1,$number = 20)
    {
        $args[] = array("AND","username = orderusername");
        $data = array(
            'select' => 'users.*,count(ordersn) as number,sum(orderprice) as price',
            'table' => array('orders','users'),
            'query' => $args,
            'groupby' => 'orderusername'
        );
        return \PHPEMSPRO\pepdo::getInstance()->listElements($page,$number,$data);
    }
    static function getOrderBySn($sn)
    {
        $args = array(array("AND","ordersn = :ordersn","ordersn",$sn));
        $data = array(
            'select' => false,
            'table' => 'orders',
            'query' => $args
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElement($data);
    }

    static function modifyOrder($sn,$args)
    {
        $data = array(
            'table' => 'orders',
            'value' => $args,
            'query' => array(
                array("AND","ordersn = :ordersn","ordersn",$sn)
            )
        );
        \PHPEMSPRO\pepdo::getInstance()->updateElement($data);
    }

    static function delOrder($sn)
    {
        $data = array(
            'table' => 'orders',
            'query' => array(
                array("AND","ordersn = :ordersn","ordersn",$sn)
            )
        );
        \PHPEMSPRO\pepdo::getInstance()->delElement($data);
    }

    static function addOrder($args)
    {
        $data = array('table' => 'orders','query' => $args);
        return \PHPEMSPRO\pepdo::getInstance()->insertElement($data);
    }

    static function getOrderList($args = array(),$page,$number = \PHPEMSPRO\config::webpagenumber)
    {
        $data = array(
            'table' => 'orders',
            'query' => $args,
            'orderby' => 'ordersn desc'
        );
        return \PHPEMSPRO\pepdo::getInstance()->listElements($page,$number,$data);
    }

    static function getRegOrderList($args = array(),$page,$number = \PHPEMSPRO\config::webpagenumber)
    {
        $data = array(
            'table' => array('orders','users'),
            'query' => $args,
            'orderby' => 'ordersn desc'
        );
        return \PHPEMSPRO\pepdo::getInstance()->listElements($page,$number,$data);
    }

    static function getOrdersTotalMoney($args)
    {
        $data = array(
            'select' => 'sum(orderprice) as money',
            'table' => 'orders',
            'query' => $args
        );
        $r = \PHPEMSPRO\pepdo::getInstance()->getElement($data);
        return $r['money'];
    }

    static function payforOrder($ordersn,$paytype = 'offline')
    {
        $order = self::getOrderBySn($ordersn);
        pepdo::getInstance()->beginTransaction();
        try{
            if($order['ordertype'] == 'exam')
            {
                foreach($order['orderitems'] as $item)
                {
                    $subjectid = $item['subjectid'];
                    $subject = \PHPEMSPRO\exam\model\points::getSubjectById($subjectid);
                    $basicid = $item['basicid'];
                    $r = \PHPEMSPRO\exam\model\exams::getIsMember($subject['subjectdb'],$order['orderusername'],$basicid);
                    if(!$r)
                    {
                        $args = array(
                            'obbasicid' => $basicid,
                            'obusername' => $order['orderusername'],
                            'obtime' => TIME,
                            'obendtime' => TIME + 24*3600*$item['time']
                        );
                        \PHPEMSPRO\exam\model\exams::addBasicMember($subject['subjectdb'],$args);
                    }
                    elseif(strtotime($r['obendtime']) < TIME)
                    {
                        $args = array(
                            'obendtime' => TIME + 24*3600*$item['time']
                        );
                        \PHPEMSPRO\exam\model\exams::modifyBasicMember($subject['subjectdb'],$r['obid'],$args);
                    }
                    elseif(strtotime($r['obendtime']) >= TIME)
                    {
                        $args = array(
                            'obendtime' => strtotime($r['obendtime']) + 24*3600*$item['time']
                        );
                        \PHPEMSPRO\exam\model\exams::modifyBasicMember($subject['subjectdb'],$r['obid'],$args);
                    }
                }
            }
            elseif($order['ordertype'] == 'lesson')
            {
                foreach($order['orderitems'] as $item)
                {
                    $lessonid = $item['lessonid'];
                    $time = $item['time'];
                    \PHPEMSPRO\lesson\model\lessons::openLesson($order['orderusername'], $lessonid, $time);
                }
            }
            elseif($order['ordertype'] == 'recharge')
            {
                $user = \PHPEMSPRO\user\model\users::getUserByUsername($order['orderusername']);
                $coin = $user['usercoin'] + $order['orderprice'];
                \PHPEMSPRO\user\model\users::modifyUser($user['userid'],array('usercoin' => $coin));
            }
            elseif($order['ordertype'] == 'sale')
            {
                foreach($order['orderitems'] as $item)
                {
                    if($item['type'] == 'exam')
                    {
                        $basicid = $item['basicid'];
                        $r = \PHPEMSPRO\exam\model\exams::getIsMember($item['db'],$order['orderusername'],$basicid);
                        if(!$r)
                        {
                            $args = array(
                                'obbasicid' => $basicid,
                                'obusername' => $order['orderusername'],
                                'obtime' => TIME,
                                'obendtime' => TIME + 24*3600*$item['time']
                            );
                            \PHPEMSPRO\exam\model\exams::addBasicMember($item['db'],$args);
                        }
                        elseif(strtotime($r['obendtime']) < TIME)
                        {
                            $args = array(
                                'obendtime' => TIME + 24*3600*$item['time']
                            );
                            \PHPEMSPRO\exam\model\exams::modifyBasicMember($item['db'],$r['obid'],$args);
                        }
                        elseif(strtotime($r['obendtime']) >= TIME)
                        {
                            $args = array(
                                'obendtime' => strtotime($r['obendtime']) + 24*3600*$item['time']
                            );
                            \PHPEMSPRO\exam\model\exams::modifyBasicMember($item['db'],$r['obid'],$args);
                        }
                    }
                    elseif($item['type'] == 'lesson')
                    {
                        $lessonid = $item['lessonid'];
                        $time = $item['time'];
                        \PHPEMSPRO\lesson\model\lessons::openLesson($order['orderusername'], $lessonid, $time);
                    }
                }
            }
            self::modifyOrder($ordersn,array('orderstatus' => 2,'orderpaytype' => $paytype));
            pepdo::getInstance()->commit();
            return true;
        } catch (Exception $e){
            pepdo::getInstance()->rollback();
            return false;
        }
    }
}