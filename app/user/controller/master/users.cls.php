<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/11/25
 * Time: 21:24
 */

namespace PHPEMSPRO\user\controller\master;

use PHPEMSPRO\database\model\model;
use PHPEMSPRO\user\master;

class users extends master
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
        }
    }

    public function import()
    {
        if(\PHPEMSPRO\route::get('insertUser'))
        {
            $uploadfile = \PHPEMSPRO\route::get('uploadfile');
            if(!file_exists($uploadfile))
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "上传文件不存在"
                );
                exit(json_encode($message));
            }
            else
            {
                setlocale(LC_ALL,'zh_CN');
                $handle = fopen($uploadfile,"r");
                $defaultgroup = \PHPEMSPRO\user\model\users::getDefaultGroup();
                $app = \PHPEMSPRO\core\model\apps::getAppByCode('user');
                $tpfields = explode(',',$app['appsetting']['regfields']);
                while ($data = fgetcsv($handle,200))
                {
                    if($data[0] && $data[1])
                    {
                        $args = array();
                        $args['username'] = iconv("GBK","UTF-8",$data[0]);
                        if(\PHPEMSPRO\strings::isUserName($args['username']))
                        {
                            $u = \PHPEMSPRO\user\model\users::getUserByUserName($args['username']);
                            if(!$u)
                            {
                                $args['userphone'] = $data[1];
                                if(\PHPEMSPRO\strings::isCellphone($args['userphone']))
                                {
                                    $u = \PHPEMSPRO\user\model\users::getUserByPhone($args['userphone']);
                                    if(!$u)
                                    {
                                        $args['useremail'] = $data[2];
                                        if(!$data[3])$data[3] = '111111';
                                        $args['userpassword'] = $data[3];
                                        $args['usergroupcode'] = $defaultgroup['groupcode'];
                                        $i = 4;
                                        foreach($tpfields as $p)
                                        {
                                            $args[$p] = iconv("GBK","UTF-8",$data[$i]);
                                            $i++;
                                        }
                                        $args['userstatus'] = 1;
                                        $userid = \PHPEMSPRO\user\model\users::addUser($args);
                                        if(!$userid)
                                        {
                                            $errors[] = $args['username']."数据库添加失败";
                                        }
                                    }
                                    else
                                    {
                                        $errors[] = array($args['username'],"手机号已经存在");
                                    }
                                }
                            }
                            else
                            {
                                $errors[] = array($args['username'],"用户名已经存在");
                            }
                        }
                        else
                        {
                            $errors[] = array($args['username'],"用户名格式错误");
                        }
                    }
                }
                if($errors)
                {
                    $fname = 'public/data/out/errors/'.TIME.'.csv';
                    files::mdir('public/data/out/errors/');
                    files::outCsv($fname,$errors);
                    $message = array(
                        'statusCode' => 200,
                        "message" => "操作成功，部分未成功导入，<a href=\"{$fname}\">点击下载错误名单</a>",
                        "callbackType" => "forward",
                        "forwardUrl" => $fname
                    );
                }
                else
                {
                    $message = array(
                        'statusCode' => 200,
                        "message" => "操作成功",
                        "callbackType" => "forward",
                        "forwardUrl" => "index.php?user-master-users"
                    );
                }
                exit(json_encode($message));
            }
        }
        else
        {
            \PHPEMSPRO\tpl::getInstance()->display('users_import');
        }
    }

    public function add()
    {
        if(\PHPEMSPRO\route::get('adduser'))
        {
            $args = \PHPEMSPRO\route::get('args');
            $u = \PHPEMSPRO\user\model\users::getUserByUsername($args['username']);
            if($u)
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "这个用户名已经存在"
                );
                exit(json_encode($message));
            }
            $u = \PHPEMSPRO\user\model\users::getUserByEmail($args['useremail']);
            if($u)
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "这个邮箱已经被注册了"
                );
                exit(json_encode($message));
            }
            $u = \PHPEMSPRO\user\model\users::getUserByPhone($args['userphone']);
            if($u)
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "这个手机号码已经被注册了"
                );
                exit(json_encode($message));
            }
            $group = \PHPEMSPRO\user\model\users::getGroupByCode($args['usergroupcode']);
            $properties = \PHPEMSPRO\database\model\model::getAllowPropertiesByModelcode($group['groupmodel'],1);
            $args = \PHPEMSPRO\database\model\model::callModelFieldsFilter($args,$properties);
            $args['userstatus'] = 1;
            \PHPEMSPRO\user\model\users::addUser($args);
            $message = array(
                'statusCode' => 200,
                "message" => "注册成功",
                "callbackType" => "forward",
                "forwardUrl" => "index.php?user-master-users"
            );
            exit(json_encode($message));
        }
        else
        {
            $models = \PHPEMSPRO\database\model\model::getModelsByApp('user');
            \PHPEMSPRO\tpl::getInstance()->assign('models',$models);
            \PHPEMSPRO\tpl::getInstance()->display('users_add');
        }
    }

    public function modify()
    {
        $userid = \PHPEMSPRO\route::get('userid');
        $user = \PHPEMSPRO\user\model\users::getUserById($userid);
        $group = \PHPEMSPRO\user\model\users::getGroupByCode($user['usergroupcode']);
        $modelcode = $group['groupmodel'];
        if(\PHPEMSPRO\route::get('modifyuser'))
        {
            $args = \PHPEMSPRO\route::get('args');
            if(!$args['userpassword'])unset($args['userpassword']);
            $properties = \PHPEMSPRO\database\model\model::getAllowPropertiesByModelcode($modelcode,1);
            $args = \PHPEMSPRO\database\model\model::callModelFieldsFilter($args,$properties);
            \PHPEMSPRO\user\model\users::modifyUser($userid,$args);
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
            $properties = \PHPEMSPRO\database\model\model::getAllowPropertiesByModelcode($modelcode,1);
            unset($user['userpassword']);
            $forms = \PHPEMSPRO\html::buildForm($properties,$user);
            \PHPEMSPRO\tpl::getInstance()->assign('user',$user);
            \PHPEMSPRO\tpl::getInstance()->assign('forms',$forms);
            \PHPEMSPRO\tpl::getInstance()->display('users_modify');
        }
    }

    public function del()
    {
        $userid = \PHPEMSPRO\route::get('userid');
        \PHPEMSPRO\user\model\users::delUser($userid);
        $message = array(
            'statusCode' => 200,
            "message" => "删除成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function batdel()
    {
        $delids = \PHPEMSPRO\route::get('delids');
        foreach($delids as $userid => $p)
        {
            \PHPEMSPRO\user\model\users::delUser($userid);
        }
        $message = array(
            'statusCode' => 200,
            "message" => "删除成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function index()
    {
        $page = \PHPEMSPRO\route::get('page');
        $page = $page >= 1?$page:1;
        $args = array();
        if($this->search)
        {
            if($this->search['userid'])$args[] = array('AND',"userid = :userid",'userid',$this->search['userid']);
            if($this->search['username'])$args[] = array('AND',"username LIKE :username",'username','%'.$this->search['username'].'%');
            if($this->search['useremail'])$args[] = array('AND',"useremail  LIKE :useremail",'useremail','%'.$this->search['useremail'].'%');
            if($this->search['userphone'])$args[] = array('AND',"userphone  LIKE :userphone",'userphone','%'.$this->search['userphone'].'%');
            if($this->search['groupcode'])$args[] = array('AND',"usergroupcode = :usergroupcode",'usergroupcode',$this->search['groupcode']);
            if($this->search['stime'] || $this->search['etime'])
            {
                if(!is_array($args))$args = array();
                if($this->search['stime']){
                    $stime = strtotime($this->search['stime']);
                    $args[] = array('AND',"userregtime >= :userregtime",'userregtime',$stime);
                }
                if($this->search['etime']){
                    $etime = strtotime($this->search['etime']);
                    $args[] = array('AND',"userregtime <= :userregtime",'userregtime',$etime);
                }
            }
        }
        $users = \PHPEMSPRO\user\model\users::getUsersList($args,$page);
        $groups = \PHPEMSPRO\user\model\users::getGroups();
        \PHPEMSPRO\tpl::getInstance()->assign('users',$users);
        \PHPEMSPRO\tpl::getInstance()->assign('groups',$groups);
        \PHPEMSPRO\tpl::getInstance()->display('users');
    }
}