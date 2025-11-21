<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\user\controller\mobile;

use PHPEMSPRO\auth;
use PHPEMSPRO\config;
use PHPEMSPRO\user\mobile;
use PHPEMSPRO\user\model\elem;
use PHPEMSPRO\user\model\users;

class verify extends mobile
{
    public function detail()
    {
        //
    }

    public function index()
    {
        $user = $this->_user;
        $group = \PHPEMSPRO\user\model\users::getGroupByCode($user['usergroupcode']);
        $fields = elem::$fields;
        $modelcode = $group['groupmodel'];
        if(\PHPEMSPRO\route::get('modifyuser'))
        {
            if($user['userverify'])
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "您已经通过实名认证！"
                );
                exit(json_encode($message));
            }
            $args = \PHPEMSPRO\route::get('args');
            if(!$args['userpassword'])unset($args['userpassword']);
            $properties = \PHPEMSPRO\database\model\model::getAssignPropertiesByModelcode($modelcode,$fields[$modelcode]);
            $args = \PHPEMSPRO\database\model\model::callModelFieldsFilter($args,$properties);
            \PHPEMSPRO\user\model\users::modifyUser($user['userid'],$args);
            $user = users::getUserById($user['userid']);
            if($user['usergroupcode'] == 'user')
            {
                if(!$user['userpassport'] || !$user['userface'] || !$user['userrealname'])
                {
                    $message = array(
                        'statusCode' => 300,
                        "message" => "姓名、身份证号、照片必须上传"
                    );
                    exit(json_encode($message));
                }
            }
            $message = array(
                'statusCode' => 200,
                "message" => "实名信息已提交，请等待管理员审核！"
            );
            exit(json_encode($message));
        }
        else
        {
            $properties = \PHPEMSPRO\database\model\model::getAssignPropertiesByModelcode($modelcode,$fields[$modelcode]);
            if($user['userstatus'])
            {
                $forms = \PHPEMSPRO\html::buildHtml($properties,$user);
            }
            else
            {
                $forms = \PHPEMSPRO\html::buildForm($properties,$user);
            }
            \PHPEMSPRO\tpl::getInstance()->assign('user',$user);
            \PHPEMSPRO\tpl::getInstance()->assign('forms',$forms);
            \PHPEMSPRO\tpl::getInstance()->display('verify');
        }
    }
}