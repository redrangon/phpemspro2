<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\sale\controller\app;

use PHPEMSPRO\exam\model\exams;
use PHPEMSPRO\finance\model\orders;
use PHPEMSPRO\lesson\model\lessons;
use PHPEMSPRO\route;
use PHPEMSPRO\sale\app;
use PHPEMSPRO\sale\model\elem;
use PHPEMSPRO\sale\model\sale;
use PHPEMSPRO\tpl;
use PHPEMSPRO\user\model\users;

class sales extends app
{

    public function order()
    {
        $sgdid = route::get('sgdid');
        $goods = sale::getSaleGoodsById($sgdid);
        if(!$goods)
        {
            $message = array(
                'statusCode' => 300,
                "message" => "此活动不存在！"
            );
            route::urlJump($message);
        }
        $sale = sale::getSaleById($goods['sgdsaleid']);
        if(strtotime($sale['salestime']) > TIME || strtotime($sale['saleetime']) < TIME)
        {
            $message = array(
                'statusCode' => 300,
                "message" => "此活动未开始或已过期！"
            );
            route::urlJump($message);
        }
        $items = sale::getSaleItemsByArgs(array(array(
            "AND","simgoodsid = :simgoodsid","simgoodsid",$goods['sgdid']
        )));
        $args = array();
        $args['orderprice'] = $goods['sgdprice'];
        $args['ordername'] = '【 营销活动 】'.$goods['sgdtitle'];
        $args['orderitems'] = array();
        foreach($items as $item)
        {
            if($item['simtype'] == 'exam')
            {
                $basic = exams::getBasicById($item['simdb'],$item['simitemid']);
                $args['orderitems'][] = array(
                    'db' => $item['simdb'],
                    'basicid' => $basic['basicid'],
                    'basicname' => $basic['basic'],
                    'time' => $item['simday'],
                    'price' => $item['simprice'],
                    'type' => 'exam'
                );
            }
            elseif($item['simtype'] == 'lesson')
            {
                $lesson = lessons::getLessonById($item['simitemid']);
                $args['orderitems'][] = array(
                    'lessonid' => $lesson['lessonid'],
                    'lessonname' => $lesson['lessonname'],
                    'time' => $item['simday'],
                    'price' => $item['simprice'],
                    'type' => 'lesson'
                );
            }
        }
        $args['ordersn'] = date('YmdHi').rand(100,999);
        $args['orderstatus'] = 1;
        $args['orderusername'] = $this->_user['username'];
        $args['ordertime'] = TIME;
        $args['ordertype'] = 'sale';
        $args['ordertips'] = array(
            'saleid' => $goods['sgtsaleid']
        );
        orders::addOrder($args);
        $message = array(
            'statusCode' => 200,
            "message" => "下单成功！",
            "callbackType" => "forward",
            "forwardUrl" => 'index.php?user-app-orders-detail&ordersn='.$args['ordersn']
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function goods()
    {
        $sgdid = route::get('sgdid');
        $goods = sale::getSaleGoodsById($sgdid);
        $sale = sale::getSaleById($goods['sgdsaleid']);
        $items = sale::getSaleItemsByArgs(array(array(
            "AND","simgoodsid = :simgoodsid","simgoodsid",$goods['sgdid']
        )));
        $titles = array();
        foreach($items as $item)
        {
            if($item['simtype'] == 'exam')
            {
                $basic = exams::getBasicById($item['simdb'],$item['simitemid']);
                $titles[$item['simid']] = $basic['basic'];
            }
            elseif($item['simtype'] == 'lesson')
            {
                $lesson = lessons::getLessonById($item['simitemid']);
                $titles[$item['simid']] = $lesson['lessonname'];
            }
        }
        $salegoods = sale::getSaleGoodsByArgs(array(array("AND","sgdsaleid = :sgdsaleid","sgdsaleid",$goods['sgdsaleid'])));
        tpl::getInstance()->assign('sale',$sale);
        tpl::getInstance()->assign('goods',$goods);
        tpl::getInstance()->assign('salegoods',$salegoods);
        tpl::getInstance()->assign('items',$items);
        tpl::getInstance()->assign('titles',$titles);
        tpl::getInstance()->assign('itemtypes',elem::$itemtypes);
        tpl::getInstance()->display('sales_goods');
    }

    public function index()
    {
        $saleid = route::get('saleid');
        $sale = sale::getSaleById($saleid);
        $goods = sale::getSaleGoodsByArgs(array(array("AND","sgdsaleid = :sgdsaleid","sgdsaleid",$saleid)));
        tpl::getInstance()->assign('sale',$sale);
        tpl::getInstance()->assign('goods',$goods);
        tpl::getInstance()->display('sale');
    }
}