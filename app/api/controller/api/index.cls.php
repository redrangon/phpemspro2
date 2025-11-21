<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\api\controller\api;

use PHPEMSPRO\api\api;

class index extends api
{
    public function version()
    {
        $message = array(
            "statusCode" => 200,
            "data" => array(
                "version" => 2.0,
                "name" => "PHPEMSPRO",
                "company" => "新乡市落笔千言网络技术有限公司"
            )
        );
        self::json($message);
    }

    public function user()
    {
        $fields = array('userpassword','useropenid');
        if($this->_user['userid'])
        {
            $data = $this->_user;
            foreach($fields as $field)
            {
                unset($data[$field]);
            }
        }
        else
        {
            $data = array(
                'userid' => 0
            );
        }
        $message = array(
            "statusCode" => 200,
            "data" => $data
        );
        self::json($message);
    }

    public function index()
    {
        $message = array(
            "statusCode" => 200,
            "data" => $_SERVER?$_SERVER:array()
        );
        self::json($message);
    }
}