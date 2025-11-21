<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\finance\controller\master;

use PHPEMSPRO\finance\master;
use PHPEMSPRO\route;

class orders extends master
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
        }
        $orderstatus = array(1=>'待付款',2=>'已完成',99=>'已撤单');
        \PHPEMSPRO\tpl::getInstance()->assign('orderstatus',$orderstatus);
        $paytypes = array('alipay'=>'支付宝','wxpay'=>'微信','self'=>'手动');
        \PHPEMSPRO\tpl::getInstance()->assign('paytypes',$paytypes);
    }

    public function batdel()
    {
        $delids = \PHPEMSPRO\route::get('delids');
        foreach($delids as $ordersn => $p)
        {
            $order = \PHPEMSPRO\finance\model\orders::getOrderBySn($ordersn);
            if($order['orderstatus'] != 2)
            {
                \PHPEMSPRO\finance\model\orders::delOrder($ordersn);
            }
        }
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => 'forward',
            "forwardUrl" => "reload"
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function del()
    {
        $ordersn = \PHPEMSPRO\route::get('ordersn');
        $order = \PHPEMSPRO\finance\model\orders::getOrderBySn($ordersn);
        if($order['orderstatus'] != 99)
        {
            $message = array(
                'statusCode' => 300,
                "message" => "只有作废的订单才能删除"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        \PHPEMSPRO\finance\model\orders::delOrder($ordersn);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => 'forward',
            "forwardUrl" => "reload"
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function offlinepay()
    {
        $ordersn = route::get('ordersn');
        if(\PHPEMSPRO\finance\model\orders::payforOrder($ordersn))
        {
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => 'forward',
                "forwardUrl" => "reload"
            );
        }
        else
        {
            $message = array(
                'statusCode' => 300,
                "message" => "操作失败"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        \PHPEMSPRO\route::urlJump($message);
    }

    public function index()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 1?$page:1;
        $args = array();
        if($this->search['ordersn'])$args[] = array("AND","ordersn = :ordersn","ordersn",$this->search['ordersn']);
        if($this->search['ordertitle'])$args[] = array("AND","ordername LIKE :ordertitle","ordertitle",'%'.$this->search['ordertitle'].'%');
        if($this->search['sprice'])$args[] = array("AND","orderprice >= :sorderprice","sorderprice",$this->search['sprice']);
        if($this->search['eprice'])$args[] = array("AND","orderprice <= :eorderprice","eorderprice",$this->search['eprice']);
        if($this->search['username'])$args[] = array("AND","orderusername = :orderusername","orderusername",$this->search['username']);
        if($this->search['stime'])$args[] = array("AND","ordertime >= :stime","stime",strtotime($this->search['stime']));
        if($this->search['etime'])$args[] = array("AND","ordertime <= :etime","etime",strtotime($this->search['etime']));
        if($this->search['paytype'])$args[] = array("AND","orderpaytype = :orderpaytype","orderpaytype",$this->search['paytype']);
        if($this->search['status'])$args[] = array("AND","orderstatus = :status","status",$this->search['status']);
        $orders = \PHPEMSPRO\finance\model\orders::getOrderList($args,$page);
        $money = \PHPEMSPRO\finance\model\orders::getOrdersTotalMoney($args);
        \PHPEMSPRO\tpl::getInstance()->assign('money',$money);
        \PHPEMSPRO\tpl::getInstance()->assign('orders',$orders);
        \PHPEMSPRO\tpl::getInstance()->assign('page',$page);
        \PHPEMSPRO\tpl::getInstance()->display('orders');
    }
}