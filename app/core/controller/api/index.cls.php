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
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;

class index extends api
{
    public function enckey()
	{
		$data = array(
		    'select' => false,
            'table' => [
                ['name' => 'users','as' => 'users'],
                ['name' => 'openbasics','type' => 'left join', 'on' => [
                    ['AND','obusername = username']
                ]]
            ]
        );
		$sql = pepdo::getInstance()->makeSelectPro($data);
	}

	public function wordtest()
    {
        $phpWord = new PhpWord();
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment;filename="test.docx"');
        header('Cache-Control: max-age=0');
        $html = file_get_contents('test.txt');
        $section = $phpWord->addSection();
        Html::addHtml($section, $html, false, false);
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save('php://output');
    }
	
	public function qrcode()
    {
        $data = \PHPEMSPRO\route::stripSlashes(urldecode(\PHPEMSPRO\route::get('data')));
        \PHPEMSPRO\qrcode::png($data,false,QR_ECLEVEL_L,7);
    }

    public function sendsms()
    {
        $randcode = \PHPEMSPRO\route::get('randcode');
      	if(!$randcode || $randcode != $_SESSION['randcode'])
        {
          $_SESSION['randcode'] = 0;
          $message = array(
            'statusCode' => 300,
            'message' => '错误的验证码'
          );
          exit(json_encode($message));
        }
		$phonenumber = \PHPEMSPRO\route::get('phonenumber');
        $action = \PHPEMSPRO\route::get('action');
        if(!\PHPEMSPRO\strings::isCellphone($phonenumber))
        {
            $message = array(
                'statusCode' => 300,
                'message' => '错误的手机号码'
            );
            exit(json_encode($message));
        }
        $user = \PHPEMSPRO\user\model\users::getUserByPhone($phonenumber);
        if(!$user && $action != 'reg')
        {
            $message = array(
                'statusCode' => 300,
                'message' => '该手机号未注册'
            );
            exit(json_encode($message));
        }
        if($user && $action == 'reg')
        {
            $message = array(
                'statusCode' => 300,
                'message' => '该手机号已注册'
            );
            exit(json_encode($message));
        }
        if(!$action)$action = 'findpassword';
        $randcode = rand(1000,9999);
        $_SESSION['phonerandcode'] = array(
            $action => $randcode,
            $action.'phonenumber' => $phonenumber
        );
        $params = array ();

        // *** 需用户填写部分 ***
        // fixme 必填：是否启用https
        $security = false;

        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = \PHPEMSPRO\config::aliyunsms['accessid'];
        $accessKeySecret = \PHPEMSPRO\config::aliyunsms['accesskey'];

        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = \PHPEMSPRO\config::aliyunsms['signature'];

        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] = $phonenumber;

        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        //$params["TemplateCode"] = "SMS_0000001";
        if($user)
        {
            if($action == 'findpassword')
            {
                $params["TemplateCode"] = \PHPEMSPRO\config::aliyunsms['findpasstpl'];
            }
        }
        else
        {
            if($action == 'reg')
            {
                $params["TemplateCode"] = \PHPEMSPRO\config::aliyunsms['regtpl'];
            }
        }

        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = Array (
            "code" => $randcode,
            "name" => $phonenumber
        );

        // fixme 可选: 设置发送短信流水号
        //$params['OutId'] = "12345";

        // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
        //$params['SmsUpExtendCode'] = "1234567";


        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $sms = \PHPEMSPRO\sms::getInstance();

        // 此处可能会抛出异常，注意catch
        $content = $sms->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            )),
            $security
        );
        $message = array(
            'statusCode' => 200
        );
        exit(json_encode($message));
    }
	
	public function randcode()
    {
        header("Content-type: image/png");
        $_SESSION['randcode'] = rand(1000,9999);
        \PHPEMSPRO\files::createRandImage($_SESSION['randcode'],100,42);
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