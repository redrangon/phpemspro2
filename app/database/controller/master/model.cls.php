<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 18:37
 */

namespace PHPEMSPRO\database\controller\master;
use PHPEMSPRO\core\app;
use PHPEMSPRO\core\model\apps;
use PHPEMSPRO\database\master;

class model extends master
{public function gettables()
    {
        $dbid = \PHPEMSPRO\route::get('dbid');
        $tables = \PHPEMSPRO\database\model\database::getDatabaseTables($dbid);
        foreach($tables as $p)
        {
            $table = current($p);
            echo '<option value="'.$table.'">'.$table.'</option>';
        }
    }

    public function getfields()
    {
        $dbid = \PHPEMSPRO\route::get('dbid');
        $table = \PHPEMSPRO\route::get('table');
        $fields = \PHPEMSPRO\database\model\database::getDatabaseFields($dbid,$table);
        foreach($fields as $p)
        {
            echo '<option value="'.$p['Field'].'">'.$p['Field'].'</option>';
        }
    }

    public function copymodel()
    {
        $modelcode = \PHPEMSPRO\route::get('modelcode');
        $model = \PHPEMSPRO\database\model\model::getModelByCode($modelcode);
        if(\PHPEMSPRO\route::get('copymodel'))
        {
            $args = \PHPEMSPRO\route::get('args');
            if(\PHPEMSPRO\database\model\model::getModelByCode($args['modelcode']))
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "操作失败，同名模型存在"
                );
                exit(json_encode($message));
            }
            else
            {
                $args['modeldb'] = $model['modeldb'];
                $args['modeltable'] = $model['modeltable'];
                $args['modelapp'] = $model['modelapp'];
                \PHPEMSPRO\database\model\model::addModel($args);
                $properties = \PHPEMSPRO\database\model\model::getAllPropertyiesByModelcode($modelcode);
                foreach($properties as $property)
                {
                    $property['ppymodel'] = $args['modelcode'];
                    \PHPEMSPRO\database\model\model::addProperty($property);
                }
                $message = array(
                    'statusCode' => 200,
                    "message" => "操作成功",
                    "callbackType" => "forward",
                    "forwardUrl" => "index.php?database-master-model"
                );
                exit(json_encode($message));
            }
        }
        else
        {
            \PHPEMSPRO\tpl::getInstance()->assign('model',$model);
            \PHPEMSPRO\tpl::getInstance()->display('model_copy');
        }
    }

    public function add()
    {
        if(\PHPEMSPRO\route::get('addmodel'))
        {
            $args = \PHPEMSPRO\route::get('args');
            if(\PHPEMSPRO\database\model\model::getModelByCode($args['modelcode']))
            {
                $message = array(
                    'statusCode' => 300,
                    "message" => "操作失败，同名模型存在"
                );
                exit(json_encode($message));
            }
            else
            {
                \PHPEMSPRO\database\model\model::addModel($args);
                $message = array(
                    'statusCode' => 200,
                    "message" => "操作成功",
                    "callbackType" => "forward",
                    "forwardUrl" => "index.php?database-master-model"
                );
                exit(json_encode($message));
            }
        }
        else
        {
            \PHPEMSPRO\tpl::getInstance()->assign('databases',\PHPEMSPRO\config::db);
            \PHPEMSPRO\tpl::getInstance()->display('model_add');
        }
    }

    public function addproperty()
    {
        $modelcode = \PHPEMSPRO\route::get('modelcode');
        $model = \PHPEMSPRO\database\model\model::getModelByCode($modelcode);
        if(\PHPEMSPRO\route::get('addproperty'))
        {
            $args = \PHPEMSPRO\route::get('args');
            $args['ppymodel'] = $modelcode;
            \PHPEMSPRO\database\model\model::addProperty($args);

            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "index.php?database-master-model-properties&modelcode={$modelcode}"
            );
            exit(json_encode($message));
        }
        else
        {
            $properties = \PHPEMSPRO\database\model\model::getAllPropertyiesByModelcode($modelcode);
            $dbid = $model['modeldb'];
            $table = $model['modeltable'];
            $fields = \PHPEMSPRO\database\model\database::getDatabaseFields($dbid,$table);
            $usedfields = array();
            foreach($properties as $p)
            {
                $usedfields[] = $p['ppyfield'];
            }
            \PHPEMSPRO\tpl::getInstance()->assign('usedfields',$usedfields);
            \PHPEMSPRO\tpl::getInstance()->assign('fields',$fields);
            \PHPEMSPRO\tpl::getInstance()->assign('model',$model);
            \PHPEMSPRO\tpl::getInstance()->display('model_addproperty');
        }
    }

    public function del()
    {
        $modelcode = \PHPEMSPRO\route::get('modelcode');
        $properties = \PHPEMSPRO\database\model\model::getAllPropertyiesByModelcode($modelcode);
        if($properties)
        {
            $message = array(
                'statusCode' => 300,
                "message" => "操作失败，请先删除该模型下的属性"
            );
            exit(json_encode($message));
        }
        \PHPEMSPRO\database\model\model::delModel($modelcode);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function delproperty()
    {
        $ppyid = \PHPEMSPRO\route::get('ppyid');
        \PHPEMSPRO\database\model\model::delProperty($ppyid);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function modifyproperty()
    {
        $ppyid = \PHPEMSPRO\route::get('ppyid');
        $property = \PHPEMSPRO\database\model\model::getPropertyById($ppyid);
        if(\PHPEMSPRO\route::get('modifyproperty'))
        {
            $args = \PHPEMSPRO\route::get('args');
            if(!$args['ppyaccess'])$args['ppyaccess'] = array();
            \PHPEMSPRO\database\model\model::modifyProperty($ppyid,$args);

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
            $model = \PHPEMSPRO\database\model\model::getModelByCode($property['ppymodel']);
            $properties = \PHPEMSPRO\database\model\model::getAllPropertyiesByModelcode($model['modelcode']);
            $dbid = $model['modeldb'];
            $table = $model['modeltable'];
            $fields = \PHPEMSPRO\database\model\database::getDatabaseFields($dbid,$table);
            $usedfields = array();
            foreach($properties as $p)
            {
                if($ppyid != $p['ppyid'])
                $usedfields[] = $p['ppyfield'];
            }
            \PHPEMSPRO\tpl::getInstance()->assign('usedfields',$usedfields);
            \PHPEMSPRO\tpl::getInstance()->assign('fields',$fields);
            \PHPEMSPRO\tpl::getInstance()->assign('model',$model);
            \PHPEMSPRO\tpl::getInstance()->assign('property',$property);
            \PHPEMSPRO\tpl::getInstance()->display('model_modifyproperty');
        }
    }

    public function modify()
    {
        $modelcode = \PHPEMSPRO\route::get('modelcode');
        $model = \PHPEMSPRO\database\model\model::getModelByCode($modelcode);
        if(\PHPEMSPRO\route::get('modifymodel'))
        {
            $args = \PHPEMSPRO\route::get('args');
            \PHPEMSPRO\database\model\model::modifyModel($modelcode,$args);

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
            $tables = \PHPEMSPRO\database\model\database::getDatabaseTables($model['modeldb']);
            \PHPEMSPRO\tpl::getInstance()->assign('databases',\PHPEMSPRO\config::db);
            \PHPEMSPRO\tpl::getInstance()->assign('tables',$tables);
            \PHPEMSPRO\tpl::getInstance()->assign('model',$model);
            \PHPEMSPRO\tpl::getInstance()->display('model_modify');
        }
    }

    public function properties()
    {
        $modelcode = \PHPEMSPRO\route::get('modelcode');
        $page = \PHPEMSPRO\route::get('page');
        $model = \PHPEMSPRO\database\model\model::getModelByCode($modelcode);
        $properties = \PHPEMSPRO\database\model\model::getAllPropertyiesByModelcode($modelcode);
        \PHPEMSPRO\tpl::getInstance()->assign('page',$page);
        \PHPEMSPRO\tpl::getInstance()->assign('model',$model);
        \PHPEMSPRO\tpl::getInstance()->assign('properties',$properties);
        \PHPEMSPRO\tpl::getInstance()->display('model_properties');
    }

    public function preview()
    {
        $modelcode = \PHPEMSPRO\route::get('modelcode');
        $model = \PHPEMSPRO\database\model\model::getModelByCode($modelcode);
        $properties = \PHPEMSPRO\database\model\model::getPropertyiesByModelcode($modelcode);
        $forms = \PHPEMSPRO\html::buildForm($properties);
        \PHPEMSPRO\tpl::getInstance()->assign('model',$model);
        \PHPEMSPRO\tpl::getInstance()->assign('forms',$forms);
        \PHPEMSPRO\tpl::getInstance()->display('model_preview');
    }

    public function orders()
    {
        if(\PHPEMSPRO\route::get('orderproperty'))
        {
            $ids = \PHPEMSPRO\route::get('ids');
            foreach($ids as $key => $p)
            {
                \PHPEMSPRO\database\model\model::modifyProperty($key,array('ppyorder' => $p));
            }
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "reload"
            );
            exit(json_encode($message));
        }
    }

    public function index()
    {
        $page = \PHPEMSPRO\route::get('page');
        if($page < 1)$page = 1;
        $models = \PHPEMSPRO\database\model\model::getModels($page);
        \PHPEMSPRO\tpl::getInstance()->assign('page',$page);
        \PHPEMSPRO\tpl::getInstance()->assign('models',$models);
        \PHPEMSPRO\tpl::getInstance()->display('model');
    }
}