<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\lesson\controller\master;

use PHPEMSPRO\database\model\database;
use PHPEMSPRO\database\model\model;
use PHPEMSPRO\lesson\master;

class lessons extends master
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

    public function catsmenu()
    {
        $catid = \PHPEMSPRO\route::get('catid');
        $categories = \PHPEMSPRO\core\model\category::getAllCategory('lesson');
        $r = array();
        \PHPEMSPRO\core\model\category::levelCategory($r,0,\PHPEMSPRO\core\model\category::$tidyCategory['lesson'],$catid,'index.php?lesson-master-lessons&catid=');
        \PHPEMSPRO\core\model\category::resetCategoryIndex($r);
        echo 'var treeData = '.json_encode($r);
        exit();
    }

    public function delvideo()
    {
        $videoid = \PHPEMSPRO\route::get('videoid');
        \PHPEMSPRO\lesson\model\lessons::delVideo($videoid);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function modifyvideo()
    {
        $videoid = \PHPEMSPRO\route::get('videoid');
        $video = \PHPEMSPRO\lesson\model\lessons::getVideoById($videoid);
        $model = model::getModelByCode($video['videomodelcode']);
        $properties = \PHPEMSPRO\database\model\model::getAllowPropertiesByModelcode($video['videomodelcode'],1);
        if(\PHPEMSPRO\route::get('modifyvideo'))
        {
            $args = \PHPEMSPRO\route::get('args');
            $args = \PHPEMSPRO\database\model\model::callModelFieldsFilter($args,$properties);
            \PHPEMSPRO\lesson\model\lessons::modifyVideo($videoid,$args);
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
            foreach(glob("app/lesson/view/app/video_*.tpl") as $p)
            {
                $tpls[] = substr(basename($p),0,-4);
            }
            $forms = \PHPEMSPRO\html::buildForm($properties,$video);
            $lesson = \PHPEMSPRO\lesson\model\lessons::getLessonById($video['videolesson']);
            \PHPEMSPRO\tpl::getInstance()->assign('lesson',$lesson);
            \PHPEMSPRO\tpl::getInstance()->assign('forms',$forms);
            \PHPEMSPRO\tpl::getInstance()->assign('model',$model);
            \PHPEMSPRO\tpl::getInstance()->assign('video',$video);
            \PHPEMSPRO\tpl::getInstance()->assign('tpls',$tpls);
            \PHPEMSPRO\tpl::getInstance()->display('video_modify');
        }
    }

    public function addvideo()
    {
        $lessonid = \PHPEMSPRO\route::get('lessonid');
        $lesson = \PHPEMSPRO\lesson\model\lessons::getLessonById($lessonid);
        $modelcode = \PHPEMSPRO\route::get('modelcode');
        $model = model::getModelByCode($modelcode);
        if(\PHPEMSPRO\route::get('addvideo'))
        {
            $args = \PHPEMSPRO\route::get('args');
            $args['videoauthor'] = $this->_user['username'];
            $args['videomodelcode'] = $modelcode;
            $args['videolesson'] = $lessonid;
            $properties = \PHPEMSPRO\database\model\model::getAllowPropertiesByModelcode($modelcode,1);
            $args = \PHPEMSPRO\database\model\model::callModelFieldsFilter($args,$properties);
            \PHPEMSPRO\lesson\model\lessons::addVideo($args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "index.php?lesson-master-lessons-videos&lessonid={$lessonid}"
            );
            exit(json_encode($message));
        }
        else
        {
            $tpls = array();
            foreach(glob("app/lesson/view/app/video_*.tpl") as $p)
            {
                $tpls[] = substr(basename($p),0,-4);
            }
            $properties = \PHPEMSPRO\database\model\model::getAllowPropertiesByModelcode($modelcode,1);
            $forms = \PHPEMSPRO\html::buildForm($properties);
            \PHPEMSPRO\tpl::getInstance()->assign('forms',$forms);
            \PHPEMSPRO\tpl::getInstance()->assign('model',$model);
            \PHPEMSPRO\tpl::getInstance()->assign('lesson',$lesson);
            \PHPEMSPRO\tpl::getInstance()->assign('tpls',$tpls);
            \PHPEMSPRO\tpl::getInstance()->display('video_add');
        }
    }

    public function addpage()
    {
        $lessonid = \PHPEMSPRO\route::get('lessonid');
        $models = model::getModelsByApp('lesson');
        $categories = \PHPEMSPRO\core\model\category::getAllCategory('lesson');
        \PHPEMSPRO\tpl::getInstance()->assign('categories',$categories);
        \PHPEMSPRO\tpl::getInstance()->assign('lessonid',$lessonid);
        \PHPEMSPRO\tpl::getInstance()->assign('models',$models);
        \PHPEMSPRO\tpl::getInstance()->display('video_addpage');
    }

    public function ordervideo()
    {
        if(\PHPEMSPRO\route::get('ordervideo'))
        {
            switch(\PHPEMSPRO\route::get('action'))
            {
                case 'order':
                    $ids = \PHPEMSPRO\route::get('ids');
                    foreach($ids as $key => $value)
                    {
                        \PHPEMSPRO\lesson\model\lessons::modifyVideo($key,array('videoorder' => $value));
                    }
                    break;

                case 'delete':
                    $delids = \PHPEMSPRO\route::get('delids');
                    foreach($delids as $key => $id)
                    {
                        \PHPEMSPRO\lesson\model\lessons::delVideo($key);
                    }
                    break;

                default:
                    break;
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

    public function videos()
    {
        $page = intval(\PHPEMSPRO\route::get('page'));
        $page = $page >1?$page:1;
        $lessonid = \PHPEMSPRO\route::get('lessonid');
        $lesson = \PHPEMSPRO\lesson\model\lessons::getLessonById($lessonid);
        $args = array(
            array("AND","videolesson = :videolesson","videolesson",$lessonid)
        );
        $videos = \PHPEMSPRO\lesson\model\lessons::getVideoList($args,$page);
        $models = model::getModelsByApp('lesson');
        \PHPEMSPRO\tpl::getInstance()->assign('models',$models);
        \PHPEMSPRO\tpl::getInstance()->assign('lesson',$lesson);
        \PHPEMSPRO\tpl::getInstance()->assign('videos',$videos);
        \PHPEMSPRO\tpl::getInstance()->assign('page',$page);
        \PHPEMSPRO\tpl::getInstance()->display('video');
    }

    public function del()
    {
        $lessonid = \PHPEMSPRO\route::get('lessonid');
        $number = \PHPEMSPRO\lesson\model\lessons::getVideosNumber($lessonid);
        if($number)
        {
            $message = array(
                'statusCode' => 300,
                "message" => "请先删除此课程下所有课件"
            );
            exit(json_encode($message));
        }
        \PHPEMSPRO\lesson\model\lessons::delLesson($lessonid);
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
        $lessonid = \PHPEMSPRO\route::get('lessonid');
        $lesson = \PHPEMSPRO\lesson\model\lessons::getLessonById($lessonid);
        if(\PHPEMSPRO\route::get('modifylesson'))
        {
            $args = \PHPEMSPRO\route::get('args');
            \PHPEMSPRO\lesson\model\lessons::modifyLesson($lessonid,$args);
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
            \PHPEMSPRO\tpl::getInstance()->assign('lesson',$lesson);
            \PHPEMSPRO\tpl::getInstance()->display('lesson_modify');
        }
    }

    public function add()
    {
        if(\PHPEMSPRO\route::get('addlesson'))
        {
            $args = \PHPEMSPRO\route::get('args');
            $args['lessontime'] = TIME;
            \PHPEMSPRO\lesson\model\lessons::addLesson($args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "index.php?lesson-master-lessons"
            );
            exit(json_encode($message));
        }
        else
        {
            $parentcat = \PHPEMSPRO\core\model\category::getCategoriesByArgs(array(array("AND","catparent = 0"),array("AND","catapp = 'lesson'")));
            \PHPEMSPRO\tpl::getInstance()->assign('parentcat',$parentcat);
            \PHPEMSPRO\tpl::getInstance()->display('lesson_add');
        }
    }

    public function movecategory()
    {
        $lessonids = explode(',',\PHPEMSPRO\route::get('lessonids'));
        $targetcatid = \PHPEMSPRO\route::get('targetcatid');
        if($targetcatid) {
            foreach ($lessonids as $key => $id) {
                if ($id) \PHPEMSPRO\lesson\model\lessons::modifyLesson($id, array('lessoncatid' => $targetcatid));
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
        if(\PHPEMSPRO\route::get('orderlesson'))
        {
            switch(\PHPEMSPRO\route::get('action'))
            {
                case 'order':
                    $ids = \PHPEMSPRO\route::get('ids');
                    foreach($ids as $key => $value)
                    {
                        \PHPEMSPRO\lesson\model\lessons::modifyLesson($key,array('lessonorder' => $value));
                    }
                    break;
                case 'move':
                    $lessonids = array();
                    $ids = \PHPEMSPRO\route::get('delids');
                    foreach($ids as $key => $id)
                    {
                        if($key)$lessonids[] = $key;
                    }
                    $lessonids = implode(',',$lessonids);
                    $parentcat = \PHPEMSPRO\core\model\category::getCategoriesByArgs(array(array("AND","catparent = 0"),array('AND',"catapp = 'lesson'")));
                    \PHPEMSPRO\tpl::getInstance()->assign('parentcat',$parentcat);
                    \PHPEMSPRO\tpl::getInstance()->assign('lessonids',$lessonids);
                    \PHPEMSPRO\tpl::getInstance()->display('lesson_move');
                    exit();
                    break;

                case 'delete':
                    $delids = \PHPEMSPRO\route::get('delids');
                    foreach($delids as $key => $id)
                    {
                        $number = \PHPEMSPRO\lesson\model\lessons::getVideosNumber($key);
                        if($number)
                        {
                            $message = array(
                                'statusCode' => 300,
                                "message" => "操作失败，请删除课程下的所有课件"
                            );
                            exit(json_encode($message));
                        }
                        \PHPEMSPRO\lesson\model\lessons::delLesson($key);
                    }
                    break;

                default:
                    break;
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

    public function removemember()
    {
        $oplid = \PHPEMSPRO\route::get('oplid');
        \PHPEMSPRO\lesson\model\lessons::delLessonMember($oplid);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    public function addmember()
    {
        $lessonid = \PHPEMSPRO\route::get('lessonid');
        $lesson = \PHPEMSPRO\lesson\model\lessons::getLessonById($lessonid);
        if(\PHPEMSPRO\route::get('addmember'))
        {
            $delids = \PHPEMSPRO\route::get('delids');
            $days = \PHPEMSPRO\route::get('days');
            $number = array('new' => 0,'old' => 0);
            foreach($delids as $key => $p)
            {
                $user = \PHPEMSPRO\user\model\users::getUserById($key);
                $r = \PHPEMSPRO\lesson\model\lessons::openLesson($user['username'],$lessonid,$days);
                if($r)
                $number['old']++;
                else
                $number['new']++;
            }
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功，新开通{$number['new']}人，延长时间{$number['old']}人",
                "callbackType" => "forward",
                "forwardUrl" => "reload"
            );
            exit(json_encode($message));
        }
        else
        {
            $page = \PHPEMSPRO\route::get('page');
            $page = $page >= 1?$page:1;
            $args = array();
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
                    $args[] = array('AND',"userregtime >= :stime",'stime',$stime);
                }
                if($this->search['etime']){
                    $etime = strtotime($this->search['etime']);
                    $args[] = array('AND',"userregtime <= :etime",'etime',$etime);
                }
            }
            $users = \PHPEMSPRO\user\model\users::getUsersList($args,$page);
            $groups = \PHPEMSPRO\user\model\users::getGroups();
            \PHPEMSPRO\tpl::getInstance()->assign('lesson',$lesson);
            \PHPEMSPRO\tpl::getInstance()->assign('users',$users);
            \PHPEMSPRO\tpl::getInstance()->assign('groups',$groups);
            \PHPEMSPRO\tpl::getInstance()->display('lesson_addmember');
        }
    }

    public function members()
    {
        $lessonid = \PHPEMSPRO\route::get('lessonid');
        $lesson = \PHPEMSPRO\lesson\model\lessons::getLessonById($lessonid);
        $page = \PHPEMSPRO\route::get('page');
        $page = $page > 0?$page:1;
        $groups = \PHPEMSPRO\user\model\users::getGroups();
        $args = array();
        $args[] = array("AND","opllessonid = :opllessonid","opllessonid",$lessonid);
        if($this->search['stime'])
        {
            $args[] = array("AND",'opltime >= :stime','stime',strtotime($this->search['stime']));
        }
        if($this->search['etime'])
        {
            $args[] = array("AND",'opltime >= :etime','etime',strtotime($this->search['etime']));
        }
        if($this->search['username'])
        {
            $args[] = array("AND",'oplusername LIKE :username','username','%'.$this->search['username'].'%');
        }
        $members = \PHPEMSPRO\lesson\model\lessons::getLessonMemberList($args,$page);
        \PHPEMSPRO\tpl::getInstance()->assign('groups',$groups);
        \PHPEMSPRO\tpl::getInstance()->assign('lesson',$lesson);
        \PHPEMSPRO\tpl::getInstance()->assign('members',$members);
        \PHPEMSPRO\tpl::getInstance()->display('lesson_members');
    }

    public function index()
    {
        $catid = intval(\PHPEMSPRO\route::get('catid'));
        $page = intval(\PHPEMSPRO\route::get('page'));
        $page = $page?$page:1;
        if(!$catid)$catid = $this->search['lessoncatid'];
        $categories = \PHPEMSPRO\core\model\category::getAllCategory('lesson');
        $parentcat = \PHPEMSPRO\core\model\category::getCategoriesByArgs(array(array("AND","catparent = 0"),array("AND","catapp = 'lesson'")));
        if($catid)
        {
            $childstring = \PHPEMSPRO\core\model\category::getChildCategoryString('lesson',$catid);
            $args = array(array("AND","find_in_set(lessoncatid,:lessoncatid)",'lessoncatid',$childstring));
        }
        else $args = array();
        if($this->search['lessonid'])
        {
            $args[] = array("AND","lessonid = :lessonid",'lessonid',$this->search['lessonid']);
        }
        else
        {
            if($this->search['lessoncatid'])$args[] = array("AND","contentcatid = :contentcatid",'lessoncatid',$this->search['lessoncatid']);
            if($this->search['lessonmodelid'])$args[] = array("AND","contentmodelid = :contentmodelid",'lessonmodelid',$this->search['lessonmodelid']);
            if($this->search['stime'])$args[] = array("AND","contenttime >= :scontentinputtime",'scontentinputtime',strtotime($this->search['stime']));
            if($this->search['etime'])$args[] = array("AND","contenttime <= :econtentinputtime",'econtentinputtime',strtotime($this->search['etime']));
            if($this->search['keyword'])$args[] = array("AND","contenttitle LIKE :contenttitle",'lessontitle',"%{$this->search['keyword']}%");
            if($this->search['username'])
            {
                $args[] = array("AND","contentauthor = :contentauthor",'lessonauthor',$this->search['username']);
            }
        }
        $lessons = \PHPEMSPRO\lesson\model\lessons::getLessonList($args,$page);
        $catlevel = 1;
        if($catid)
        {
            $pos = \PHPEMSPRO\core\model\category::getCategoryPos('lesson',$catid);
            if(count($pos))
            {
                $catlevel = count($pos) + 1;
            }
        }
        \PHPEMSPRO\tpl::getInstance()->assign('catlevel',$catlevel);
        \PHPEMSPRO\tpl::getInstance()->assign('catid',$catid);
        \PHPEMSPRO\tpl::getInstance()->assign('lessons',$lessons);
        \PHPEMSPRO\tpl::getInstance()->assign('parentcat',$parentcat);
        \PHPEMSPRO\tpl::getInstance()->assign('categories',$categories);
        \PHPEMSPRO\tpl::getInstance()->assign('page',$page);
        \PHPEMSPRO\tpl::getInstance()->display('lesson');
    }
}