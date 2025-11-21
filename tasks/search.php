<?php
/*
 * Created on 2013-12-26
 * 用于定时任务调起
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

set_time_limit(0);
define('PEP_PATH',dirname(dirname(__FILE__)));
include PEP_PATH.'/public/lib/base.cls.php';
class app
{
    static function getsearch($dbid,$basicid)
    {
        $data = array(
            'table' => 'search',
            'query' => array(
                array("AND","sdb = :sdb","sdb",$dbid),
                array("AND","sbasicid = :sbasicid","sbasicid",$basicid)
            ),
            'limit' => false
        );
        return \pepdo::getInstance()->getElement($data);
    }

    static function clearsearch()
    {
        $sql = array(
            'sql' => 'TRUNCATE '.config::db['default']['prefix'].'search'
        );
        return \pepdo::getInstance()->exec($sql);
    }

    static function getBasicsByArgs($dbid,$args)
    {
        $data = array(
            'table' => 'basics',
            'query' => $args,
            'limit' => false
        );
        return \pepdo::getInstance($dbid)->getElements($data);
    }

    static function display()
    {
        self::clearsearch();
        foreach(config::db as $db => $info)
        {
            $basics = self::getBasicsByArgs($db,array());
            foreach($basics as $basic)
            {
                $bc = self::getsearch($db,$basic['id']);
                if(!$bc)
                {
                    $subject = \exam\model\points::getSubjectById($basic['basicsubject']);
                    $data = array(
                        'table' => 'search',
                        'query' => array(
                            'sdb' => $db,
                            'sbasicid' => $basic['basicid'],
                            'stitle' => $basic['basic'],
                            'ssubjectid' => $basic['basicsubject'],
                            'strid' => $subject['subjecttrid']
                        )
                    );
                    \pepdo::getInstance()->insertElement($data);
                }
                elseif($bc['stitle'] != $basic['basic'])
                {
                    $data = array(
                        'table' => 'search',
                        'value' => array(
                            'stitle' => $basic['basic']
                        ),
                        'query' => array(
                            array("AND","sid = :sid","sid",$bc['sid'])
                        )
                    );
                    \pepdo::getInstance()->updateElement($data);
                }
            }
        }
        echo 'Search fill finished!';
    }
}
app::display();