<?php
/*
 * Created on 2014-12-10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
namespace PHPEMSPRO;

class face
{
		static function verify($face1,$face2,$type)
		{
			$result = false;
			switch($type)
			{
				case 'baidu':
					$data = array(
						array('image' => files::base64Img($face1,false),'image_type' => 'BASE64'),
						array('image' => files::base64Img($face2,false),'image_type' => 'BASE64')
					);
					$score = baidu::getInstance()->verifyface($data);
					if($score >= 80)$result = true;
					break;

				case 'local':
					$face1 = PEP_PATH."/".$face1;
					$face2 = PEP_PATH."/".$face2;
					$cmd = trim(shell_exec("python D:/pypjt/main.py {$face1} {$face2}"));
					$cmd = json_decode($cmd,true);
					$score = $cmd['score'];
					if($score >= 0.8)$result = true;
					break;

				default:
					break;
			}
			return $result;
		}
}
?>
