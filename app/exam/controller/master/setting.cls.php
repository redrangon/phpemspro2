<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/11/25
 * Time: 21:24
 */

namespace PHPEMSPRO\exam\controller\master;

use PHPEMSPRO\exam\master;
use PHPEMSPRO\exam\model\points;
use PHPEMSPRO\exam\model\question;
use PHPEMSPRO\exam\model\training;

class setting extends master
{
    public function training()
    {
        $page = \PHPEMSPRO\route::get('page') > 0?\PHPEMSPRO\route::get('page'):1;
        $trainings = training::getTrainingList(array(),$page);
        \PHPEMSPRO\tpl::getInstance()->assign('trainings',$trainings);
        \PHPEMSPRO\tpl::getInstance()->display('setting_training');
    }

    public function addtraining()
    {
        if(\PHPEMSPRO\route::get('addtraining'))
        {
            $args = \PHPEMSPRO\route::get('args');
            training::addTraining($args);
            $message = array(
                'statusCode' => 200,
                "message" => "添加成功",
                "callbackType" => "forward",
                "forwardUrl" => "index.php?exam-master-setting-training"
            );
            exit(json_encode($message));
        }
        else
        {
            \PHPEMSPRO\tpl::getInstance()->display('setting_addtraining');
        }
    }

    public function modifytraining()
    {
        $trainingid = \PHPEMSPRO\route::get('trid');
        $training = training::getTrainingById($trainingid);
        if(\PHPEMSPRO\route::get('modifytraining'))
        {
            $args = \PHPEMSPRO\route::get('args');
            training::modifyTrainingById($trainingid,$args);
            $message = array(
                'statusCode' => 200,
                "message" => "修改成功",
                "callbackType" => "forward",
                "forwardUrl" => "reload"
            );
            exit(json_encode($message));
        }
        else
        {
            \PHPEMSPRO\tpl::getInstance()->assign('training',$training);
            \PHPEMSPRO\tpl::getInstance()->display('setting_modifytraining');
        }
    }

    public function deltraining()
    {
        $trainingid = \PHPEMSPRO\route::get('trid');
        $args = array(array("AND","subjecttrid = :subjecttrid","subjecttrid",$trainingid));
        if(points::getSubjectsNumber($args))
        {
            $message = array(
                'statusCode' => 300,
                "message" => "删除失败，请先删除此培训下的科目"
            );
            exit(json_encode($message));
        }
        training::delTraining($trainingid);
        $message = array(
            'statusCode' => 200,
            "message" => "删除成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function deltrainings()
    {
        $trainingids = \PHPEMSPRO\route::get('delids');
        foreach($trainingids as $trainingid => $p)
        {
            $args = array(array("AND","subjecttrid = :subjecttrid","subjecttrid",$trainingid));
            if(points::getSubjectsNumber($args))
            {
                $training = training::getTrainingById($trainingid);
                $message = array(
                    'statusCode' => 300,
                    "message" => "删除失败，请先删除《{$training['trname']}》下的科目"
                );
                exit(json_encode($message));
            }
        }
        foreach($trainingids as $key => $trainingid)
        {
            training::delTraining($trainingid);
        }
        $message = array(
            'statusCode' => 200,
            "message" => "删除成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function questype()
    {
        $questypes = \PHPEMSPRO\exam\model\question::getQuestypesByArgs();
        \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
        \PHPEMSPRO\tpl::getInstance()->display('setting_questype');
    }

    public function addquestype()
    {
        if(\PHPEMSPRO\route::get('addquestype'))
        {
            $args = \PHPEMSPRO\route::get('args');
            question::addQuestype($args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "index.php?exam-master-setting-questype"
            );
            exit(json_encode($message));
        }
        else
        {
            \PHPEMSPRO\tpl::getInstance()->display('setting_addquestype');
        }
    }

    public function modifyquestype()
    {
        $questid = \PHPEMSPRO\route::get('questid');
        $questype = question::getQuestypeById($questid);
        if(\PHPEMSPRO\route::get('modifyquestype'))
        {
            $args = \PHPEMSPRO\route::get('args');
            if($args['questsort'])
            {
                $args['questchoice'] = 0;
            }
            question::modifyQuestypeById($questid,$args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "reload"
            );
            exit(json_encode($message));
        }
        else
        {
            \PHPEMSPRO\tpl::getInstance()->assign('questype',$questype);
            \PHPEMSPRO\tpl::getInstance()->display('setting_modifyquestype');
        }
    }

    public function delquestype()
    {
        $questid = \PHPEMSPRO\route::get('questid');
        question::delQuestype($questid);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function region()
    {
        $page = \PHPEMSPRO\route::get('page') > 0?\PHPEMSPRO\route::get('page'):1;
        $questypes = \PHPEMSPRO\exam\model\training::getTrainingList($page);
        \PHPEMSPRO\tpl::getInstance()->assign('questypes',$questypes);
        \PHPEMSPRO\tpl::getInstance()->display('setting_region');
    }

    public function index()
    {
        $app = \PHPEMSPRO\core\model\apps::getAppByCode('exam');
        if(\PHPEMSPRO\route::get('setting'))
        {
            $args = \PHPEMSPRO\route::get('args');
            \PHPEMSPRO\core\model\apps::modifyAppByCode('exam',array('appsetting' => $args));
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "reload"
            );
            exit(json_encode($message));
        }
        else
        {
            \PHPEMSPRO\tpl::getInstance()->assign('setting',$app['appsetting']);
            \PHPEMSPRO\tpl::getInstance()->display('setting');
        }
    }
}