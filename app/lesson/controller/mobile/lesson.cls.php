<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\lesson\controller\mobile;

use PHPEMSPRO\finance\model\orders;
use PHPEMSPRO\lesson\mobile;
use PHPEMSPRO\lesson\model\lessons;

class lesson extends mobile
{
    public function open()
    {
        $lessonid = \PHPEMSPRO\route::get('lessonid');
        $lesson = lessons::getLessonById($lessonid);
        $prices = 0;
        if(trim($lesson['lessonprice']))
        {
            $prices = array();
            $lesson['lessonprice'] = explode("\n",$lesson['lessonprice']);
            foreach($lesson['lessonprice'] as $p)
            {
                if($p)
                {
                    $p = explode(":",$p);
                    $prices[] = array('name'=>$p[0],'time'=>$p[1],'price'=>floatval($p[2]));
                }
            }
            \PHPEMSPRO\tpl::getInstance()->assign('prices',$prices);
        }
        if(\PHPEMSPRO\route::get('openlesson'))
        {
            $paytype = \PHPEMSPRO\route::get('paytype');
            $opentype = intval(\PHPEMSPRO\route::get('opentype'));
            if(\PHPEMSPRO\route::isWeixin())
            {
                $paytype = 'wxpay';
            }
            $args = array();
            $args['orderprice'] = floatval($prices[$opentype]['price']);
            $args['ordername'] = "{$lesson['lessonname']}{$prices[$opentype]['name']}";
            $args['orderitems'] = array(
                array(
                    'lessonid' => $lesson['lessonid'],
                    'lessonname' => $lesson['lessonname'],
                    'time' => $prices[$opentype]['time'],
                    'price' => $prices[$opentype]['price'],
                    'type' => 'lesson'
                )
            );
            $args['ordersn'] = date('YmdHi').rand(100,999);
            $args['orderstatus'] = 1;
            $args['orderusername'] = $this->_user['username'];
            $args['ordertime'] = TIME;
            $args['ordertype'] = 'lesson';
            orders::addOrder($args);
            if($paytype == 'wxpay')
            {
                if(\PHPEMSPRO\route::isMobile())
                {
                    if (\PHPEMSPRO\route::isWeixin())
                    {
                        $message = array(
                            'statusCode' => 200,
                            "message" => "下单成功！",
                            "callbackType" => "forward",
                            "forwardUrl" => 'index.php?user-mobile-user-wxpay&ordersn='.$args['ordersn']
                        );
                        \PHPEMSPRO\route::urlJump($message);
                    }
                    else
                    {
                        $result = \PHPEMSPRO\wxpay::getInstance()->outMwebUrl($args);
                        $message = array(
                            'statusCode' => 201,
                            "message" => "下单成功！",
                            "callbackType" => "forward",
                            //"forwardUrl" => 'index.php?user-mobile-user-wxh5pay&ordersn='.$args['ordersn']
                            "forwardUrl" => $result['mweb_url']
                        );
                        \PHPEMSPRO\route::urlJump($message);
                    }
                }
                else
                {
                    $message = array(
                        'statusCode' => 200,
                        "message" => "下单成功！",
                        "callbackType" => "forward",
                        "forwardUrl" => 'index.php?user-mobile-user-wxpay&ordersn='.$args['ordersn']
                    );
                    \PHPEMSPRO\route::urlJump($message);
                }
            }
            else
            {
                if(\PHPEMSPRO\route::isMobile())
                {
                    $payforurl = \PHPEMSPRO\alipay::getInstance()->createWapPayLink($args);
                    $message = array(
                        'statusCode' => 201,
                        "message" => "下单成功！",
                        "callbackType" => "forward",
                        "forwardUrl" => $payforurl
                    );
                    \PHPEMSPRO\route::urlJump($message);
                }
                else
                {
                    $payforurl = \PHPEMSPRO\alipay::getInstance()->createPagePayLink($args);
                    $message = array(
                        'statusCode' => 201,
                        "message" => "下单成功！",
                        "callbackType" => "forward",
                        "forwardUrl" => $payforurl
                    );
                    \PHPEMSPRO\route::urlJump($message);
                }
            }
        }
        else
        {
            \PHPEMSPRO\tpl::getInstance()->assign('lesson',$lesson);
            \PHPEMSPRO\tpl::getInstance()->display('lesson');
        }
    }

    public function endstatus()
    {
        $videoid = \PHPEMSPRO\route::get('videoid');
        $record = lessons::getLessonRecordByArgs(array(
            array("AND","recusername = :recusername","recusername",$this->_user['username']),
            array("AND","recvideoid = :recvideoid","recvideoid",$videoid)
        ));
        if($record)
        {
            lessons::modifyLessonRecord($record['recid'],array("recstatus" => 1,"recfinishtime" => TIME,"recrate" => 0,'reclasttime' => TIME));
        }
        $message = array(
            'statusCode' => 200
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function recordtime()
    {
        $videoid = \PHPEMSPRO\route::get('videoid');
        $time = \PHPEMSPRO\route::get('time');
        if($videoid)
        {
            $record = lessons::getLessonRecordByArgs(array(
                array("AND","recusername = :recusername","recusername",$this->_user['username']),
                array("AND","recvideoid = :recvideoid","recvideoid",$videoid)
            ));
            if($record)
            {
                lessons::modifyLessonRecord($record['recid'],array("recrate" => $time,'reclasttime' => TIME));
            }
            else
            {
                $args = array(
                    "recusername" => $this->_user['username'],
                    "recvideoid" => $videoid,
                    "recrate" => $time,
                    "recstarttime" => TIME,
                    "recstatus" => 0,
                    'reclasttime' => TIME
                );
                $record['recid'] = lessons::addLessonRecord($args);
            }
        }
        $message = array(
            'statusCode' => 200
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function index()
    {
        $lessonid = \PHPEMSPRO\route::get('lessonid');
        $videoid = \PHPEMSPRO\route::get('videoid');
        $lesson = lessons::getLessonById($lessonid);
        if($lesson['lessondemo'])
        {
            $status = true;
        }
        else
        {
            $status = lessons::isLessonMember($this->_user['username'],$lessonid);
            if($status && strtotime($status['oplendtime']) >= TIME)
            {
                $status = true;
            }
            else
            {
                $status = false;
            }
        }
        if(!$status && $videoid)
        {
            $message = array(
                'statusCode' => 200,
                "message" => "您需要开通课程才能继续学习！",
                "callbackType" => "forward",
                "forwardUrl" => 'index.php?lesson-mobile-lesson-open&lessonid='.$lessonid
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        $args = array(
            array("AND","videolesson = :videolesson","videolesson",$lessonid)
        );
        $videos = lessons::getVideosByArgs($args);
        foreach($videos as $v)
        {
            if(!$videoid)
            {
                $video = $v;
                break;
            }
            else
            {
                if($videoid == $v['videoid'])
                {
                    $video = $v;
                }
            }
        }
        $args = array(
            array("AND","recusername = :recusername","recusername",$this->_user['username']),
            array("AND","recvideoid = :recvideoid","recvideoid",$video['videoid'])
        );
        $record = lessons::getLessonRecordByArgs($args);
        $template = 'video_default';
        switch($video['videomodelcode'])
        {
            case "pdf":
                $template = 'video_pdf';
                break;

            default:
                $template = 'video_default';
                break;
        }
        \PHPEMSPRO\tpl::getInstance()->assign('record',$record);
        \PHPEMSPRO\tpl::getInstance()->assign('videoid',$videoid);
        \PHPEMSPRO\tpl::getInstance()->assign('video',$video);
        \PHPEMSPRO\tpl::getInstance()->assign('videos',$videos);
        \PHPEMSPRO\tpl::getInstance()->assign('lesson',$lesson);
        \PHPEMSPRO\tpl::getInstance()->assign('status',$status);
        \PHPEMSPRO\tpl::getInstance()->display($template);
    }
}