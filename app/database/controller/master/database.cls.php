<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\database\controller\master;

use PHPEMSPRO\database\master;

class database extends master
{public function setting()
    {
        $dbid = \PHPEMSPRO\route::get('dbid');
        $table = \PHPEMSPRO\route::get('table');
        $field = \PHPEMSPRO\route::get('field');
        if(\PHPEMSPRO\route::get('setfield'))
        {
            $args = \PHPEMSPRO\route::get('args');
            \PHPEMSPRO\database\model\database::setDbProperty($dbid,$table,$field,$args);
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
            $intro = \PHPEMSPRO\database\model\database::getFieldIntro($dbid,$table,$field);
            if(!$intro['dbformat'])
            {
                $intro['dbformat'] = 'default';
            }
            \PHPEMSPRO\tpl::getInstance()->assign('dbid',$dbid);
            \PHPEMSPRO\tpl::getInstance()->assign('table',$table);
            \PHPEMSPRO\tpl::getInstance()->assign('field',$field);
            \PHPEMSPRO\tpl::getInstance()->assign('formats',\PHPEMSPRO\config::dataformat);
            \PHPEMSPRO\tpl::getInstance()->assign('intro',$intro);
            \PHPEMSPRO\tpl::getInstance()->display('database_setting');
        }
    }

    public function intro()
    {
        $dbid = \PHPEMSPRO\route::get('dbid');
        $table = \PHPEMSPRO\route::get('table');
        if(\PHPEMSPRO\route::get('settable'))
        {
            $args = \PHPEMSPRO\route::get('args');
            \PHPEMSPRO\database\model\database::setDbProperty($dbid,$table,null,$args);
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
            $intro = \PHPEMSPRO\database\model\database::getTableIntro($dbid,$table,null);
            \PHPEMSPRO\tpl::getInstance()->assign('dbid',$dbid);
            \PHPEMSPRO\tpl::getInstance()->assign('table',$table);
            \PHPEMSPRO\tpl::getInstance()->assign('intro',$intro);
            \PHPEMSPRO\tpl::getInstance()->display('database_intro');
        }
    }

    public function fields()
    {
        $dbid = \PHPEMSPRO\route::get('dbid');
        $table = \PHPEMSPRO\route::get('table');
        if(\PHPEMSPRO\config::db[$dbid])
        {
            $fields = \PHPEMSPRO\database\model\database::getDatabaseFields($dbid,$table);
        }
        else
        {
            $fields = null;
        }
        $intros = \PHPEMSPRO\database\model\database::getFieldsIntros($dbid,$table);
        \PHPEMSPRO\tpl::getInstance()->assign('dbid',$dbid);
        \PHPEMSPRO\tpl::getInstance()->assign('table',$table);
        \PHPEMSPRO\tpl::getInstance()->assign('fields',$fields);
        \PHPEMSPRO\tpl::getInstance()->assign('intros',$intros);
        \PHPEMSPRO\tpl::getInstance()->display('database_fields');
    }

    public function tables()
    {
        $dbid = \PHPEMSPRO\route::get('dbid');
        if(\PHPEMSPRO\config::db[$dbid])
        {
            $tables = \PHPEMSPRO\database\model\database::getDatabaseTables($dbid);
        }
        else
        {
            $tables = null;
        }
        $intros = \PHPEMSPRO\database\model\database::getTableIntros($dbid);
        \PHPEMSPRO\tpl::getInstance()->assign('dbid',$dbid);
        \PHPEMSPRO\tpl::getInstance()->assign('tables',$tables);
        \PHPEMSPRO\tpl::getInstance()->assign('intros',$intros);
        \PHPEMSPRO\tpl::getInstance()->display('database_tables');
    }

    public function synch()
    {
        $dbid = \PHPEMSPRO\route::get('dbid');
        $table = \PHPEMSPRO\route::get('table');
        $intro = \PHPEMSPRO\database\model\database::getTableIntro($dbid,$table);
        \PHPEMSPRO\tpl::getInstance()->assign('dbid',$dbid);
        \PHPEMSPRO\tpl::getInstance()->assign('table',$table);
        \PHPEMSPRO\tpl::getInstance()->assign('intro',$intro);
        \PHPEMSPRO\tpl::getInstance()->display('database_synch');
    }
	
	public function synchdb()
    {
        $dbid = \PHPEMSPRO\route::get('dbid');
        if(\PHPEMSPRO\config::db[$dbid])
        {
            $tables = \PHPEMSPRO\database\model\database::getDatabaseTables($dbid);
			foreach($tables as $table)
			{
				$table = current($table);
				\PHPEMSPRO\database\model\database::synchfields($dbid,$table);
			}
        }        
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function synchfields()
    {
        $dbid = \PHPEMSPRO\route::get('dbid');
        $table = \PHPEMSPRO\route::get('table');
        \PHPEMSPRO\database\model\database::synchfields($dbid,$table);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function synchdata()
    {
        $dbid = \PHPEMSPRO\route::get('dbid');
        $table = \PHPEMSPRO\route::get('table');
        \PHPEMSPRO\database\model\database::synchdata($dbid,$table);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function index()
    {
        \PHPEMSPRO\tpl::getInstance()->assign('databases',\PHPEMSPRO\config::db);
        \PHPEMSPRO\tpl::getInstance()->display('database');
    }
}