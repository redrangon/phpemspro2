<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\user\controller\mobile;

use PHPEMSPRO\exam\model\exams;
use PHPEMSPRO\exam\model\points;
use PHPEMSPRO\exam\model\training;
use PHPEMSPRO\user\mobile;

class coupon extends mobile
{public function active()
    {
        if(\PHPEMSPRO\route::get('active'))
        {
            $cpnsn = \PHPEMSPRO\route::get('cpnsn');
            $coupon = \PHPEMSPRO\finance\model\coupon::getCouponBySn($cpnsn);
            if($coupon && !$coupon['cpnstatus'])
            {
                if($coupon['cpntype'] == 'lesson')
                {
                    $lessonid = $coupon['cpnlessonid'];
                    $time = $coupon['cpndays'];
                    \PHPEMSPRO\lesson\model\lessons::openLesson($this->_user['username'], $lessonid, $time);
                    \PHPEMSPRO\finance\model\coupon::modifyCoupon($coupon['cpnid'],array('cpnstatus' => 1,'cpnusetime' => TIME,'cpnusername' => $this->_user['username']));
                    $message = array(
                        'statusCode' => 200,
                        "message" => "激活成功",
                        "callbackType" => 'forward',
                        "forwardUrl" => "index.php?lesson-mobile"
                    );
                }
                else
                {
                    $subjectid = $coupon['cpnsubjectid'];
                    $subject = \PHPEMSPRO\exam\model\points::getSubjectById($subjectid);
                    $basicid = $coupon['cpnbasicid'];
                    $r = \PHPEMSPRO\exam\model\exams::getIsMember($subject['subjectdb'],$this->_user['username'],$basicid);
                    if(!$r)
                    {
                        $args = array(
                            'obbasicid' => $basicid,
                            'obusername' => $this->_user['username'],
                            'obtime' => TIME,
                            'obendtime' => TIME + 24*3600*$coupon['cpndays']
                        );
                        \PHPEMSPRO\exam\model\exams::addBasicMember($subject['subjectdb'],$args);
                    }
                    elseif(strtotime($r['obendtime']) < TIME)
                    {
                        $args = array(
                            'obendtime' => TIME + 24*3600*$coupon['cpndays']
                        );
                        \PHPEMSPRO\exam\model\exams::modifyBasicMember($subject['subjectdb'],$r['obid'],$args);
                    }
                    elseif(strtotime($r['obendtime']) >= TIME)
                    {
                        $args = array(
                            'obendtime' => strtotime($r['obendtime']) + 24*3600*$coupon['cpndays']
                        );
                        \PHPEMSPRO\exam\model\exams::modifyBasicMember($subject['subjectdb'],$r['obid'],$args);
                    }
                    \PHPEMSPRO\finance\model\coupon::modifyCoupon($coupon['cpnid'],array('cpnstatus' => 1,'cpnusetime' => TIME,'cpnusername' => $this->_user['username']));
                    $message = array(
                        'statusCode' => 200,
                        "message" => "激活成功",
                        "callbackType" => 'forward',
                        "forwardUrl" => "index.php?exam-mobile"
                    );
                }
            }
            else
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "该激活码无效"
                );
            }
            exit(json_encode($message));
        }
    }

    public function index()
    {
        \PHPEMSPRO\tpl::getInstance()->display('coupon');
    }
}