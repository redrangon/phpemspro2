<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\user\controller\app;

use PHPEMSPRO\sale\model\elem;
use PHPEMSPRO\user\app;

class orders extends app
{public function qrcode()
    {
        $data = urldecode(\PHPEMSPRO\route::get('data'));
        \QRcode::png($data,false,QR_ECLEVEL_L,7);
    }

    public function getstatus()
    {
        $ordersn = \PHPEMSPRO\route::get('ordersn');
        $order = \PHPEMSPRO\finance\model\orders::getOrderBySn($ordersn);
        if($order['orderstatus'] == 1)
        {
            $message = array(
                'statusCode' => 300
            );
        }
        else
        {
            $message = array(
                'statusCode' => 200,
                "message" => "支付成功",
                "callbackType" => "forward",
                "forwardUrl" => "index.php?user-app-orders-detail&ordersn=".$ordersn
            );
        }
        exit(json_encode($message));
    }

    public function wxpay()
    {
        $ordersn = \PHPEMSPRO\route::get('ordersn');
        $order = \PHPEMSPRO\finance\model\orders::getOrderBySn($ordersn);
        $wxpayforurl = \PHPEMSPRO\wxpay::getInstance()->outQrcodeUrl($order);
        \PHPEMSPRO\tpl::getInstance()->assign('data',urlencode($wxpayforurl['code_url']));
        \PHPEMSPRO\tpl::getInstance()->assign('order',$order);
        \PHPEMSPRO\tpl::getInstance()->display('orders_wxpay');
    }

    public function detail()
    {
        $ordersn = \PHPEMSPRO\route::get('ordersn');
        $order = \PHPEMSPRO\finance\model\orders::getOrderBySn($ordersn);
        $alipayforurl = \PHPEMSPRO\alipay::getInstance()->createPagePayLink($order);
        \PHPEMSPRO\tpl::getInstance()->assign('alipayforurl',$alipayforurl);
        \PHPEMSPRO\tpl::getInstance()->assign('itemtypes',elem::$itemtypes);
        \PHPEMSPRO\tpl::getInstance()->assign('order',$order);
        \PHPEMSPRO\tpl::getInstance()->display('orders_detail');
    }

    public function index()
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