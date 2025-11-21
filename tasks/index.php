<?php

function testok($a = '',$x,$y)
{
	$a = explode(":",$a);
	foreach($a as $an)
	{
		$an = explode(",",$an);
		if($an[0] == $x)return false;
		if($an[1] == $y)return false;
		if(abs($x - $an[0]) == abs($y - $an[1]))return false;
	}
	return true;
}

function initData($x,$y,$number = array())
{
	if(!$number)
	{
		$number = array("{$x},{$y}");
		initData($x,$y++,$number);
	}
	else
	{
		$y++;
		$numbers = array();
		foreach($number as $n)
		{
			for($i=1;$i<=8;$i++)
			{
				if(testok($n,$i,$y))
				{
					$numbers[] = $n.":{$i},{$y}";
				}
			}
		}				
		if($y < 8)
		{
			initData($x,$y++,$numbers);
		}
		else
		{
			print_r($numbers);
			return $numbers;		
		}
	}	
}
for($x = 1;$x<=8;$x++)
{
	$rs = initData($x,1);
	print_r($rs);
}
exit;
$p = array();
if($p)echo "1";
else echo "0";
$p[] = 1;
if($p)echo "1";
else echo "0";
exit;
$time = microtime();
echo $time;
$cmd = shell_exec("python D:/pypjt/main.py e:/1.jpg e:/2.jpg");
print_r($cmd);
$time2 = microtime();
echo $time2;
echo $time2 - $time;
exit;
$a = array(
		'3612' => "D",
		'3613' => "B",
		'3611' => "B",
		'3607' => "A",
		'3609' => "B",
		'3615' => "B",
		'3614' => "C",
		'3610' => "C",
		'3608' => "C",
		'3616' => "B",
		'3621' => "A",
		'3618' => "B",
		'3620' => "A",
		'3625' => "B",
		'3617' => "B",
		'3619' => "B",
		'3626' => "A",
		'3623' => "A",
		'3622' => "A",
		'3624' => "A",
		'3627' => "&lt;p&gt;风险分析&lt;/p&gt;",
		'3630' => "&lt;p&gt;需求分析&lt;/p&gt;",
		'3636' => "&lt;p&gt;中间类软件&lt;/p&gt;",
		'3629' => "&lt;p&gt;相关软件&lt;/p&gt;",
		'3634' => "&lt;p&gt;形式&lt;/p&gt;",
		'3632' => "&lt;p&gt;业务需求&lt;/p&gt;",
		'3635' => "&lt;p&gt;开发者&lt;/p&gt;",
		'3628' => "&lt;p&gt;需求开发&lt;/p&gt;",
		'3633' => "&lt;p&gt;模拟性&lt;/p&gt;",
		'3631' => "&lt;p&gt;分析领域&lt;/p&gt;",
		'3639' => "&lt;p&gt;用户接口原型分类为抛弃式原型和进化型原型。&lt;/p&gt;&lt;p&gt;构建用户接口原型方法有纸上原型化方法、人工模拟原型化方法和自动原型化方法。&lt;/p&gt;",
		'3637' =>"&lt;p&gt;快速圆形的基本思想是快速实现一个实现了若干功能的可运行模型用来启发、揭示和满足用户需求，直至满足用户的全部需求为止。&lt;/p&gt;&lt;p&gt;目的：1.开发和满足用户需求&lt;/p&gt;&lt;p&gt;2.探索开发选项方案&lt;/p&gt;&lt;p&gt;3.实现为最终产品&lt;/p&gt;",
		'3640' =>"&lt;p&gt;描述场景应具有以下特征&lt;/p&gt;&lt;p&gt;1.场景必须是完整的&lt;/p&gt;&lt;p&gt;2.场景是某些用户可以看见的功能，实现了一个具体的系统需求&lt;/p&gt;&lt;p&gt;3.场景总是由被参与者启发的，并向参与者提供可识别的信息&lt;/p&gt;",
		'3638' =>"&lt;p&gt;1.硬数据&lt;/p&gt;&lt;p&gt;2.相关标准&lt;/p&gt;&lt;p&gt;3.法规&lt;/p&gt;&lt;p&gt;4.重要文档&lt;/p&gt;&lt;p&gt;5.相关产品&lt;/p&gt;",
		'4068' =>"&lt;p&gt;首先，先调查教务处，向教务处获取信息；其次，再调查教学院部，向教学院部获取需求；最后向师生角色调查，获取他们对于使用教务管理系统的相关信息。&lt;/p&gt;",
		'4066' =>"&lt;table border=&quot;1&quot; cellpadding=&quot;1&quot; cellspacing=&quot;1&quot; style=&quot;height:200px; width:500px&quot;&gt;	&lt;tbody&gt;		&lt;tr&gt;			&lt;td&gt;输入数据&lt;/td&gt;			&lt;td&gt;有效等价类&lt;/td&gt;			&lt;td&gt;无效等价类&lt;/td&gt;		&lt;/tr&gt;		&lt;tr&gt;			&lt;td&gt;输入字符&lt;/td&gt;			&lt;td&gt;7个字符（1）&lt;/td&gt;			&lt;td&gt;0~6个字符（2）；7个字符以上（3）&lt;/td&gt;		&lt;/tr&gt;		&lt;tr&gt;			&lt;td&gt;首字符类型&lt;/td&gt;			&lt;td&gt;			&lt;p&gt;首字符为&amp;ldquo;豫&amp;rdquo;（4）&lt;/p&gt;			&lt;/td&gt;			&lt;td&gt;首字符不为&amp;ldquo;豫&amp;rdquo;（5）&lt;/td&gt;		&lt;/tr&gt;		&lt;tr&gt;			&lt;td&gt;第2个字符&lt;/td&gt;			&lt;td&gt;第2个字符为&amp;ldquo;E&amp;rdquo;（6）&lt;/td&gt;			&lt;td&gt;第2个字符不为&amp;ldquo;E&amp;rdquo;（7）&lt;/td&gt;		&lt;/tr&gt;		&lt;tr&gt;			&lt;td&gt;第3个字符&lt;/td&gt;			&lt;td&gt;第3个字符为任一字母（8）&lt;/td&gt;			&lt;td&gt;第3个字符不为字母（9）&lt;/td&gt;		&lt;/tr&gt;		&lt;tr&gt;			&lt;td&gt;第4到7个字符&lt;/td&gt;			&lt;td&gt;第4到7个字符为任意数字（10）&lt;/td&gt;			&lt;td&gt;第4到7个字符不为数字（11）&lt;/td&gt;		&lt;/tr&gt;	&lt;/tbody&gt;&lt;/table&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;",
		'4067' =>"&lt;table border=&quot;1&quot; cellpadding=&quot;1&quot; cellspacing=&quot;1&quot; style=&quot;height:200px; width:500px&quot;&gt;	&lt;tbody&gt;		&lt;tr&gt;			&lt;td&gt;豫EA1234&lt;/td&gt;			&lt;td&gt;（1）（4）（6）（8）（10）&lt;/td&gt;			&lt;td&gt;有效等价类&lt;/td&gt;		&lt;/tr&gt;		&lt;tr&gt;			&lt;td&gt;豫EA12&lt;/td&gt;			&lt;td&gt;（2）&lt;/td&gt;			&lt;td&gt;无效等价类&lt;/td&gt;		&lt;/tr&gt;		&lt;tr&gt;			&lt;td&gt;豫EA12345&lt;/td&gt;			&lt;td&gt;（3）&lt;/td&gt;			&lt;td&gt;无效等价类&lt;/td&gt;		&lt;/tr&gt;		&lt;tr&gt;			&lt;td&gt;京EA1234&lt;/td&gt;			&lt;td&gt;（5）&lt;/td&gt;			&lt;td&gt;无效等价类&lt;/td&gt;		&lt;/tr&gt;		&lt;tr&gt;			&lt;td&gt;豫AE1234&lt;/td&gt;			&lt;td&gt;（7）&lt;/td&gt;			&lt;td&gt;无效等价类&lt;/td&gt;		&lt;/tr&gt;		&lt;tr&gt;			&lt;td&gt;豫A11234&lt;/td&gt;			&lt;td&gt;（9）&lt;/td&gt;			&lt;td&gt;无效等价类&lt;/td&gt;		&lt;/tr&gt;		&lt;tr&gt;			&lt;td&gt;豫AEabcd&lt;/td&gt;			&lt;td&gt;（11）&lt;/td&gt;			&lt;td&gt;无效等价类&lt;/td&gt;		&lt;/tr&gt;	&lt;/tbody&gt;&lt;/table&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;",
		'3650' =>"&lt;p&gt;&lt;img alt=&quot;&quot; src=&quot;files/attach/files/content/20211227/16405660843221.png&quot; style=&quot;height:391px; width:677px&quot; /&gt;&lt;/p&gt;",
		'3651' =>"&lt;p&gt;&lt;img alt=&quot;&quot; src=&quot;files/attach/files/content/20211227/16405669385699.png&quot; style=&quot;height:378px; width:750px&quot; /&gt;&lt;/p&gt;",
		'3648' =>"&lt;p&gt;&lt;img alt=&quot;&quot; src=&quot;files/attach/files/content/20211227/16405674937756.png&quot; style=&quot;height:318px; width:729px&quot; /&gt;&lt;/p&gt;",
		'3649' =>"&lt;p&gt;顾客表结构&lt;/p&gt;&lt;table border=&quot;1&quot; cellpadding=&quot;1&quot; cellspacing=&quot;1&quot; style=&quot;height:100px; width:500px&quot;&gt;	&lt;tbody&gt;		&lt;tr&gt;			&lt;td&gt;ID（key）&lt;/td&gt;			&lt;td&gt;姓名&lt;/td&gt;			&lt;td&gt;单位&lt;/td&gt;			&lt;td&gt;家庭住址&lt;/td&gt;			&lt;td&gt;电话号码&lt;/td&gt;		&lt;/tr&gt;		&lt;tr&gt;			&lt;td&gt;&amp;nbsp;&lt;/td&gt;			&lt;td&gt;&amp;nbsp;&lt;/td&gt;			&lt;td&gt;&amp;nbsp;&lt;/td&gt;			&lt;td&gt;&amp;nbsp;&lt;/td&gt;			&lt;td&gt;&amp;nbsp;&lt;/td&gt;		&lt;/tr&gt;	&lt;/tbody&gt;&lt;/table&gt;&lt;p&gt;管理商品表&lt;/p&gt;&lt;table border=&quot;1&quot; cellpadding=&quot;1&quot; cellspacing=&quot;1&quot; style=&quot;height:100px; width:500px&quot;&gt;	&lt;tbody&gt;		&lt;tr&gt;			&lt;td&gt;管理商品名称&lt;/td&gt;			&lt;td&gt;购买人ID（key）&lt;/td&gt;			&lt;td&gt;商品编码（key）&lt;/td&gt;		&lt;/tr&gt;		&lt;tr&gt;			&lt;td&gt;&amp;nbsp;&lt;/td&gt;			&lt;td&gt;&amp;nbsp;&lt;/td&gt;			&lt;td&gt;&amp;nbsp;&lt;/td&gt;		&lt;/tr&gt;	&lt;/tbody&gt;&lt;/table&gt;&lt;p&gt;商品数据库表&lt;/p&gt;&lt;table border=&quot;1&quot; cellpadding=&quot;1&quot; cellspacing=&quot;1&quot; style=&quot;height:100px; width:500px&quot;&gt;	&lt;tbody&gt;		&lt;tr&gt;			&lt;td&gt;商品编码（key）&lt;/td&gt;			&lt;td&gt;商品名称&lt;/td&gt;			&lt;td&gt;单价&lt;/td&gt;		&lt;/tr&gt;		&lt;tr&gt;			&lt;td&gt;&amp;nbsp;&lt;/td&gt;			&lt;td&gt;&amp;nbsp;&lt;/td&gt;			&lt;td&gt;&amp;nbsp;&lt;/td&gt;		&lt;/tr&gt;	&lt;/tbody&gt;&lt;/table&gt;&lt;p&gt;设计依据：因为顾客和商品是多对多的关系，因此采用三个表来管理这个系统的数据库表结构。&lt;/p&gt;"
);
$ser = serialize($a);
echo $ser;
file_put_contents('ser3.txt',$ser);
print_r(unserialize($ser));
$t = file_get_contents('ser2.txt');
echo $t;
print_r(unserialize($t));
exit;
echo html_entity_decode('&lt;p&gt;应中共中央对外联络部邀请，朝鲜劳动党中央政治局委员、中央副委员长、国际部部长李洙墉率朝鲜友好艺术团于1月23日起对中国进行访问演出。&amp;lt;script&amp;gt;&lt;/p&gt;

&lt;p&gt;&amp;nbsp;&lt;/p&gt;

&lt;p&gt;&amp;nbsp;&lt;/p&gt;

&lt;p&gt;&amp;nbsp;&lt;/p&gt;

&lt;p&gt;&amp;nbsp;&lt;/p&gt;');
exit;

$a = array(1 => 'aa','bb','cc');
$b = array(
	1 => 5,
	2 => 4,6
);

usort($a,function($a1,$b1){
	echo $a1;
	echo '<br />';
	echo $b1;
	echo '<br />';
});

?>