<?php
/*
 * Created on 2013-12-26
 * 用于定时任务调起
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

if(php_sapi_name() != 'cli')exit('Access denied!');
set_time_limit(0);
define('PEP_PATH',dirname(dirname(__FILE__)));
include PEP_PATH.'/public/lib/base.cls.php';
class app
{
    static function display()
    {
        $sourcedb = 'demo';
        $targetdb = 'pep';
        $number = 100;
        $tmps = \PHPEMSPRO\database\model\database::getDatabaseTables($targetdb);
        $tables = array();
        foreach($tmps as $table)
        {
            $table = current($table);
            $tables[] = str_replace(\PHPEMSPRO\config::db[$targetdb]['prefix'],'',$table);
        }
        foreach($tables as $table)
        {
            $index = 0;
            while(1)
            {
                $limit = array($number * $index,$number);
                $data = array(false,$table,array(),false,false,$limit);
                $sql = \PHPEMSPRO\pdosql::getInstance($sourcedb)->makeSelect($data);
                $rs = \PHPEMSPRO\pepdo::getInstance($sourcedb)->fetchAll($sql);
                if($rs)
                {
                    foreach($rs as $r)
                    {
                        $data = array($table,$r);
                        $sql = \PHPEMSPRO\pdosql::getInstance($targetdb)->makeInsert($data,0,0);
                        $rs = \PHPEMSPRO\pepdo::getInstance($targetdb)->exec($sql);
                    }
                }
                else
                {
                    break;
                }
                $index++;
            }
            echo "Table {$table} Transfer Finished!\n";
        }
        echo 'User Data Transfer Finished!';
    }
}
app::display();