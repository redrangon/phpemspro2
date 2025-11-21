<?php
/*
 * Created on 2016-5-19
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

namespace PHPEMSPRO\document\controller\api;

use PHPEMSPRO\document\api;

class uploader extends api
{
    protected function _start()
    {
        $this->allowexts = array('csv','swf','jpg','png','mp4','pdf');
    }

    public function ckeditor()
	{
        $fn = \PHPEMSPRO\route::get('CKEditorFuncNum');
        $upfile = \PHPEMSPRO\route::getFile('upload');
        $path = 'files/attach/files/content/'.date('Ymd').'/';
        $args['attext'] = \PHPEMSPRO\files::getFileExtName($upfile['name']);
        if(!in_array(strtolower($args['attext']),$this->allowexts))
        {
            $message = '上传失败，附件类型不符!';
			$back = array(
				'error' => array(
					'number' => 105,
					'message' => $message
				)
			);
			exit(json_encode($back));
        }
        if($upfile)
		{
			$fileurl = \PHPEMSPRO\files::uploadFile($upfile,$path,NULL,NULL,$this->allowexts);
		}
        if($fileurl)
        {
            $message = '上传成功!';
            $args = array();
            $args['attpath'] = $fileurl;
            $args['atttitle'] = $upfile['name'];
            $args['attext'] = \PHPEMSPRO\files::getFileExtName($upfile['name']);
            $args['attsize'] = $upfile['size'];
            $args['attuserid'] = $this->_user['sessionuserid'];
            $args['attcntype'] = $upfile['type'];
            $back = array(
				'fileName' => $upfile['name'],
				'url' => $fileurl,
				'uploaded' => 1
			);
        }
        else
        {
            $message = '上传失败，附件类型不符!';
			$back = array(
				'error' => array(
					'number' => 105,
					'message' => $message
				)
			);
        }
        exit(json_encode($back));
	}

    public function fineuploader()
	{
		$args = array();
		$path = 'files/attach/images/content/'.date('Ymd').'/';
		$upfile = \PHPEMSPRO\route::getFile('qqfile');
		$args['attext'] = \PHPEMSPRO\files::getFileExtName($upfile['name']);
		if(!in_array(strtolower($args['attext']),$this->allowexts))
		{
			exit(json_encode(array('status' => 'fail')));
        }
		if($upfile)
		{
			$fileurl = \PHPEMSPRO\files::uploadFile($upfile,$path);
        }
		if($fileurl)
		{
			$args['attpath'] = $fileurl;
			$args['atttitle'] = $upfile['name'];

			$args['attsize'] = $upfile['size'];
			$args['attuserid'] = $this->_user['sessionuserid'];
			$args['attcntype'] = $upfile['type'];
			//$this->attach->addAttach($args);
			if(\PHPEMSPRO\route::get('imgwidth') || \PHPEMSPRO\route::get('imgheight'))
			{
				if(\PHPEMSPRO\files::thumb($fileurl,$fileurl.'.png',\PHPEMSPRO\route::get('imgwidth'),\PHPEMSPRO\route::get('imgheight')))
				$thumb = $fileurl.'.png';
				else
				$thumb = $fileurl;
			}
			else
			$thumb = $fileurl;
			exit(json_encode(array('success' => true,'thumb' => $thumb,'title' => $upfile['name'])));
		}
		else
		{
			exit(json_encode(array('status' => 'fail')));
		}
	}
}


?>
