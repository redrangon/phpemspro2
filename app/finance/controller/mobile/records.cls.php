<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\finance\controller\mobile;

use PHPEMSPRO\finance\mobile;

class records extends mobile
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
        }
    }

    public function recharge()
    {
        if(\PHPEMSPRO\route::get('recharge'))
        {
            $money = \PHPEMSPRO\route::get('money');
            if($money < 1)
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "最低充值1元"
                );
                \PHPEMSPRO\route::urlJump($message);
            }
            $money = round($money,2);
            $args = array();
            $args['orderprice'] = $money;
            $args['ordername'] = '代理充值'.$money.'元';
            $args['orderitems'] = array();
            $args['ordersn'] = date('YmdHi').rand(100,999);
            $args['orderstatus'] = 1;
            $args['orderusername'] = $this->_user['username'];
            $args['ordertime'] = TIME;
            $args['ordertype'] = 'recharge';
            \PHPEMSPRO\finance\model\orders::addOrder($args);
            $payforurl = \PHPEMSPRO\alipay::getInstance()->createPagePayLink($args);
            $message = array(
                'statusCode' => 201,
                "message" => "下单成功！",
                "callbackType" => "forward",
                "forwardUrl" => $payforurl
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        else
            \PHPEMSPRO\tpl::getInstance()->display('recharge');
    }

    public function index()
    {
        $search = \PHPEMSPRO\route::get('search');
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 1?$page:1;
        $args = array();
        $args[] = array("AND","find_in_set(ordertype,'recharge,offline')");
        $args[] = array("AND","orderstatus = 2");
        $args[] = array("AND","orderusername = :orderusername","orderusername",$this->_user['username']);
        if($search['stime'])
        {
            $args[] = array("AND","ordertime >= :stime","stime",strtotime($search['stime']));
        }
        if($search['etime'])
        {
            $args[] = array("AND","ordertime <= :etime","etime",strtotime($search['etime']));
        }
        if($search['orderstatus'])
        {
            $args[] = array("AND","orderstatus = :orderstatus","orderstatus",$search['orderstatus']);
        }
        $orders = \PHPEMSPRO\finance\model\orders::getOrderList($args,$page);
        $money = \PHPEMSPRO\finance\model\orders::getOrdersTotalMoney($args);
        \PHPEMSPRO\tpl::getInstance()->assign('money',$money);
        \PHPEMSPRO\tpl::getInstance()->assign('orders',$orders);
        \PHPEMSPRO\tpl::getInstance()->assign('page',$page);
        \PHPEMSPRO\tpl::getInstance()->display('records');
    }
}