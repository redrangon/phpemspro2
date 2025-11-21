<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\sale\controller\master;

use PHPEMSPRO\exam\model\exams;
use PHPEMSPRO\exam\model\points;
use PHPEMSPRO\lesson\model\lessons;
use PHPEMSPRO\sale\master;
use PHPEMSPRO\sale\model\sale;

class sales extends master
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

    }

    public function delitem()
    {
        $itemid = \PHPEMSPRO\route::get('itemid');
        sale::delSaleItems($itemid);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function modifyitem()
    {
        $itemid = \PHPEMSPRO\route::get('itemid');
        $item = sale::getSaleItemsById($itemid);
        if(\PHPEMSPRO\route::get('modifyitem'))
        {
            $args = \PHPEMSPRO\route::get('args');
            sale::modifySaleItems($itemid,$args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功！",
                "callbackType" => "forward",
                "forwardUrl" => "reload"
            );
            exit(json_encode($message));
        }
        else
        {
            $goods = sale::getSaleGoodsById($item['simgoodsid']);
            $sale = sale::getSaleById($goods['sgdsaleid']);
            \PHPEMSPRO\tpl::getInstance()->assign('sale',$sale);
            \PHPEMSPRO\tpl::getInstance()->assign('goods',$goods);
            \PHPEMSPRO\tpl::getInstance()->assign('databases',\PHPEMSPRO\config::db);
            \PHPEMSPRO\tpl::getInstance()->assign('item',$item);
            \PHPEMSPRO\tpl::getInstance()->display('sale_modifyitem');
        }
    }

    public function additem()
    {
        $goodsid = \PHPEMSPRO\route::get('goodsid');
        $goods = sale::getSaleGoodsById($goodsid);
        if(\PHPEMSPRO\route::get('additem'))
        {
            $args = \PHPEMSPRO\route::get('args');
            $args['simgoodsid'] = $goodsid;
            $args['simtime'] = TIME;
            sale::addSaleItems($args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功！",
                "callbackType" => "forward",
                "forwardUrl" => "index.php?sale-master-sales-items&goodsid=".$goodsid
            );
            exit(json_encode($message));
        }
        else
        {
            $sale = sale::getSaleById($goods['sgdsaleid']);
            \PHPEMSPRO\tpl::getInstance()->assign('sale',$sale);
            \PHPEMSPRO\tpl::getInstance()->assign('databases',\PHPEMSPRO\config::db);
            \PHPEMSPRO\tpl::getInstance()->assign('goods',$goods);
            \PHPEMSPRO\tpl::getInstance()->display('sale_additem');
        }
    }

    public function items()
    {
        $goodsid = \PHPEMSPRO\route::get('goodsid');
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 0?$page:1;
        $args = array();
        $args[] = array("AND","simgoodsid = :simgoodsid","simgoodsid",$goodsid);
        $items = sale::getSaleItemsList($args,$page,10);
        $titles = array();
        foreach($items['data'] as $item)
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
        $goods = sale::getSaleGoodsById($goodsid);
        $sale = sale::getSaleById($goods['sgdsaleid']);
        \PHPEMSPRO\tpl::getInstance()->assign('sale',$sale);
        \PHPEMSPRO\tpl::getInstance()->assign('goods',$goods);
        \PHPEMSPRO\tpl::getInstance()->assign('titles',$titles);
        \PHPEMSPRO\tpl::getInstance()->assign('items',$items);
        \PHPEMSPRO\tpl::getInstance()->display('sale_items');
    }

    public function delgoods()
    {
        $goodsid = \PHPEMSPRO\route::get('goodsid');
        sale::delSaleGoods($goodsid);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function modifygoods()
    {
        $goodsid = \PHPEMSPRO\route::get('goodsid');
        $goods = sale::getSaleGoodsById($goodsid);
        $sale = sale::getSaleById($goods['sgdsaleid']);
        if(\PHPEMSPRO\route::get('modifygoods'))
        {
            $args = \PHPEMSPRO\route::get('args');
            sale::modifySaleGoods($goodsid,$args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功！",
                "callbackType" => "forward",
                "forwardUrl" => "reload"
            );
            exit(json_encode($message));
        }
        else
        {
            \PHPEMSPRO\tpl::getInstance()->assign('sale',$sale);
            \PHPEMSPRO\tpl::getInstance()->assign('goods',$goods);
            \PHPEMSPRO\tpl::getInstance()->display('sale_modifygoods');
        }
    }

    public function addgoods()
    {
        $saleid = \PHPEMSPRO\route::get('saleid');
        $sale = sale::getSaleById($saleid);
        if(\PHPEMSPRO\route::get('addgoods'))
        {
            $args = \PHPEMSPRO\route::get('args');
            $args['sgdsaleid'] = $saleid;
            $args['sgdtime'] = TIME;
            sale::addSaleGoods($args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "index.php?sale-master-sales-goods&saleid=".$saleid
            );
            exit(json_encode($message));
        }
        else
        {
            \PHPEMSPRO\tpl::getInstance()->assign('sale',$sale);
            \PHPEMSPRO\tpl::getInstance()->display('sale_addgoods');
        }
    }

    public function goods()
    {
        $saleid = \PHPEMSPRO\route::get('saleid');
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 0?$page:1;
        $args = array();
        $args[] = array("AND","sgdsaleid = :sgdsaleid","sgdsaleid",$saleid);
        $goods = sale::getSaleGoodsList($args,$page,10);
        $sale = sale::getSaleById($saleid);
        \PHPEMSPRO\tpl::getInstance()->assign('sale',$sale);
        \PHPEMSPRO\tpl::getInstance()->assign('goods',$goods);
        \PHPEMSPRO\tpl::getInstance()->display('sale_goods');
    }

    public function del()
    {
        $saleid = \PHPEMSPRO\route::get('saleid');
        sale::delSale($saleid);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function modify()
    {
        $saleid = \PHPEMSPRO\route::get('saleid');
        $sale = sale::getSaleById($saleid);
        if(\PHPEMSPRO\route::get('modifysale'))
        {
            $args = \PHPEMSPRO\route::get('args');
            sale::modifySale($saleid,$args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "reload"
            );
            exit(json_encode($message));
        }
        else
        {
            \PHPEMSPRO\tpl::getInstance()->assign('sale',$sale);
            \PHPEMSPRO\tpl::getInstance()->display('sale_modify');
        }
    }

    public function add()
    {
        if(\PHPEMSPRO\route::get('addsale'))
        {
            $args = \PHPEMSPRO\route::get('args');
            $args['salestime'] = strtotime($args['salestime']);
            $args['saleetime'] = strtotime($args['saleetime']);
            $args['saletime'] = TIME;
            sale::addSale($args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "index.php?sale-master-sales"
            );
            exit(json_encode($message));
        }
        else
        {
            \PHPEMSPRO\tpl::getInstance()->display('sale_add');
        }
    }

    public function index()
    {
        $page = intval(\PHPEMSPRO\route::get('page'));
        $page = $page?$page:1;
        $args = array();
        $sales = sale::getSaleList($args,$page,10);
        \PHPEMSPRO\tpl::getInstance()->assign('sales',$sales);
        \PHPEMSPRO\tpl::getInstance()->assign('page',$page);
        \PHPEMSPRO\tpl::getInstance()->display('sale');
    }
}