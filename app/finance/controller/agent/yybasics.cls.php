<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\finance\controller\agent;

use PHPEMSPRO\exam\model\exams;
use PHPEMSPRO\exam\model\points;
use PHPEMSPRO\exam\model\training;
use PHPEMSPRO\user\model\users;

class basics
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
            \PHPEMSPRO\tpl::getInstance()->assign('u',$this->u);
        }
        $this->user = users::getUserByUsername($this->_user['username']);
        $this->rate = round($this->user['userrate']/100,2);
        if($this->rate <= 0)$this->rate = 1;
        if($this->rate > 1)$this->rate = 1;
    }

    public function cancel()
    {
        if((TIME - $_SESSION['onlytime']) < 1)
        {
            $message = array(
                'statusCode' => 300,
                "message" => "请勿多次提交"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        $_SESSION['onlytime'] = TIME;
        $ordersn = \PHPEMSPRO\route::get('ordersn');
        $order = \PHPEMSPRO\finance\model\orders::getOrderBySn($ordersn);
        if($order['ordersn'])
        {
            if(TIME - strtotime($order['ordertime']) > 432000)
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => '该订单已下单超过5天，不能退款！'
                );
                \PHPEMSPRO\route::urlJump($message);
            }
            if(strtotime($order['orderactivetime']) || $order['orderstatus'] != 2)
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => '该订单未付款或已经激活使用，不能退款！'
                );
                \PHPEMSPRO\route::urlJump($message);
            }
            $coin = $this->user['usercoin'] + $order['orderprice'];
            users::modifyUser($this->user['userid'],array('usercoin' => $coin));
            \PHPEMSPRO\finance\model\orders::modifyOrder($ordersn,array('orderstatus' => 99));
            $actives = \PHPEMSPRO\finance\model\orders::getActivesByArgs(array(array("AND","activeorder = :activeorder","activeorder",$ordersn),array("AND","activestatus = 1")));
            if($actives)
            {
                foreach($actives as $active)
                {
                    $daytime = $active['activetime'] * 3600 * 24;
                    $subject = points::getSubjectById($active['activesubjectid']);
                    $r = \PHPEMSPRO\exam\model\exams::getIsMember($subject['subjectdb'],$active['activeusername'],$active['activebasicid']);
                    $endtime = strtotime($r['obendtime']);
                    if($endtime && $endtime > TIME)
                    {
                        $obendtime = $endtime - $daytime;
                        if($obendtime < TIME)$obendtime = TIME;
                        \PHPEMSPRO\exam\model\exams::modifyBasicMember($subject['subjectdb'],$r['obid'],array("obendtime" => $obendtime));
                    }
                }
            }
            $query = array(array("AND","activeorder = :activeorder","activeorder",$ordersn));
            \PHPEMSPRO\finance\model\orders::modifyActives($query,array('activestatus' => 99));
        }
        $message = array(
            'statusCode' => 200,
            "message" => '取消成功',
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function email()
    {
        $ordersn = \PHPEMSPRO\route::get('ordersn');
        $order = \PHPEMSPRO\finance\model\orders::getOrderBySn($ordersn);
        $app = \PHPEMSPRO\core\model\apps::getAppByCode('user');
        \mail::getInstance()->setServer('smtp.163.com',$app['appsetting']['emailaccount'],$app['appsetting']['emailpassword'],'465',true);
        \mail::getInstance()->setFrom($app['appsetting']['emailaccount']);
        $rs = array($order['orderusername'],'您设置的密码',$order['ordername']);
        $content = str_replace(array('{username}','{password}','{productname}'),$rs,$app['appsetting']['contenttext']);
        \mail::getInstance()->setReceiver($order['orderemail']);
        \mail::getInstance()->setMail('毕过题库开号通知',$content);
        \mail::getInstance()->sendMails();
        $message = array(
            'statusCode' => 200,
            "message" => '发送成功'
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function openpackage()
    {
        if((TIME - $_SESSION['onlytime']) < 1)
        {
            $message = array(
                'statusCode' => 300,
                "message" => "请勿多次提交"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        $_SESSION['onlytime'] = TIME;
        $userphone = \PHPEMSPRO\route::get('phonenumber');
        $userrealname = \PHPEMSPRO\route::get('name');
        $useremail = \PHPEMSPRO\route::get('email').'@qq.com';
        $u = users::getUserByPhone($userphone);
        if(!$u['userid'])
        {
            $args = array(
                'username' => $userphone,
                'userphone' => $userphone,
                'useremail' => $useremail,
                'userrealname' => $userrealname,
                'userpassword' => 'biguotiku100'
            );
            $group = users::getDefaultGroup();
            $args['usergroupcode'] = $group['groupcode'];
            users::addUser($args);
        }elseif(!$u['userrealname']){
        	if($userrealname)
            {
            	users::modifyUser($u['userid'],array('userrealname' => $userrealname));
            }
        }

        $orderprice = 0;
        $items = array();
        $title = null;
        $number = 0;
        $delids = \PHPEMSPRO\route::get('delids');
        foreach($delids as $trid => $ids)
        {
            foreach ($ids as $type => $id)
            {
                $training = training::getTrainingById($trid);
                if(!$title)$title = $training['trname'];
                $package = trim($training['trpackage']);
                $packprice = explode("\n",$package);
                $prices = array();
                foreach($packprice as $p)
                {
                    $tmp = explode(':',$p);
                    if($tmp[3])
                    {
                        $price = array(
                            'name' => $tmp[0],
                            'package' => $tmp[1],
                            'time' => $tmp[2],
                            'price' => floatval($tmp[3])
                        );
                        $prices[] = $price;
                    }
                }
                $orderprice += $prices[$type]['price'];
                $prices[$type]['package'] = explode(',',trim($prices[$type]['package']));
                foreach($prices[$type]['package'] as $pack)
                {
                    $pk = explode('-',$pack);
                    $subject = points::getSubjectById($pk[0]);
                    $basic = exams::getBasicById($subject['subjectdb'],$pk[1]);
                    $items[] = array(
                        'subjectid' => $pk[0],
                        'basicid' => $pk[1],
                        'basicname' => $basic['basic'],
                        'time' => $prices[$type]['time']
                    );
                }
                $number++;
            }
        }
        if(!$number)
        {
            $message = array(
                'statusCode' => 300,
                "message" => "请选择购买的套餐"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        $args = array();
        $args['ordersaleprice'] = $orderprice;
        $args['orderprice'] = round(floatval($args['ordersaleprice'] * $this->rate),2);
        if(floatval($this->user['usercoin']) < $args['orderprice'])
        {
            $message = array(
                'statusCode' => 300,
                "message" => "余额不足，请充值"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        $args['ordername'] = "{$title}等{$number}个套餐";;
        $args['orderitems'] = $items;
        $args['ordersn'] = date('YmdHi').rand(100,999);
        $args['orderusername'] = $u['username']?$u['username']:$userphone;
        $args['orderrealname'] = $userrealname;
        $args['orderemail'] = $useremail;
        $args['ordertime'] = TIME;
        $args['ordertype'] = 'exam';
        $args['orderagent'] = $this->user['username'];
        $args['orderstatus'] = 2;
        $args['orderactive'] = 0;
        $args['orderpaytype'] = 'coin';
      	$app = \PHPEMSPRO\core\model\apps::getAppByCode('user');
      	if($u['userid'])
        {
            $rs = array($userphone,'您设置的密码',$args['ordername']);
        }
        else
        {
            $rs = array($userphone,'biguotiku100',$args['ordername']);
        }
        $content = str_replace(array('{username}','{password}','{productname}'),$rs,$app['appsetting']['contenttext']);
      	$args['orderdescribe'] = $content;
        \PHPEMSPRO\finance\model\orders::addOrder($args);
        $coin = $this->user['usercoin'] - $args['orderprice'];
        users::modifyUser($this->user['userid'],array('usercoin' => $coin));

        \mail::getInstance()->setServer('smtp.163.com',$app['appsetting']['emailaccount'],$app['appsetting']['emailpassword'],'465',true);
        \mail::getInstance()->setFrom($app['appsetting']['emailaccount']);
        \mail::getInstance()->setReceiver($useremail);
        \mail::getInstance()->setMail('毕过题库开号通知',$content);
        \mail::getInstance()->sendMails();
        $message = array(
            'statusCode' => 200,
            "message" => '购买成功',
            "callbackType" => "forward",
            "forwardUrl" => "index.php?finance-agent-orders"
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function openbasic()
    {
        if((TIME - $_SESSION['onlytime']) < 1)
        {
            $message = array(
                'statusCode' => 300,
                "message" => "请勿多次提交"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        $_SESSION['onlytime'] = TIME;
        $userphone = \PHPEMSPRO\route::get('phonenumber');
        $userrealname = \PHPEMSPRO\route::get('name');
        $useremail = \PHPEMSPRO\route::get('email').'@qq.com';
        $u = users::getUserByPhone($userphone);
        if(!$u['userid'])
        {
            $args = array(
                'username' => $userphone,
                'userphone' => $userphone,
                'useremail' => $useremail,
                'userrealname' => $userrealname,
                'userpassword' => 'biguotiku100'
            );
            $group = users::getDefaultGroup();
            $args['usergroupcode'] = $group['groupcode'];
            users::addUser($args);
        }elseif(!$u['userrealname']){
        	if($userrealname)
            {
            	users::modifyUser($u['userid'],array('userrealname' => $userrealname));
            }
        }

        $orderprice = 0;
        $items = array();
        $title = null;
        $number = 0;
        $delids = \PHPEMSPRO\route::get('delids');
        foreach($delids as $trid => $ids)
        {
            foreach ($ids as $type => $id)
            {
                $training = training::getTrainingById($trid);
                if(!$title)$title = $training['trname'];
                $package = trim($training['trpackage']);
                $packprice = explode("\n",$package);
                $prices = array();
                foreach($packprice as $p)
                {
                    $tmp = explode(':',$p);
                    if($tmp[3])
                    {
                        $price = array(
                            'name' => $tmp[0],
                            'package' => $tmp[1],
                            'time' => $tmp[2],
                            'price' => floatval($tmp[3])
                        );
                        $prices[] = $price;
                    }
                }
                $orderprice += $prices[$type]['price'];
                $prices[$type]['package'] = explode(',',trim($prices[$type]['package']));
                foreach($prices[$type]['package'] as $pack)
                {
                    $pk = explode('-',$pack);
                    $subject = points::getSubjectById($pk[0]);
                    $basic = exams::getBasicById($subject['subjectdb'],$pk[1]);
                    $items[] = array(
                        'subjectid' => $pk[0],
                        'basicid' => $pk[1],
                        'basicname' => $basic['basic'],
                        'time' => $prices[$type]['time']
                    );
                }
                $number++;
            }
        }
        if(!$number)
        {
            $message = array(
                'statusCode' => 300,
                "message" => "请选择购买的套餐"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        $args = array();
        $args['ordersaleprice'] = $orderprice;
        $args['orderprice'] = round(floatval($args['ordersaleprice'] * $this->rate),2);
        if(floatval($this->user['usercoin']) < $args['orderprice'])
        {
            $message = array(
                'statusCode' => 300,
                "message" => "余额不足，请充值"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        $args['ordername'] = "{$title}等{$number}个套餐";;
        $args['orderitems'] = $items;
        $args['ordersn'] = date('YmdHi').rand(100,999);
        $args['orderusername'] = $u['username']?$u['username']:$userphone;
        $args['orderrealname'] = $userrealname;
        $args['orderemail'] = $useremail;
        $args['ordertime'] = TIME;
        $args['ordertype'] = 'exam';
        $args['orderagent'] = $this->user['username'];
        $args['orderstatus'] = 2;
        $args['orderactive'] = 0;
        $args['orderpaytype'] = 'coin';
      	$app = \PHPEMSPRO\core\model\apps::getAppByCode('user');
      	if($u['userid'])
        {
            $rs = array($userphone,'您设置的密码',$args['ordername']);
        }
        else
        {
            $rs = array($userphone,'biguotiku100',$args['ordername']);
        }
        $content = str_replace(array('{username}','{password}','{productname}'),$rs,$app['appsetting']['contenttext']);
      	$args['orderdescribe'] = $content;
        \PHPEMSPRO\finance\model\orders::addOrder($args);
        $coin = $this->user['usercoin'] - $args['orderprice'];
        users::modifyUser($this->user['userid'],array('usercoin' => $coin));
        
        \mail::getInstance()->setServer('smtp.163.com',$app['appsetting']['emailaccount'],$app['appsetting']['emailpassword'],'465',true);
        \mail::getInstance()->setFrom($app['appsetting']['emailaccount']);
        \mail::getInstance()->setReceiver($useremail);
        \mail::getInstance()->setMail('毕过题库开号通知',$content);
        \mail::getInstance()->sendMails();
        $message = array(
            'statusCode' => 200,
            "message" => '购买成功',
            "callbackType" => "forward",
            "forwardUrl" => "index.php?finance-agent-orders"
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function recharge()
    {
        if(\PHPEMSPRO\route::get('recharge'))
        {
            $money = \PHPEMSPRO\route::get('money');
            if($money < 1)
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "最低充值1元"
                );
                \PHPEMSPRO\route::urlJump($message);
            }
            $money = round($money,2);
            $args = array();
            $args['orderprice'] = $money;
            $args['ordername'] = '代理充值'.$money.'元';
            $args['orderitems'] = array();
            $args['ordersn'] = date('YmdHi').rand(100,999);
            $args['orderstatus'] = 1;
            $args['orderusername'] = $this->_user['username'];
            $args['ordertime'] = TIME;
            $args['ordertype'] = 'recharge';
            \PHPEMSPRO\finance\model\orders::addOrder($args);
            $payforurl = \PHPEMSPRO\alipay::getInstance()->createPagePayLink($args);
            $message = array(
                'statusCode' => 201,
                "message" => "下单成功！",
                "callbackType" => "forward",
                "forwardUrl" => $payforurl
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        else
        \PHPEMSPRO\tpl::getInstance()->display('recharge');
    }

    public function package()
    {
        $page = \PHPEMSPRO\route::get('page') > 0?\PHPEMSPRO\route::get('page'):1;
        $args = array();
        $args[] = array("AND","trpackage > ''");
        if($this->search['keyword'])
        {
            $args[] = array("AND","trname like :trname","trname","%{$this->search['keyword']}%");
        }
        $trainings = training::getTrainingList($args,$page,10);
        foreach($trainings['data'] as $key => $training)
        {
            $package = trim($training['trpackage']);
            $packprice = explode("\n",$package);
            $prices = array();
            foreach($packprice as $p)
            {
                $tmp = explode(':',$p);
                if($tmp[3])
                {
                    $price = array(
                        'name' => $tmp[0],
                        'package' => $tmp[1],
                        'time' => $tmp[2],
                        'price' => floatval($tmp[3]),
                        'agentprice' => floatval($tmp[3]) * $this->rate
                    );
                    $prices[] = $price;
                }
            }
            $trainings['data'][$key]['prices'] = $prices;
        }
        \PHPEMSPRO\tpl::getInstance()->assign('trainings',$trainings);
        \PHPEMSPRO\tpl::getInstance()->display('basics_package');
    }

    public function index()
    {
        $page = \PHPEMSPRO\route::get('page')?\PHPEMSPRO\route::get('page'):1;
        $args = array();
        if($this->search['keyword'])
        {
            $args[] = array("AND","stitle like :stitle","stitle","%{$this->search['keyword']}%");
        }
        if($this->search['trid'])
        {
            $args[] = array("AND","strid = :strid","strid",$this->search['trid']);
        }
        $basics = training::getSearchList($args,$page);
        $args = array();
        $trainings = training::getTrainingsByArgs($args);
        foreach($basics['data'] as $key => $basic)
        {
            $tmp = \PHPEMSPRO\exam\model\exams::getBasicById($basic['sdb'], $basic['sbasicid']);
            $prices = array();
            $basicprice = explode("\n",$tmp['basicprice']);
            foreach($basicprice as $p)
            {
                if($p)
                {
                    $p = explode(":",$p);
                    $prices[] = array('name'=>$p[0],'time'=>$p[1],'price'=>floatval($p[2]),'agentprice' => $this->rate * floatval($p[2]));
                }
            }
            $basics['data'][$key]['prices'] = $prices;
            $subject = \PHPEMSPRO\exam\model\points::getSubjectById($tmp['basicsubject']);
            if($trainings[$subject['subjecttrid']]['trpackage'])
            {
                $basics['data'][$key]['haspackage'] = $subject['subjecttrid'];
            }
        }
        \PHPEMSPRO\tpl::getInstance()->assign('trainings',$trainings);
        \PHPEMSPRO\tpl::getInstance()->assign('basics',$basics);
        \PHPEMSPRO\tpl::getInstance()->display('basics');
    }
}