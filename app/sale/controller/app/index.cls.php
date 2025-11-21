<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\sale\controller\app;

use PHPEMSPRO\sale\app;
use PHPEMSPRO\sale\model\sale;

class index extends app
{
    public function index()
    {
        $args = array(
            array("AND","salestime <= :salestime","salestime",TIME),
            array("AND","saleetime >= :saleetime","saleetime",TIME)
        );
        $sales = sale::getSalesByArgs($args);
        $goods = array();
        foreach($sales as $sale)
        {
            $args = array(
                array("AND","sgdsaleid = :sgdsaleid","sgdsaleid",$sale['saleid'])
            );
            $goods[$sale['saleid']] = sale::getSaleGoodsByArgs($args,12);
        }
        \PHPEMSPRO\tpl::getInstance()->assign('sales',$sales);
        \PHPEMSPRO\tpl::getInstance()->assign('goods',$goods);
        \PHPEMSPRO\tpl::getInstance()->display('index');
    }
}