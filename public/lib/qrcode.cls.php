<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2019/1/14
 * Time: 21:16
 */
namespace PHPEMSPRO;
require_once PEP_PATH."/public/lib/packages/qrcode/qrcode.cls.php";

class qrcode extends \QRcode
{
    static $instance;

    static function getInstance()
    {
        if(self::$instance == NULL)
        {
            self::$instance = new static();//亦可写为 new self();
        }
        return self::$instance;
    }
}

?>