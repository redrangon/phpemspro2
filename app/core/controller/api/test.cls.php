<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\core\controller\api;

use PHPEMSPRO\core\api;
use PHPEMSPRO\pepdo;
use PHPEMSPRO\pedis;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;

class test extends api
{
    public function redis()
    {
        $rs = pedis::getInstance()->searchKeys('process');
        foreach($rs as $r)
        {
            $keys = pedis::getInstance()->getHashKeys($r);
            foreach($keys as $key)
            {
                $data = json_decode(pedis::getInstance()->getHashData($r,$key),true);
				echo $key.':';
				print_r($data);
				echo "\n";
                //if($data['starttime'] + 259200 < TIME)
                //{
                    //pedis::getInstance()->delHashData($r,$key);
                //}
            }
        }
        //$keys = pedis::getInstance()->getHashKeys($r);
    }

    public function test()
    {
        $word = \PHPEMSPRO\word::getInstance()->analy('经济发展和疫情防控保持全球领先地位，国家战略科技力量加快壮大，产业链韧性得到提升。',15);
        print_r($word);
    }

    public function index()
    {
		//
    }
}