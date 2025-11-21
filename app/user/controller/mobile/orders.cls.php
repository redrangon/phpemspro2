<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\user\controller\mobile;

use PHPEMSPRO\user\mobile;

class orders extends mobile
{public function index()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 1?$page:1;
        $args = array(
            array("AND","orderusername = :orderusername","orderusername",$this->_user['username']),
            array("AND","orderstatus = 2")
        );
        $orders = \PHPEMSPRO\finance\model\orders::getOrderList($args,$page);
        \PHPEMSPRO\tpl::getInstance()->assign('page',$page);
        \PHPEMSPRO\tpl::getInstance()->assign('orders',$orders);
        \PHPEMSPRO\tpl::getInstance()->display('orders');
    }
}