<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\content\controller\master;

use PHPEMSPRO\content\master;
use PHPEMSPRO\content\model\content;
use PHPEMSPRO\database\model\database;
use PHPEMSPRO\database\model\model;

class contents extends master
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
            \PHPEMSPRO\tpl::getInstance()->assign('u',$this->u);
        }
    }

    public function catsmenu()
    {
        $catid = \PHPEMSPRO\route::get('catid');
        $categories = \PHPEMSPRO\core\model\category::getAllCategory('content');
        $r = array();
        \PHPEMSPRO\core\model\category::levelCategory($r,0,\PHPEMSPRO\core\model\category::$tidyCategory['content'],$catid,'index.php?content-master-contents&catid=');
        \PHPEMSPRO\core\model\category::resetCategoryIndex($r);
        echo 'var treeData = '.json_encode($r);
        exit();
    }

    public function movecategory()
    {
        $contentids = explode(',',\PHPEMSPRO\route::get('contentids'));
        $targetcatid = \PHPEMSPRO\route::get('targetcatid');
        if($targetcatid) {
            foreach ($contentids as $key => $id) {
                if ($id) content::modifyContent($id, array('contentcatid' => $targetcatid));
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

    public function order()
    {
        if(\PHPEMSPRO\route::get('ordercontent'))
        {
            switch(\PHPEMSPRO\route::get('action'))
            {
                case 'order':
                    $ids = \PHPEMSPRO\route::get('ids');
                    foreach($ids as $key=>$p)
                    {
                        content::modifyContent($key,array('contentorder' => $p));
                    }
                    break;

                case 'move':
                    $contentids = array();
                    $ids = \PHPEMSPRO\route::get('delids');
                    foreach($ids as $key => $id)
                    {
                        if($key)$contentids[] = $key;
                    }
                    $contentids = implode(',',$contentids);
                    $parentcat = \PHPEMSPRO\core\model\category::getCategoriesByArgs(array(array("AND","catparent = 0"),array('AND',"catapp = 'content'")));
                    \PHPEMSPRO\tpl::getInstance()->assign('parentcat',$parentcat);
                    \PHPEMSPRO\tpl::getInstance()->assign('contentids',$contentids);
                    \PHPEMSPRO\tpl::getInstance()->display('content_move');
                    exit;
                    break;

                case 'delete':
                    $ids = \PHPEMSPRO\route::get('delids');
                    foreach($ids as $key=>$p)
                    {
                        content::delContent($key);
                    }
                    break;

                default:
                    break;
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

    public function modify()
    {
        $contentid = \PHPEMSPRO\route::get('contentid');
        $content = content::getContentById($contentid);
        $model = model::getModelByCode($content['contentmodelcode']);
        $properties = \PHPEMSPRO\database\model\model::getAllowPropertiesByModelcode($content['contentmodelcode'],1);
        if(\PHPEMSPRO\route::get('modifycontent'))
        {
            $args = \PHPEMSPRO\route::get('args');
            $args = \PHPEMSPRO\database\model\model::callModelFieldsFilter($args,$properties);
            content::modifyContent($contentid,$args);
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
            foreach(glob("app/content/view/app/content_*.tpl") as $p)
            {
                $tpls[] = substr(basename($p),0,-4);
            }
            $forms = \PHPEMSPRO\html::buildForm($properties,$content);
            \PHPEMSPRO\tpl::getInstance()->assign('forms',$forms);
            \PHPEMSPRO\tpl::getInstance()->assign('model',$model);
            \PHPEMSPRO\tpl::getInstance()->assign('content',$content);
            \PHPEMSPRO\tpl::getInstance()->assign('tpls',$tpls);
            \PHPEMSPRO\tpl::getInstance()->display('content_modify');
        }
    }

    public function del()
    {
        $contentid = \PHPEMSPRO\route::get('contentid');
        content::delContent($contentid);
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
        $modelcode = \PHPEMSPRO\route::get('modelcode');
        $model = model::getModelByCode($modelcode);
        if(\PHPEMSPRO\route::get('addcontent'))
        {
            $args = \PHPEMSPRO\route::get('args');
            $args['contentauthor'] = $this->_user['username'];
            $args['contentmodelcode'] = $modelcode;
            $properties = \PHPEMSPRO\database\model\model::getAllowPropertiesByModelcode($modelcode,1);
            $args = \PHPEMSPRO\database\model\model::callModelFieldsFilter($args,$properties);
            content::addContent($args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "index.php?content-master-contents"
            );
            exit(json_encode($message));
        }
        else
        {
            $tpls = array();
            foreach(glob("app/content/view/app/content_*.tpl") as $p)
            {
                $tpls[] = substr(basename($p),0,-4);
            }
            $properties = \PHPEMSPRO\database\model\model::getAllowPropertiesByModelcode($modelcode,1);
            $forms = \PHPEMSPRO\html::buildForm($properties);
            \PHPEMSPRO\tpl::getInstance()->assign('forms',$forms);
            \PHPEMSPRO\tpl::getInstance()->assign('model',$model);
            \PHPEMSPRO\tpl::getInstance()->assign('tpls',$tpls);
            \PHPEMSPRO\tpl::getInstance()->display('content_add');
        }
    }

    public function addpage()
    {
        $models = model::getModelsByApp('content');
        $categories = \PHPEMSPRO\core\model\category::getAllCategory('content');
        \PHPEMSPRO\tpl::getInstance()->assign('categories',$categories);
        \PHPEMSPRO\tpl::getInstance()->assign('models',$models);
        \PHPEMSPRO\tpl::getInstance()->display('content_addpage');
    }

    public function index()
    {
        $catid = intval(\PHPEMSPRO\route::get('catid'));
        $page = intval(\PHPEMSPRO\route::get('page'));
        $page = $page?$page:1;
        if(!$catid)$catid = $this->search['contentcatid'];
        $categories = \PHPEMSPRO\core\model\category::getAllCategory('content');
        $parentcat = \PHPEMSPRO\core\model\category::getCategoriesByArgs(array(array("AND","catparent = 0"),array("AND","catapp = 'content'")));
        if($catid)
        {
            $childstring = \PHPEMSPRO\core\model\category::getChildCategoryString('content',$catid);
            $args = array(array("AND","find_in_set(contentcatid,:contentcatid)",'contentcatid',$childstring));
        }
        else $args = array();
        if($this->search['contentid'])
        {
            $args[] = array("AND","contentid = :contentid",'contentid',$this->search['contentid']);
        }
        else
        {
            if($this->search['contentcatid'])$args[] = array("AND","contentcatid = :contentcatid",'contentcatid',$this->search['contentcatid']);
            if($this->search['contentmodelid'])$args[] = array("AND","contentmodelid = :contentmodelid",'contentmodelid',$this->search['contentmodelid']);
            if($this->search['stime'])$args[] = array("AND","contenttime >= :scontentinputtime",'scontentinputtime',strtotime($this->search['stime']));
            if($this->search['etime'])$args[] = array("AND","contenttime <= :econtentinputtime",'econtentinputtime',strtotime($this->search['etime']));
            if($this->search['keyword'])$args[] = array("AND","contenttitle LIKE :contenttitle",'contenttitle',"%{$this->search['keyword']}%");
            if($this->search['username'])
            {
                $args[] = array("AND","contentauthor = :contentauthor",'contentauthor',$this->search['username']);
            }
        }
        $contents = \PHPEMSPRO\content\model\content::getContentList($args,$page);
        $models = model::getModelsByApp('content');
        $catlevel = 1;
        if($catid)
        {
            $pos = \PHPEMSPRO\core\model\category::getCategoryPos('content',$catid);
            if(count($pos))
            {
                $catlevel = count($pos) + 1;
            }
        }
        \PHPEMSPRO\tpl::getInstance()->assign('catlevel',$catlevel);
        \PHPEMSPRO\tpl::getInstance()->assign('models',$models);
        \PHPEMSPRO\tpl::getInstance()->assign('catid',$catid);
        \PHPEMSPRO\tpl::getInstance()->assign('contents',$contents);
        \PHPEMSPRO\tpl::getInstance()->assign('parentcat',$parentcat);
        \PHPEMSPRO\tpl::getInstance()->assign('categories',$categories);
        \PHPEMSPRO\tpl::getInstance()->assign('page',$page);
        \PHPEMSPRO\tpl::getInstance()->display('content');
    }
}