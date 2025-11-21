<?php

namespace PHPEMSPRO;
error_reporting(0);
class config
{
    const defaultapp = 'core';
    const secretkey = '!#kjkssu2123*4kDHSsaJsd';
	const sslkey = '356d9abc7532ceb0945b615a622c3370';
    const ssliv = "#phpemsproiv*";

    const db = array(
        'default' => array(
            'host' => '127.0.0.1',
            'user' => 'root',
            'pass' => 'root',
            'name' => 'pep',
            'prefix' => 'x2_',
            'intro' => '主库'
        )
    );
    const dataformat = array(
        'default' => '默认',
		'html' => 'html',
        'timestamp' => 'UNIX时间戳',
        'split' => '逗号分隔',
        'json' => 'JSON编码',
        'serialize' => 'PHP序列化',
        'md5' => 'md5加密',
        'base64' => 'base64加密',
        'zipbase64' => '压缩后base64加密'
    );
    const dblog = 0;

    const redis = array(
        'default' => array(
            'host' => '127.0.0.1',
            'pass' => '',
            'name' => '1'
        )
    );

    const aliyunsms = array(
        'accessid' => '11111',
        'accesskey' => '121111',
        'signature' => '1111',
        'regtpl' => 'SMS_111111', //注册短信模板
        'findpasstpl' => 'SMS_111111' //找回密码短信模板
    );

    const webpath = 'http://pro.phpems.net/';
    const webpagenumber = 20;

    const cookieprefix = 'nf_';
    const cookiepath = '/';
    const cookiedomain = '';

    const systimezone = 'Asia/Shanghai';

    const usewechat = false;
    const wxappid = 'wxf';
    const wxsecret = '038305c974bfc0585c';
    const wxmchid = '1510646931';
    const wxkey = '242211oplk';
	const openappid = 'wxf';
    const opensecret = '038305c974bfc0585c';
    const mpappid = '1510646931';
    const mpsecret = '242211oplk';

    const wxlogin = array(
        'autoreg' => true,
        'emdomain' => '@phpems.net'
    );

    const aliappid = '201901111685';
    const alikey = '1111111=';
    const alipub = '11111111';
}