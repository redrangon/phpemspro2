<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\finance\controller\master;

use PHPEMSPRO\exam\model\exams;
use PHPEMSPRO\exam\model\points;
use PHPEMSPRO\exam\model\question;
use PHPEMSPRO\exam\model\training;
use PHPEMSPRO\finance\master;
use PHPEMSPRO\finance\model\coupon;
use PHPEMSPRO\lesson\model\lessons;

class coupons extends master
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
    }

    public function clear()
    {
        $subjectid = \PHPEMSPRO\route::get('subjectid');
        $basicid = \PHPEMSPRO\route::get('basicid');
        coupon::clearCancelCoupons($subjectid,$basicid);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => 'forward',
            "forwardUrl" => "reload"
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function clearl()
    {
        $lessonid = \PHPEMSPRO\route::get('lessonid');
        coupon::clearCancellCoupons($lessonid);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => 'forward',
            "forwardUrl" => "reload"
        );
        \PHPEMSPRO\route::urlJump($message);
    }

    public function exportcoupons()
    {
        $subjectid = \PHPEMSPRO\route::get('subjectid');
        $basicid = \PHPEMSPRO\route::get('basicid');
        $status = intval(\PHPEMSPRO\route::get('status'));
        $subject = points::getSubjectById($subjectid);
        $basic = exams::getBasicById($subject['subjectdb'],$basicid);
        $args = array();
        $args[] = array("AND","cpnsubjectid = :cpnsubjectid","cpnsubjectid",$subjectid);
        $args[] = array("AND","cpnbasicid = :cpnbasicid","cpnbasicid",$basicid);
        $args[] = array("AND","cpnstatus = :cpnstatus","cpnstatus",$status);
        $args[] = array("AND","cpntype = :cpntype","cpntype",'exam');
        if($this->search['cpnsn'])$args[] = array("AND","cpnsn = :cpnsn","cpnsn",$this->search['cpnsn']);
        if($status)
        {
            if($this->search['stime'])$args[] = array("AND","cpnusetime >= :stime","stime",strtotime($this->search['stime']));
            if($this->search['etime'])$args[] = array("AND","cpnusetime <= :etime","etime",strtotime($this->search['etime']));
        }
        else
        {
            if($this->search['stime'])$args[] = array("AND","cpntime >= :stime","stime",strtotime($this->search['stime']));
            if($this->search['etime'])$args[] = array("AND","cpntime <= :etime","etime",strtotime($this->search['etime']));
        }
        $coupons = coupon::getCouponsByArgs($args);
        $r = array();
        $r[] = array(
            iconv("UTF-8","GBK",'券码'),
            iconv("UTF-8","GBK",'天数'),
            iconv("UTF-8","GBK",'科目'),
            iconv("UTF-8","GBK",'考场'),
            iconv("UTF-8","GBK",'生成时间'),
            iconv("UTF-8","GBK",'到期时间'),
            iconv("UTF-8","GBK",'使用时间'),
            iconv("UTF-8","GBK",'使用人')
        );
        foreach($coupons as $coupon)
        {
            $r[] = array(
                iconv("UTF-8","GBK",$coupon['cpnsn']),
                iconv("UTF-8","GBK",$coupon['cpndays']),
                iconv("UTF-8","GBK",$subject['subjectname']),
                iconv("UTF-8","GBK",$basic['basic']),
                iconv("UTF-8","GBK",$coupon['cpntime']),
                iconv("UTF-8","GBK",$coupon['cpncanceltime']),
                iconv("UTF-8","GBK",$coupon['cpnusetime']),
                iconv("UTF-8","GBK",$coupon['cpnusername'])
            );
        }
        $filename = 'public/data/out/coupons/'.TIME.'.csv';
        if(\PHPEMSPRO\files::outCsv($filename,$r))
        {
            $message = array(
                'statusCode' => 200,
                "message" => "成绩导出成功，转入下载页面，如果浏览器没有相应，请<a href=\"{$filename}\">点此下载</a>",
                "callbackType" => 'forward',
                "forwardUrl" => "{$filename}"
            );
        }
        else
        {
            $message = array(
                'statusCode' => 300,
                "message" => "成绩导出失败"
            );
        }
        exit(json_encode($message));
    }

    public function exportlcoupons()
    {
        $lessonid = \PHPEMSPRO\route::get('lessonid');
        $lesson = lessons::getLessonById($lessonid);
        $status = intval(\PHPEMSPRO\route::get('status'));
        $args = array();
        $args[] = array("AND","cpnlessonid = :cpnlessonid","cpnlessonid",$lessonid);
        $args[] = array("AND","cpnstatus = :cpnstatus","cpnstatus",$status);
        $args[] = array("AND","cpntype = :cpntype","cpntype",'lesson');
        if($this->search['cpnsn'])$args[] = array("AND","cpnsn = :cpnsn","cpnsn",$this->search['cpnsn']);
        if($status)
        {
            if($this->search['stime'])$args[] = array("AND","cpnusetime >= :stime","stime",strtotime($this->search['stime']));
            if($this->search['etime'])$args[] = array("AND","cpnusetime <= :etime","etime",strtotime($this->search['etime']));
        }
        else
        {
            if($this->search['stime'])$args[] = array("AND","cpntime >= :stime","stime",strtotime($this->search['stime']));
            if($this->search['etime'])$args[] = array("AND","cpntime <= :etime","etime",strtotime($this->search['etime']));
        }
        $coupons = coupon::getCouponsByArgs($args);
        $r = array();
        $r[] = array(
            iconv("UTF-8","GBK",'券码'),
            iconv("UTF-8","GBK",'天数'),
            iconv("UTF-8","GBK",'课程'),
            iconv("UTF-8","GBK",'生成时间'),
            iconv("UTF-8","GBK",'到期时间'),
            iconv("UTF-8","GBK",'使用时间'),
            iconv("UTF-8","GBK",'使用人')
        );
        foreach($coupons as $coupon)
        {
            $r[] = array(
                iconv("UTF-8","GBK",$coupon['cpnsn']),
                iconv("UTF-8","GBK",$coupon['cpndays']),
                iconv("UTF-8","GBK",$lesson['lessonname']),
                iconv("UTF-8","GBK",$coupon['cpntime']),
                iconv("UTF-8","GBK",$coupon['cpncanceltime']),
                iconv("UTF-8","GBK",$coupon['cpnusetime']),
                iconv("UTF-8","GBK",$coupon['cpnusername'])
            );
        }
        $filename = 'public/data/out/coupons/'.TIME.'.csv';
        if(\PHPEMSPRO\files::outCsv($filename,$r))
        {
            $message = array(
                'statusCode' => 200,
                "message" => "成绩导出成功，转入下载页面，如果浏览器没有相应，请<a href=\"{$filename}\">点此下载</a>",
                "callbackType" => 'forward',
                "forwardUrl" => "{$filename}"
            );
        }
        else
        {
            $message = array(
                'statusCode' => 300,
                "message" => "成绩导出失败"
            );
        }
        exit(json_encode($message));
    }

    public function batadd()
    {
        $subjectid = \PHPEMSPRO\route::get('subjectid');
        $basicid = \PHPEMSPRO\route::get('basicid');
        $subject = points::getSubjectById($subjectid);
        $basic = exams::getBasicById($subject['subjectdb'],$basicid);
        if(\PHPEMSPRO\route::get('batadd'))
        {
            $days = \PHPEMSPRO\route::get('days');
            $canceltime = strtotime(\PHPEMSPRO\route::get('canceltime'));
            $number = \PHPEMSPRO\route::get('number');
            if($number < 0)$number = 100;
            if($canceltime < TIME)$canceltime = TIME + 3600*24*365;
            coupon::batAddCoupon($number,$subjectid,$basicid,$days,$canceltime);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => 'forward',
                "forwardUrl" => "index.php?finance-master-coupons-coupons&subjectid={$subjectid}&basicid={$basicid}"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        else
        {
            \PHPEMSPRO\tpl::getInstance()->assign('basic',$basic);
            \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
            \PHPEMSPRO\tpl::getInstance()->assign('canceltime',date('Y-m-d',TIME + 365*24*3600));
            \PHPEMSPRO\tpl::getInstance()->display('coupons_batadd');
        }
    }

    public function bataddl()
    {
        $lessonid = \PHPEMSPRO\route::get('lessonid');
        $lesson = lessons::getLessonById($lessonid);
        if(\PHPEMSPRO\route::get('batadd'))
        {
            $days = \PHPEMSPRO\route::get('days');
            $canceltime = strtotime(\PHPEMSPRO\route::get('canceltime'));
            $number = \PHPEMSPRO\route::get('number');
            if($number < 0)$number = 100;
            if($canceltime < TIME)$canceltime = TIME + 3600*24*365;
            coupon::batAddlCoupon($number,$lessonid,$days,$canceltime);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => 'forward',
                "forwardUrl" => "index.php?finance-master-coupons-lcoupons&lessonid={$lessonid}"
            );
            \PHPEMSPRO\route::urlJump($message);
        }
        else
        {
            \PHPEMSPRO\tpl::getInstance()->assign('lesson',$lesson);
            \PHPEMSPRO\tpl::getInstance()->assign('canceltime',date('Y-m-d',TIME + 365*24*3600));
            \PHPEMSPRO\tpl::getInstance()->display('coupons_bataddl');
        }
    }

    public function coupons()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 0?$page:1;
        $subjectid = \PHPEMSPRO\route::get('subjectid');
        $basicid = \PHPEMSPRO\route::get('basicid');
        $subject = points::getSubjectById($subjectid);
        $basic = exams::getBasicById($subject['subjectdb'],$basicid);
        $args = array();
        $args[] = array("AND","cpnsubjectid = :cpnsubjectid","cpnsubjectid",$subjectid);
        $args[] = array("AND","cpnbasicid = :cpnbasicid","cpnbasicid",$basicid);
        $args[] = array("AND","cpnstatus = 0");
        $args[] = array("AND","cpntype = 'exam'");
        if($this->search['cpnsn'])$args[] = array("AND","cpnsn = :cpnsn","cpnsn",$this->search['cpnsn']);
        if($this->search['stime'])$args[] = array("AND","cpntime >= :stime","stime",strtotime($this->search['stime']));
        if($this->search['etime'])$args[] = array("AND","cpntime <= :etime","etime",strtotime($this->search['etime']));
        $coupons = coupon::getCouponList($args,$page);
        \PHPEMSPRO\tpl::getInstance()->assign('basic',$basic);
        \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
        \PHPEMSPRO\tpl::getInstance()->assign('coupons',$coupons);
        \PHPEMSPRO\tpl::getInstance()->display('coupons_coupons');
    }

    public function usedcoupons()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 0?$page:1;
        $subjectid = \PHPEMSPRO\route::get('subjectid');
        $basicid = \PHPEMSPRO\route::get('basicid');
        $subject = points::getSubjectById($subjectid);
        $basic = exams::getBasicById($subject['subjectdb'],$basicid);
        $args = array();
        $args[] = array("AND","cpnsubjectid = :cpnsubjectid","cpnsubjectid",$subjectid);
        $args[] = array("AND","cpnbasicid = :cpnbasicid","cpnbasicid",$basicid);
        $args[] = array("AND","cpnstatus = 1");
        $args[] = array("AND","cpntype = 'exam'");
        if($this->search['cpnsn'])$args[] = array("AND","cpnsn = :cpnsn","cpnsn",$this->search['cpnsn']);
        if($this->search['stime'])$args[] = array("AND","cpnusetime >= :stime","stime",strtotime($this->search['stime']));
        if($this->search['etime'])$args[] = array("AND","cpnusetime <= :etime","etime",strtotime($this->search['etime']));
        $coupons = coupon::getCouponList($args,$page);
        \PHPEMSPRO\tpl::getInstance()->assign('basic',$basic);
        \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
        \PHPEMSPRO\tpl::getInstance()->assign('coupons',$coupons);
        \PHPEMSPRO\tpl::getInstance()->display('coupons_usedcoupons');
    }

    public function basics()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 0?$page:1;
        $subjectid = \PHPEMSPRO\route::get('subjectid');
        $subject = points::getSubjectById($subjectid);
        $args = array();
        $args[] = array("AND","basicsubject = :subjectid","subjectid",$subjectid);
        $basics = \PHPEMSPRO\exam\model\exams::getBasicsList($subject['subjectdb'],$args,$page);
        foreach($basics['data'] as $key => $basic)
        {
            $number = \PHPEMSPRO\exam\model\exams::getBasicMemberNumber($subject['subjectdb'],$basic['basicid']);
            $basics['data'][$key]['number'] = $number;
        }
        \PHPEMSPRO\tpl::getInstance()->assign('subject',$subject);
        \PHPEMSPRO\tpl::getInstance()->assign('basics',$basics);
        \PHPEMSPRO\tpl::getInstance()->display('coupons_basics');
    }

    public function lcoupons()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 0?$page:1;
        $lessonid = \PHPEMSPRO\route::get('lessonid');
        $lesson = lessons::getLessonById($lessonid);
        $args = array();
        $args[] = array("AND","cpnlessonid = :cpnlessonid","cpnlessonid",$lessonid);
        $args[] = array("AND","cpnstatus = 0");
        $args[] = array("AND","cpntype = 'lesson'");
        if($this->search['cpnsn'])$args[] = array("AND","cpnsn = :cpnsn","cpnsn",$this->search['cpnsn']);
        if($this->search['stime'])$args[] = array("AND","cpntime >= :stime","stime",strtotime($this->search['stime']));
        if($this->search['etime'])$args[] = array("AND","cpntime <= :etime","etime",strtotime($this->search['etime']));
        $coupons = coupon::getCouponList($args,$page);
        \PHPEMSPRO\tpl::getInstance()->assign('lesson',$lesson);
        \PHPEMSPRO\tpl::getInstance()->assign('coupons',$coupons);
        \PHPEMSPRO\tpl::getInstance()->display('coupons_lcoupons');
    }

    public function usedlcoupons()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 0?$page:1;
        $lessonid = \PHPEMSPRO\route::get('lessonid');
        $lesson = lessons::getLessonById($lessonid);
        $args = array();
        $args[] = array("AND","cpnlessonid = :cpnlessonid","cpnlessonid",$lessonid);
        $args[] = array("AND","cpnstatus = 1");
        $args[] = array("AND","cpntype = 'lesson'");
        if($this->search['cpnsn'])$args[] = array("AND","cpnsn = :cpnsn","cpnsn",$this->search['cpnsn']);
        if($this->search['stime'])$args[] = array("AND","cpnusetime >= :stime","stime",strtotime($this->search['stime']));
        if($this->search['etime'])$args[] = array("AND","cpnusetime <= :etime","etime",strtotime($this->search['etime']));
        $coupons = coupon::getCouponList($args,$page);
        \PHPEMSPRO\tpl::getInstance()->assign('lesson',$lesson);
        \PHPEMSPRO\tpl::getInstance()->assign('coupons',$coupons);
        \PHPEMSPRO\tpl::getInstance()->display('coupons_usedlcoupons');
    }

    public function lessons()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 0?$page:1;
        $args = array();
        if($this->search['lessonid'])$args[] = array("AND","lessonid = :lessonid",'lessonid',$this->search['lessonid']);
        if($this->search['stime'])$args[] = array("AND","lessontime >= :slessontime",'slessontime',strtotime($this->search['stime']));
        if($this->search['etime'])$args[] = array("AND","lessontime <= :elessontime",'elessontime',strtotime($this->search['etime']));
        if($this->search['keyword'])$args[] = array("AND","lessonname LIKE :lessonname",'lessonname',"%{$this->search['keyword']}%");
        $lessons = lessons::getLessonList($args,$page);
        \PHPEMSPRO\tpl::getInstance()->assign('lessons',$lessons);
        \PHPEMSPRO\tpl::getInstance()->display('coupons_lessons');
    }

    public function index()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 0?$page:1;
        $args = array();
        $subjects = points::getSubjectsList($args,$page);
        $trainings = training::getTrainingsByArgs();
        \PHPEMSPRO\tpl::getInstance()->assign('trainings',$trainings);
        \PHPEMSPRO\tpl::getInstance()->assign('subjects',$subjects);
        \PHPEMSPRO\tpl::getInstance()->display('coupons');
    }
}