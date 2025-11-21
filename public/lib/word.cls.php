<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2019/1/14
 * Time: 21:16
 */
namespace PHPEMSPRO;
require_once PEP_PATH."/public/lib/packages/wordanalysis/phpanalysis.class.php";

class word extends \PhpAnalysis
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

	public function analy($content = "",$number = 10)
	{
		if (empty ( $content ))
		{
            return '';
        }
		//$this->LoadDict ();构造函数中已经调用
		$this->SetSource ($content);
        $this->StartAnalysis ( true );
		$tags = $this->GetFinallyKeywords($number);
		return $tags;
	}    
}

?>