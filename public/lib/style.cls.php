<?php

namespace PHPEMSPRO;

use PHPEMSPRO\plugin\model\plugin;

class style
{
    static $pcdefaultcss = array(
        array('css','public/static/css/swiper.min.css'),
        array('css','public/static/css/bootstrap.css'),
        array('less','public/static/css/pep_app.less'),
        array('css','public/static/js/videojs/video-js.css'),
    );
    static $pcdefaultjs = array(
        array('js','public/static/js/less.min.js'),
        array('js','public/static/js/jquery.min.js'),
        array('js','public/static/js/bootstrap.min.js'),
        array('js','public/static/js/swiper.jquery.min.js'),
        array('js','public/static/js/ckeditor/js/MathJax.js?config=TeX-AMS_HTML'),
        array('js','public/static/js/ckeditor/ckeditor.js'),
        array('js','public/static/js/all.fine-uploader.min.js'),
        array('js','public/static/js/pep.app.js'),
        array('js','public/static/js/exam_plugin.js'),
        array('js','public/static/js/videojs/video.min.js'),
        array('js','public/static/js/echarts.min.js')
    );

    static $mobiledefaultcss = array(
        array('css','public/static/css/animations.css'),
        array('css','public/static/css/swiper.min.css'),
        array('css','public/static/css/font-awesome.css'),
        array('less','public/static/css/pep_mobile.less'),
        array('css','public/static/js/videojs/video-js.css'),
    );
    static $mobiledefaultjs = array(
        array('js','public/static/js/less.min.js'),
        array('js','public/static/js/jquery.min.js'),
        array('js','public/static/js/sonic.js'),
        array('js','public/static/js/swiper.jquery.min.js'),
        array('js','public/static/js/all.fine-uploader.min.js'),
        array('js','public/static/js/question.mobile.js'),
        array('js','public/static/js/videojs/video.min.js'),
        array('js','public/static/js/pep.mobile.js'),
        array('js','public/static/js/echarts.min.js')
    );
    static function loadCss()
    {
        if(route::isMobile())
        {
            $css = self::$mobiledefaultcss;
            $css = plugin::getInstance()->filter('afterLoadMobileCss',$css);
        }
        else
        {
            $css = self::$pcdefaultcss;
            $css = plugin::getInstance()->filter('afterLoadPcCss', $css);
        }
        $cssHTML = [];
        foreach($css as $file)
        {
            switch($file[0])
            {
                case 'less':
                    $file[0] = 'stylesheet/less';
                    break;

                default:
                    $file[0] = 'stylesheet';
                    break;
            }
            $cssHTML[] = "<link rel=\"{$file[0]}\" type=\"text/css\" href=\"{$file[1]}\" />";
        }
        return $cssHTML;
    }

    static function loadJs()
    {
        if(route::isMobile())
        {
            $js = self::$mobiledefaultjs;
            $js = plugin::getInstance()->filter('afterLoadMobileJs',$js);
        }
        else
        {
            $js = self::$pcdefaultjs;
            $js = plugin::getInstance()->filter('afterLoadPcJs',$js);
        }
        $jsHTML = [];
        foreach($js as $file)
        {
            switch($file[0])
            {
                case 'module':
                    $file[0] = 'module';
                    break;

                case 'json':
                    $file[0] = 'application/json';
                    break;

                default:
                    $file[0] = 'text/javascript';
                    break;
            }
            $jsHTML[] = "<script type=\"{$file[0]}\" src=\"{$file[1]}\"></script>";
        }
        return $jsHTML;
    }

    static function loadStyle()
    {
        $css = self::loadCss();
        $js = self::loadJs();
        return implode("\n\t",array_merge($css,$js));
    }
}