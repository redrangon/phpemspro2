<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\exam\controller\app;

use PHPEMSPRO\exam\app;
use PHPEMSPRO\exam\model\exams;
use PHPEMSPRO\exam\model\points;
use PHPEMSPRO\exam\model\training;
use PHPEMSPRO\exam\model\favor;
use PHPEMSPRO\exam\model\question;
use PHPEMSPRO\finance\model\orders;

class basic extends app
{

    protected function _start()
    {
        if(!$this->currentsubject)
        {
            $message = array(
                'statusCode' => 300,
                "callbackType" => "forward",
                "forwardUrl" => "index.php"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        $this->subject = points::getSubjectById($this->currentsubject);
        $this->basic = exams::getBasicById($this->subject['subjectdb'],$this->currentbasic);
        $this->status = exams::getIsMember($this->subject['subjectdb'],$this->_user['username'],$this->currentbasic);
        if(strtotime($this->status['obendtime']) >= TIME)
        {
            $this->status['status'] = true;
        }
        \PHPEMSPRO\tpl::getInstance()->assign('subject',$this->subject);
        \PHPEMSPRO\tpl::getInstance()->assign('basic',$this->basic);
        \PHPEMSPRO\tpl::getInstance()->assign('status',$this->status);
    }

    public function package()
    {
        $trid = \PHPEMSPRO\route::get('trid');
        $training = training::getTrainingById($trid);
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
                    'price' => $tmp[3]
                );
                $prices[] = $price;
            }
        }
        if(\PHPEMSPRO\route::get('openbasic'))
        {
            $opentype = intval(\PHPEMSPRO\route::get('opentype'));
            $args = array();
            $args['orderprice'] = floatval($prices[$opentype]['price']);
            $args['ordername'] = $prices[$opentype]['name'];
            $prices[$opentype]['package'] = explode(',',trim($prices[$opentype]['package']));
            $args['orderitems'] = array();
            foreach($prices[$opentype]['package'] as $pack)
            {
                $pk = explode('-',$pack);
                $subject = points::getSubjectById($pk[0]);
                $basic = exams::getBasicById($subject['subjectdb'],$pk[1]);
                $args['orderitems'][] = array(
                    'subjectid' => $pk[0],
                    'basicid' => $pk[1],
                    'basicname' => $basic['basic'],
                    'time' => $prices[$opentype]['time'],
                    'type' => 'exam'
                );
            }
            $args['ordersn'] = date('YmdHi').rand(100,999);
            $args['orderstatus'] = 1;
            $args['orderusername'] = $this->_user['username'];
            $args['ordertime'] = TIME;
            $args['ordertype'] = 'exam';
            orders::addOrder($args);
            $message = array(
                'statusCode' => 200,
                "message" => "下单成功！",
                "callbackType" => "forward",
                "forwardUrl" => 'index.php?user-app-orders-detail&ordersn='.$args['ordersn']
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        else
        {
            header("location:index.php?exam-app-basic-open");
            exit;
        }
    }

    public function open()
    {
        $prices = 0;
        if(trim($this->basic['basicprice']))
        {
            $prices = array();
            $this->basic['basicprice'] = explode("\n",$this->basic['basicprice']);
            foreach($this->basic['basicprice'] as $p)
            {
                if($p)
                {
                    $p = explode(":",$p);
                    $prices[] = array('name'=>$p[0],'time'=>$p[1],'price'=>floatval($p[2]));
                }
            }
            \PHPEMSPRO\tpl::getInstance()->assign('prices',$prices);
        }
        if(\PHPEMSPRO\route::get('openbasic'))
        {
            $paytype = \PHPEMSPRO\route::get('paytype');
            $opentype = intval(\PHPEMSPRO\route::get('opentype'));
            if(\PHPEMSPRO\route::isWeixin())
            {
                $paytype = 'wxpay';
            }
            $args = array();
            $args['orderprice'] = floatval($prices[$opentype]['price']);
            $args['ordername'] = "{$this->basic['basic']}{$prices[$opentype]['name']}";
            $args['orderitems'] = array(
                array(
                    'subjectid' => $this->subject['subjectid'],
                    'basicid' => $this->basic['basicid'],
                    'basicname' => $this->basic['basic'],
                    'time' => $prices[$opentype]['time'],
                    'price' => $prices[$opentype]['price'],
                    'type' => 'exam'
                )
            );
            $args['ordersn'] = date('YmdHi').rand(100,999);
            $args['orderstatus'] = 1;
            $args['orderusername'] = $this->_user['username'];
            $args['ordertime'] = TIME;
            $args['ordertype'] = 'exam';
            orders::addOrder($args);
            $message = array(
                'statusCode' => 200,
                "message" => "下单成功！",
                "callbackType" => "forward",
                "forwardUrl" => 'index.php?user-app-orders-detail&ordersn='.$args['ordersn']
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        else
        {
            $training = training::getTrainingById($this->subject['subjecttrid']);
            $package = trim($training['trpackage']);
            $packprice = explode("\n",$package);
            $package = null;
            $lestprice = 0;
            foreach($packprice as $p)
            {
                $tmp = explode(':',$p);
                if($tmp[3])
                {
                    $price = array(
                        'name' => $tmp[0],
                        'package' => $tmp[1],
                        'time' => $tmp[2],
                        'price' => $tmp[3]
                    );
                    $package[] = $price;
                    if(!$lestprice){
                        $lestprice = $price['price'];
                    }
                    else{
                        if($lestprice > $price['price']){
                            $lestprice = $price['price'];
                        }
                    }
                }
            }
            \PHPEMSPRO\tpl::getInstance()->assign('lestprice',$lestprice);
            \PHPEMSPRO\tpl::getInstance()->assign('package',$package);
            \PHPEMSPRO\tpl::getInstance()->assign('training',$training);
            \PHPEMSPRO\tpl::getInstance()->display('basic_open');
        }
    }

    public function index()
    {
        if($this->basic['basicexam']['model'] == 2)
        {
            $message = array(
                'statusCode' => 200,
                "callbackType" => "forward",
                "forwardUrl" => "index.php?exam-app-exam"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        $points = points::getPointsBySubjectid($this->subject['subjectid']);
        $sections = $points['sections'];
        $points = $points['points'];
        $record = favor::getRecordSession($this->subject['subjectdb'],$this->_user['username']);
        $lastquestion = json_decode(\PHPEMSPRO\pedis::getInstance()->getHashData('lastquestion',$this->_user['username'].'-'.$this->subject['subjectdb'].'-'.$this->currentbasic),true);
        if(is_array($lastquestion))$pointid = key($lastquestion);
        else $pointid = $sections[0]['points'][0]['pointid'];
        $index = $lastquestion[$pointid];
        $wrong = array();
        $right = array();
        $favor = array();
        $note = array();
        $numbers = array();
        $donenumber = array();
        foreach($points as $key => $point)
        {
            $rt = 0;
            $wg = 0;
            foreach($point as $p)
            {
                if($this->basic['basicpoints'][$key][$p['pointid']])
                {
                    $numbers[$p['pointid']] = question::getQuestionNumberByPointid($this->subject['subjectdb'],$p['pointid']);
                    $favor[$key] += intval(\PHPEMSPRO\pedis::getInstance()->getHashData('favornumber',$this->_user['username'].'-'.$this->subject['subjectdb'].'-'.$p['pointid']));
                    $note[$key] += intval(\PHPEMSPRO\pedis::getInstance()->getHashData('notenumber',$this->_user['username'].'-'.$this->subject['subjectdb'].'-'.$p['pointid']));
                    $rt += $record['recordnumber'][$p['pointid']]['right'];
                    $wg += $record['recordnumber'][$p['pointid']]['wrong'];
                    $donenumber[$p['pointid']] = $record['recordnumber'][$p['pointid']]['right'] + $record['recordnumber'][$p['pointid']]['wrong'];
                    if($p['pointid'] == $pointid)
                    {
                        $thispoint = $p;
                    }
                }
            }
            $wrong[$key] = intval($wg);
            $right[$key] = intval($rt);
        }

        unset($record);
        \PHPEMSPRO\tpl::getInstance()->assign('sections',$sections);
        \PHPEMSPRO\tpl::getInstance()->assign('allnumber',array('right' => array_sum($right),'wrong' => array_sum($wrong),'all' => array_sum($numbers)));
        \PHPEMSPRO\tpl::getInstance()->assign('donenumber',$donenumber);
        \PHPEMSPRO\tpl::getInstance()->assign('numbers',$numbers);
        \PHPEMSPRO\tpl::getInstance()->assign('wrong',$wrong);
        \PHPEMSPRO\tpl::getInstance()->assign('favor',$favor);
        \PHPEMSPRO\tpl::getInstance()->assign('note',$note);
        \PHPEMSPRO\tpl::getInstance()->assign('points',$points);
        \PHPEMSPRO\tpl::getInstance()->assign('point',$thispoint);
        \PHPEMSPRO\tpl::getInstance()->assign('index',$index+1);
        \PHPEMSPRO\tpl::getInstance()->display('basic');
    }
}