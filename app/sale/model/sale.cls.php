<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2019/1/17
 * Time: 21:20
 */

namespace PHPEMSPRO\sale\model;


class sale
{
    static function getSaleById($id)
    {
        $args = array(array("AND","saleid = :saleid","saleid",$id));
        $data = array(
            'select' => false,
            'table' => 'sales',
            'query' => $args
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElement($data);
    }

    static function modifySale($id,$args)
    {
        $data = array(
            'table' => 'sales',
            'value' => $args,
            'query' => array(
                array("AND","saleid = :saleid","saleid",$id)
            )
        );
        \PHPEMSPRO\pepdo::getInstance()->updateElement($data);
    }

    //添加信息
    static function addSale($args)
    {
        $data = array(
            'table' => 'sales',
            'query' => $args
        );
        \PHPEMSPRO\pepdo::getInstance()->insertElement($data);
    }

    static function delSale($id)
    {
        $data = array(
            'table' => 'sales',
            'query' => array(
                array("AND","saleid = :saleid","saleid",$id)
            )
        );
        return \PHPEMSPRO\pepdo::getInstance()->delElement($data);
    }

    static function getSaleList($args,$page,$number = \PHPEMSPRO\config::webpagenumber,$orderby = 'saleid desc')
    {
        $data = array(
            'table' => 'sales',
            'query' => $args,
            'orderby' => $orderby
        );
        return \PHPEMSPRO\pepdo::getInstance()->listElements($page,$number,$data);
    }

    static function getSalesByArgs($args,$number = 10,$orderby = 'saleid desc')
    {
        $data = array(
            'table' => 'sales',
            'query' => $args,
            'orderby' => $orderby
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElements($data);
    }

    static function getSaleGoodsList($args,$page,$number = \PHPEMSPRO\config::webpagenumber,$orderby = 'sgdid desc')
    {
        $data = array(
            'table' => 'sales_goods',
            'query' => $args,
            'orderby' => $orderby
        );
        return \PHPEMSPRO\pepdo::getInstance()->listElements($page,$number,$data);
    }

    static function getSaleGoodsByArgs($args,$number = 10,$orderby = 'sgdid desc')
    {
        $data = array(
            'table' => 'sales_goods',
            'query' => $args,
            'orderby' => $orderby,
            'limit' => $number
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElements($data);
    }

    static function getSaleGoodsById($id)
    {
        $args = array(array("AND","sgdid = :sgdid","sgdid",$id));
        $data = array(
            'select' => false,
            'table' => 'sales_goods',
            'query' => $args
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElement($data);
    }

    static function modifySaleGoods($id,$args)
    {
        $data = array(
            'table' => 'sales_goods',
            'value' => $args,
            'query' => array(
                array("AND","sgdid = :sgdid","sgdid",$id)
            )
        );
        \PHPEMSPRO\pepdo::getInstance()->updateElement($data);
    }

    //添加信息
    static function addSaleGoods($args)
    {
        $data = array(
            'table' => 'sales_goods',
            'query' => $args
        );
        \PHPEMSPRO\pepdo::getInstance()->insertElement($data);
    }

    static function delSaleGoods($id)
    {
        $data = array(
            'table' => 'sales_goods',
            'query' => array(
                array("AND","sgdid = :sgdid","sgdid",$id)
            )
        );
        return \PHPEMSPRO\pepdo::getInstance()->delElement($data);
    }

    static function getSaleItemsList($args,$page,$number = \PHPEMSPRO\config::webpagenumber,$orderby = 'simid desc')
    {
        $data = array(
            'table' => 'sales_items',
            'query' => $args,
            'orderby' => $orderby
        );
        return \PHPEMSPRO\pepdo::getInstance()->listElements($page,$number,$data);
    }

    static function getSaleItemsByArgs($args,$orderby = 'simid desc')
    {
        $data = array(
            'table' => 'sales_items',
            'query' => $args,
            'orderby' => $orderby,
            'limit' => false
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElements($data);
    }

    static function getSaleItemsById($id)
    {
        $args = array(array("AND","simid = :simid","simid",$id));
        $data = array(
            'select' => false,
            'table' => 'sales_items',
            'query' => $args
        );
        return \PHPEMSPRO\pepdo::getInstance()->getElement($data);
    }

    static function modifySaleItems($id,$args)
    {
        $data = array(
            'table' => 'sales_items',
            'value' => $args,
            'query' => array(
                array("AND","simid = :simid","simid",$id)
            )
        );
        \PHPEMSPRO\pepdo::getInstance()->updateElement($data);
    }

    //添加信息
    static function addSaleItems($args)
    {
        $data = array(
            'table' => 'sales_items',
            'query' => $args
        );
        \PHPEMSPRO\pepdo::getInstance()->insertElement($data);
    }

    static function delSaleItems($id)
    {
        $data = array(
            'table' => 'sales_items',
            'query' => array(
                array("AND","simid = :simid","simid",$id)
            )
        );
        return \PHPEMSPRO\pepdo::getInstance()->delElement($data);
    }
}