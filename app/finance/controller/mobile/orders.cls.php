<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\finance\controller\mobile;

use PHPEMSPRO\finance\mobile;

class orders extends mobile
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

    public function detail()
    {
        $ordersn = \PHPEMSPRO\route::get('ordersn');
        $order = \PHPEMSPRO\finance\model\orders::getOrderBySn($ordersn);
        \PHPEMSPRO\tpl::getInstance()->assign('order',$order);
        \PHPEMSPRO\tpl::getInstance()->display('orders_detail');
    }

    public function index()
    {
        $search = \PHPEMSPRO\route::get('search');
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 1?$page:1;
        $args = array();
        $args[] = array("AND","ordertype = 'exam'");
        $args[] = array("AND","orderstatus >= 2");
        $args[] = array("AND","orderagent = :orderagent","orderagent",$this->_user['username']);
        if($search['stime'])
        {
            $args[] = array("AND","ordertime >= :stime","stime",strtotime($search['stime']));
        }
        if($search['etime'])
        {
            $args[] = array("AND","ordertime <= :etime","etime",strtotime($search['etime']));
        }
        if($search['userphone'])
        {
            $args[] = array("AND","orderusername = :orderusername","orderusername",$search['userphone']);
        }
        if($search['useremail'])
        {
            $args[] = array("AND","orderemail LIKE :orderemail","orderemail",$search['useremail'].'%');
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
        \PHPEMSPRO\tpl::getInstance()->display('orders');
    }
}