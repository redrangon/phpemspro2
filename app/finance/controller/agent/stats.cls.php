<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\finance\controller\agent;

use PHPEMSPRO\finance\agent;

class stats extends agent
{
    protected function _start()
    {
        //
    }

    public function index()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 1?$page:1;
        $search = \PHPEMSPRO\route::get('search');
        $args = array();
        $args[] = array("AND","users.username = orders.orderusername");
        $args[] = array("AND","users.useragent = :useragent","useragent",$this->_user['username']);
        $args[] = array("AND","(orders.ordertype = 'recharge' || orders.ordertype = 'offline')");
        $args[] = array("AND","orders.orderstatus = 2");
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
        $orders = \PHPEMSPRO\finance\model\orders::getRegOrderList($args,$page);
        $money = \PHPEMSPRO\finance\model\orders::getRegOrdersMoneyByArgs($args);
        \PHPEMSPRO\tpl::getInstance()->assign('orders',$orders);
        \PHPEMSPRO\tpl::getInstance()->assign('money',$money['allprice']);
        \PHPEMSPRO\tpl::getInstance()->display('stats');
    }
}