<?php
/*
 * Created on 2013-12-26
 * 用于定时任务调起
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

set_time_limit(0);
define('PEP_PATH',dirname(dirname(__FILE__)));
include PEP_PATH.'/public/lib/base.cls.php';
class app
{
    static function display()
    {
        $questions = \exam\model\question::getQuestionsByArgs('demo',array());
        print_r($questions);
        foreach($questions as $question)
        {
            $keywords = word::getInstance()->analy($question['question'].$question['questionselect'],30);
            \exam\model\question::modifyQuestion('demo',$question['questionid'],array('questiontags' => $keywords));
        }
        echo 'Process fill finished!';
    }
}
app::display();