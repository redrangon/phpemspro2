<?php
namespace PHPEMSPRO;
use PHPEMSPRO\finance\model\orders;

require_once PEP_PATH."/public/lib/packages/alipay/AopSdk.php";
require_once PEP_PATH."/public/lib/packages/alipay/AlipayTradeService.php";
require_once PEP_PATH."/public/lib/packages/alipay/AlipayTradePagePayContentBuilder.php";
require_once PEP_PATH."/public/lib/packages/alipay/AlipayTradeWapPayContentBuilder.php";

class alipay
{
	static $instance;
	public $config = array (
        //应用ID,您的APPID。
        'app_id' => config::aliappid,
        //商户私钥，您的原始格式RSA私钥
        'merchant_private_key' => config::alikey,
        //异步通知地址
        'notify_url' => config::webpath."api/alinotify.php",
        //同步跳转
        'return_url' => config::webpath."api/alireturn.php",
        //编码格式
        'charset' => "UTF-8",
        //签名方式
        'sign_type'=>"RSA2",//RSA2
        //支付宝网关
        'gatewayUrl' => "https://openapi.alipay.com/gateway.do",
        //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
        'alipay_public_key' => config::alipub,
    );

    static function getInstance()
    {
        if(self::$instance == NULL)
        {
            self::$instance = new static();//亦可写为 new self();
        }
        return self::$instance;
    }

    public function createPagePayLink($order)
    {
        $payRequestBuilder = new \AlipayTradePagePayContentBuilder();
        $payRequestBuilder->setBody("购买课程，开通考场");
        $payRequestBuilder->setSubject($order['ordername']);
        $payRequestBuilder->setOutTradeNo($order['ordersn']);
        $payRequestBuilder->setTotalAmount($order['orderprice']);

        $payResponse = new \AlipayTradeService($this->config);
        return $payResponse->pagePay($payRequestBuilder,$this->config['return_url'],$this->config['notify_url']);
    }

    public function createWapPayLink($order)
    {
        $payRequestBuilder = new \AlipayTradeWapPayContentBuilder();
        $payRequestBuilder->setBody("购买课程，开通考场");
        $payRequestBuilder->setSubject($order['ordername']);
        $payRequestBuilder->setOutTradeNo($order['ordersn']);
        $payRequestBuilder->setTotalAmount($order['orderprice']);

        $payResponse = new \AlipayTradeService($this->config);
        return $payResponse->wapPay($payRequestBuilder,$this->config['return_url'],$this->config['notify_url']);
    }

    public function notify()
    {
        $alipaySevice = new \AlipayTradeService($this->config);
        //$alipaySevice->writeLog(var_export($_POST,true));
        $result = $alipaySevice->check($_POST);
        if($result)
        {
            $sn = route::get('out_trade_no');
            $order = orders::getOrderBySn($sn);
            if($order && $order['orderstatus'] != 2)
            {
                orders::payforOrder($sn,'alipay');
            }
            echo "success";
        }
        else
        {
            echo "fail";
        }
    }

    public function reback()
    {
        $alipaySevice = new \AlipayTradeService($this->config);
        //$alipaySevice->writeLog(var_export($_POST,true));
        $result = $alipaySevice->check($_GET);
        if($result)
        {
            $sn = route::get('out_trade_no');
            $order = \finance\model\orders::getOrderBySn($sn);
            if($order['ordertype'] == 'exam')
            {
                header("location:../index.php?exam-app-basic");
            }
            elseif($order['ordertype'] == 'lesson')
            {
                $item = current($order['orderitems']);
                header("location:../index.php?lesson-app-lesson&lessonid={$item['lessonid']}");
            }
            elseif($order['ordertype'] == 'recharge')
            {
                header("location:../index.php?finance-agent-basics");
            }
            elseif($order['ordertype'] == 'sale')
            {
                header("location:../index.php?sale-app-sales-detail&ordersn=".$sn);
            }
            exit;
        }
        else
        {
            //
        }
    }
}

?>