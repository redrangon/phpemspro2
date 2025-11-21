<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\wxlogin\model;

class wxlogin
{
    static function addLogin($args)
    {
        \PHPEMSPRO\pedis::getInstance()->setHashData('wxlogin',$args['wxsid'],json_encode($args),300);
    }

    static function delLogin($wxsid)
    {
        \PHPEMSPRO\pedis::getInstance()->delHashData('wxlogin',$wxsid);
    }

    static function getLogin($wxsid)
    {
        return json_decode(\PHPEMSPRO\pedis::getInstance()->getHashData('wxlogin',$wxsid),true);
    }
}