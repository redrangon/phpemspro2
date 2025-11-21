{x2;if:!$_userhash}
{x2;include:header}
<div class="pages">
    {x2;endif}
		<div class="pages-tabs">
			<div class="pages-header">
				<a class="col-xs-2" href="javascript:history.back();"><div class="text-center">
					<i class="fa fa-angle-left"></i>
				</div></a>
				<a class="col-xs-8 active">
					<div class="text-center">{x2;$basic['basic']}</div>
				</a>
				{x2;if:$status['status']}
				<a class="col-xs-2 ajax" href="index.php?exam-mobile">
					<div class="text-center">
						<i class="fa fa-option-horizontal"></i>
					</div>
				</a>
				{x2;else}
				<a class="col-xs-2 ajax" href="index.php?exam-mobile-basic-open">
					<div class="text-center">
						<i class="fa fa-shopping-cart"></i>
					</div>
				</a>
				{x2;endif}
			</div>
			<div class="pages-content" data-refresh="yes" data-nocache="yes">
				<div class="pages-box">
					<div class="page-ele padding pd1">
						<div style="width:7.1rem;height:4rem;" id="exam-charts"></div>
					</div>
					<div class="page-ele margin mg1 nomgLR">
						<div class="col-xs-9 padding pd1">
							{x2;if:$point}
							<marquee class="padding pd3">上次做到《{x2;$point['pointname']}》第{x2;$index}题</marquee>
							{x2;else}
							<div class="padding pd3"> 您尚未做题</div>
							{x2;endif}
						</div>
                        {x2;if:$point}
						<div class="col-xs-3 padding pd2">
							<a href="index.php?exam-mobile-point-paper&pointid={x2;$point['pointid']}" data-page="yes" data-title="{x2;$point['pointname']}" class="btn btn-primary btn-circle pull-right ajax">继续做题</a>
						</div>
                        {x2;endif}
					</div>
				</div>
				<div class="pages-box padding pd0-5 nopdTB">
					<div class="page-ele clear">
						<div class="col-xs-6 padding pd0-5">
							<div class="cards">
								<a href="index.php?exam-mobile-point" class="ajax">
									<div class="col-xs-4 padding pd1">
										<img src="public/static/images/exercise.png" style="width: 100%;"/>
									</div>
									<div class="col-xs-8">
										<div class="title">章节练习</div>
										<div class="time">
											海量题库 快速背题
										</div>
									</div>
								</a>
							</div>
						</div>
						{x2;if:$basic['basicexam']['search']}
						<div class="col-xs-6 padding pd0-5">
							<div class="cards">
								<a href="index.php?exam-mobile-search" class="ajax">
									<div class="col-xs-4 padding pd1">
										<img src="public/static/images/search.png" style="width: 100%;"/>
									</div>
									<div class="col-xs-8">
										<div class="title">试题搜索</div>
										<div class="time">
											试题搜索 快速查询
										</div>
									</div>
								</a>
							</div>
						</div>
						{x2;endif}
						<div class="col-xs-6 padding pd0-5">
							<div class="cards">
								<a href="index.php?exam-mobile-exampaper" class="ajax">
									<div class="col-xs-4 padding pd1">
										<img src="public/static/images/mokao.png" style="width: 100%;"/>
									</div>
									<div class="col-xs-8">
										<div class="title">模拟考试</div>
										<div class="time">
											模拟考试 随时检测
										</div>
									</div>
								</a>
							</div>
						</div>
						<div class="col-xs-6 padding pd0-5">
							<div class="cards">
								<a href="index.php?exam-mobile-history" class="ajax">
									<div class="col-xs-4 padding pd1">
										<img src="public/static/images/history.png" style="width: 100%;"/>
									</div>
									<div class="col-xs-8">
										<div class="title">考试记录</div>
										<div class="time">
											模考记录 温故知新
										</div>
									</div>
								</a>
							</div>
						</div>
						<div class="col-xs-6 padding pd0-5">
							<div class="cards">
								<a href="index.php?exam-mobile-stats" class="ajax">
									<div class="col-xs-4 padding pd1">
										<img src="public/static/images/stats.png" style="width: 100%;"/>
									</div>
									<div class="col-xs-8">
										<div class="title">统计分析</div>
										<div class="time">
											成绩统计 进度分析
										</div>
									</div>
								</a>
							</div>
						</div>
						{x2;if:$basic['basicbook']}
						<div class="col-xs-6 padding pd0-5">
							<div class="cards">
								<a data-page="yes" data-title="考试大纲" href="index.php?content-mobile-category&catid={x2;$basic['basicbook']}" class="ajax">
									<div class="col-xs-4 padding pd1">
										<img src="public/static/images/dagang.png" style="width: 100%;"/>
									</div>
									<div class="col-xs-8">
										<div class="title">考试大纲</div>
										<div class="time">
											教材大纲 电子课件
										</div>
									</div>
								</a>
							</div>
						</div>
						{x2;endif}
						<div class="col-xs-6 padding pd0-5">
							<div class="cards">
								<a href="index.php?exam-mobile-clear" class="ajax">
									<div class="col-xs-4 padding pd1">
										<img src="public/static/images/shanchu.png" style="width: 100%;"/>
									</div>
									<div class="col-xs-8">
										<div class="title">记录删除</div>
										<div class="time">
											清理记录 重新开始
										</div>
									</div>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="pages-footer">
				<a class="col-xs-3 active menu">
					<div class="text-center">
						<span class="fa fa-book-open"></span><br />练习
					</div>
				</a>
				<a class="col-xs-3 menu">
					<div class="text-center">
						<span class="fa fa-bug"></span><br />错题
					</div>
				</a>
				<a class="col-xs-3 menu">
					<div class="text-center">
						<span class="fa fa-star"></span><br />收藏
					</div>
				</a>
				<a class="col-xs-3 menu">
					<div class="text-center">
						<span class="fa fa-pencil"></span><br />笔记
					</div>
				</a>
			</div>
		</div>
		<div class="pages-tabs">
			<div class="pages-header">
				<a class="col-xs-2" href="javascript:history.back();">
					<div class="text-center">
						<i class="fa fa-angle-left"></i>
					</div>
				</a>
				<div class="col-xs-8 active">
					<div class="text-center">{x2;$basic['basic']}</div>
				</div>
                {x2;if:$status['status']}
				<a class="col-xs-2 ajax" href="index.php?exam-mobile">
					<div class="text-center">
						<i class="fa fa-option-horizontal"></i>
					</div>
				</a>
                {x2;else}
				<a class="col-xs-2 ajax" href="index.php?exam-mobile-basic-open">
					<div class="text-center">
						<i class="fa fa-shopping-cart"></i>
					</div>
				</a>
                {x2;endif}
			</div>
			<div class="pages-content">
				<div class="pages-box">
					<div class="page-ele margin">
						<ul class="listmenu">
                            {x2;tree:$basic['basicsections'],section,sid}
							<li class="small display dflex">
								<a class="ajax" data-page="yes" data-title="{x2;$sections[v:section]['sectionname']}" href="index.php?exam-mobile-favor-wrong&sectionid={x2;v:section}">
									<div class="col-xs-10-9 cflex">
										<p class="title">
                                            {x2;$sections[v:section]['sectionname']}
										</p>
										<div class="intro">
											共{x2;$wrong[v:section]}道错题
										</div>
									</div>
									<div class="col-xs-10-1 cflex">
										<i class="fa fa-angle-right ccmiddle"></i>
									</div>
								</a>
							</li>
                            {x2;endtree}
						</ul>
					</div>
				</div>
			</div>
			<div class="pages-footer">
				<a class="col-xs-3 menu">
					<div class="text-center">
						<span class="fa fa-book-open"></span><br />练习
					</div>
				</a>
				<a class="col-xs-3 active menu">
					<div class="text-center">
						<span class="fa fa-bug"></span><br />错题
					</div>
				</a>
				<a class="col-xs-3 menu">
					<div class="text-center">
						<span class="fa fa-star"></span><br />收藏
					</div>
				</a>
				<a class="col-xs-3 menu">
					<div class="text-center">
						<span class="fa fa-pencil"></span><br />笔记
					</div>
				</a>
			</div>
		</div>
		<div class="pages-tabs">
			<div class="pages-header">
				<a class="col-xs-2" href="javascript:history.back();"><div class="text-center">
					<i class="fa fa-angle-left"></i>
				</div></a>
				<a class="col-xs-8 active">
					<div class="text-center">{x2;$basic['basic']}</div>
				</a>
				<a class="col-xs-2">
					<div class="text-center">
						<i class="fa fa-option-horizontal"></i>
					</div>
				</a>
			</div>
			<div class="pages-content">
				<div class="pages-box">
					<div class="page-ele margin">
						<ul class="listmenu">
                            {x2;tree:$basic['basicsections'],section,sid}
							<li class="small display dflex">
								<a class="ajax" data-page="yes" data-title="{x2;$sections[v:section]['sectionname']}" href="index.php?exam-mobile-favor-index&sectionid={x2;v:section}">
									<div class="col-xs-10-9 cflex">
										<p class="title">
                                            {x2;$sections[v:section]['sectionname']}
										</p>
										<div class="intro">
											共收藏{x2;$favor[v:section]}道试题
										</div>
									</div>
									<div class="col-xs-10-1 cflex">
										<i class="fa fa-angle-right ccmiddle"></i>
									</div>
								</a>
							</li>
                            {x2;endtree}
						</ul>
					</div>
				</div>
			</div>
			<div class="pages-footer">
				<a class="col-xs-3 menu">
					<div class="text-center">
						<span class="fa fa-book-open"></span><br />练习
					</div>
				</a>
				<a class="col-xs-3 menu">
					<div class="text-center">
						<span class="fa fa-bug"></span><br />错题
					</div>
				</a>
				<a class="col-xs-3 active menu">
					<div class="text-center">
						<span class="fa fa-star"></span><br />收藏
					</div>
				</a>
				<a class="col-xs-3 menu">
					<div class="text-center">
						<span class="fa fa-pencil"></span><br />笔记
					</div>
				</a>
			</div>
		</div>
		<div class="pages-tabs">
			<div class="pages-header">
				<a class="col-xs-2" href="javascript:history.back();"><div class="text-center">
					<i class="fa fa-angle-left"></i>
				</div></a>
				<a class="col-xs-8 active">
					<div class="text-center">{x2;$basic['basic']}</div>
				</a>
                {x2;if:$status['status']}
				<a class="col-xs-2 ajax" href="index.php?exam-mobile">
					<div class="text-center">
						<i class="fa fa-option-horizontal"></i>
					</div>
				</a>
                {x2;else}
				<a class="col-xs-2 ajax" href="index.php?exam-mobile-basic-open">
					<div class="text-center">
						<i class="fa fa-shopping-cart"></i>
					</div>
				</a>
                {x2;endif}
			</div>
			<div class="pages-content">
				<div class="pages-box">
					<div class="page-ele margin">
						<ul class="listmenu">
                            {x2;tree:$basic['basicsections'],section,sid}
							<li class="small display dflex">
								<a class="ajax" data-page="yes" data-title="{x2;$sections[v:section]['sectionname']}" href="index.php?exam-mobile-favor-note&sectionid={x2;v:section}">
									<div class="col-xs-10-9 cflex">
										<div class="title">
                                            {x2;$sections[v:section]['sectionname']}
										</div>
										<div class="intro">
											共{x2;$note[v:section]}道试题笔记
										</div>
									</div>
									<div class="col-xs-10-1 cflex">
										<i class="fa fa-angle-right ccmiddle"></i>
									</div>
								</a>
							</li>
                            {x2;endtree}
						</ul>
					</div>
				</div>
			</div>
			<div class="pages-footer">
				<a class="col-xs-3 menu">
					<div class="text-center">
						<span class="fa fa-book-open"></span><br />练习
					</div>
				</a>
				<a class="col-xs-3 menu">
					<div class="text-center">
						<span class="fa fa-bug"></span><br />错题
					</div>
				</a>
				<a class="col-xs-3 menu">
					<div class="text-center">
						<span class="fa fa-star"></span><br />收藏
					</div>
				</a>
				<a class="col-xs-3 active menu">
					<div class="text-center">
						<span class="fa fa-pencil"></span><br />笔记
					</div>
				</a>
			</div>
		</div>
		<script>
            $(function(){
                var option = {
                    legend: {
                        orient: 'vertical',
                        x: 'left',
						bottom:0,
						textStyle:{
							fontSize:16
						},
						show:true,
                        data:['正确率','学习进度']
                    },
                    series: [
                        {
                            type:'pie',
                            radius: ['40%', '46%'],
                            avoidLabelOverlap: false,
                            labelLine: {
                                normal: {
                                    show: false
                                }
                            },
                            silent:true,
                            data:[
                                {
                                    value:{x2;$allnumber['right']},
									name:'正确率',
									label: {
                                        position:'center',
                                        formatter:'{a|{x2;if:$allnumber['right']}{x2;eval: echo intval(100*$allnumber['right']/($allnumber['right']+$allnumber['wrong']))}{x2;else}0{x2;endif}%}',
                                        rich:{
                                            a:{
                                                fontSize:'14'
                                            }
                                        }
                                    },
                                    itemStyle:{
                                        color:'#337ab7'
                                    }
								},
                                {
                                    value:{x2;if:$allnumber['right']}{x2;$allnumber['wrong']}{x2;else}1{x2;endif},
									itemStyle:{
                                        color:'#F5F5F5'
                                    }
                                }
                            ]
                        },
                        {
                            type:'pie',
                            radius: ['74%', '80%'],
                            avoidLabelOverlap: false,
                            silent:true,
                            data:[
                                {
                                    value:{x2;eval: echo $allnumber['right']+$allnumber['wrong']},
									name:'学习进度',
									label: {
                                        position:'outside',
                                        align:'center',
                                        verticalAlign:'top',
                                        formatter:'{a|{x2;if:($allnumber['right']+$allnumber['wrong']) > 0}{x2;eval: echo intval(100*($allnumber['right']+$allnumber['wrong'])/$allnumber['all'])}{x2;else}0{x2;endif}%}',
                                        rich:{
                                            a:{
                                                fontSize:'14'
                                            }
                                        }
                                    },
                                    labelLine: {
                                        normal: {
                                            show: true
                                        }
                                    },
                                    itemStyle:{
                                        color:'#02756E'
                                    }
								},
                                {
                                    {x2;eval: v:n = $allnumber['all'] - $allnumber['right'] - $allnumber['wrong']}
                                    value:{x2;if:($allnumber['right'] + $allnumber['wrong']) > 0}{x2;v:n}{x2;else}1{x2;endif},
                                    labelLine: {
                                        normal: {
                                            show: false
                                        }
                                    },
									itemStyle:{
                                        color:'#F5F5F5'
                                    }
                                }
                            ]
                        }
                    ]
                };
                echarts.init($('#exam-charts')[0]).setOption(option);
            });
		</script>
    {x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}