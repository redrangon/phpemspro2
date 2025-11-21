<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\lesson\controller\master;

use PHPEMSPRO\lesson\master;

class category extends master
{public function order()
    {
        $ids = \PHPEMSPRO\route::get('ids');
        foreach($ids as $key=>$p)
        {
            \PHPEMSPRO\core\model\category::modifyCategory($key,array('catorder' => $p));
        }
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function modify()
    {
        $catid = \PHPEMSPRO\route::get('catid');
        $category = \PHPEMSPRO\core\model\category::getCategoryById($catid);
        if(\PHPEMSPRO\route::get('modifycategory'))
        {
            $args = \PHPEMSPRO\route::get('args');
            \PHPEMSPRO\core\model\category::modifyCategory($catid,$args);
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
            $tpls = array();
            foreach(glob("app/content/view/app/category_*.tpl") as $p)
            {
                $tpls[] = substr(basename($p),0,-4);
            }
            \PHPEMSPRO\tpl::getInstance()->assign('category',$category);
            \PHPEMSPRO\tpl::getInstance()->assign('tpls',$tpls);
            \PHPEMSPRO\tpl::getInstance()->display('category_modify');
        }
    }

    public function del()
    {
        $catid = \PHPEMSPRO\route::get('catid');
        $catchild = \PHPEMSPRO\core\model\category::getChildCategoryString('lesson',$catid,false);
        if($catchild)
        {
            $message = array(
                'statusCode' => 300,
                "message" => "操作失败，请先删除本分类下的子分类"
            );
            exit(json_encode($message));
        }
        \PHPEMSPRO\core\model\category::delCategory($catid);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function add()
    {
        if(\PHPEMSPRO\route::get('addcategory'))
        {
            $args = \PHPEMSPRO\route::get('args');
            $args['catapp'] = 'lesson';
            \PHPEMSPRO\core\model\category::addCategory($args);
            if($args['catparent'])
            {
                $parent = \PHPEMSPRO\core\model\category::getCategoryById($args['catparent']);
                $parent = intval($parent['catparent']);
            }
            else $parent = 0;
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "index.php?lesson-master-category&parent={$parent}"
            );
            exit(json_encode($message));
        }
        else
        {
            $parent = \PHPEMSPRO\route::get('parent');
            $tpls = array();
            foreach(glob("app/lesson/view/app/category_*.tpl") as $p)
            {
                $tpls[] = substr(basename($p),0,-4);
            }
            \PHPEMSPRO\tpl::getInstance()->assign('parent',\PHPEMSPRO\core\model\category::getCategoryById($parent));
            \PHPEMSPRO\tpl::getInstance()->assign('tpls',$tpls);
            \PHPEMSPRO\tpl::getInstance()->display('category_add');
        }
    }

    public function index()
    {
        $page = intval(\PHPEMSPRO\route::get('page'));
        $page = $page?$page:1;
        $parent = intval(\PHPEMSPRO\route::get('parent'));
        $args = array();
        $args[] = array("AND","catparent = :catparent",'catparent',$parent);
        $args[] = array("AND","catapp = :catapp",'catapp','lesson');
        $categorys = \PHPEMSPRO\core\model\category::getCategoryList($args,$page);
        $categories = \PHPEMSPRO\core\model\category::getAllCategory('lesson');
        \PHPEMSPRO\tpl::getInstance()->assign('parent',$parent);
        \PHPEMSPRO\tpl::getInstance()->assign('categorys',$categorys);
        \PHPEMSPRO\tpl::getInstance()->assign('categories',$categories);
        \PHPEMSPRO\tpl::getInstance()->assign('page',$page);
        \PHPEMSPRO\tpl::getInstance()->display('category');
    }
}