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
				<a class="col-xs-2">
					<div class="text-center">
						<i class="fa fa-option-horizontal"></i>
					</div>
				</a>
			</div>
			<div class="pages-content nofooter" data-nocache="yes">
				<div class="pages-box">
					<div class="page-ele margin mg1 nomgLR">
						<div class="col-xs-9 padding pd4">
                            {x2;if:$point}
							<marquee>上次做到《{x2;$point['pointname']}》第{x2;$index}题</marquee>
                            {x2;else}
							您尚未做题
                            {x2;endif}
						</div>
                        {x2;if:$point}
                        <div class="col-xs-3 padding pd2-5">
							<a href="index.php?exam-mobile-point-paper&pointid={x2;$point['pointid']}" data-page="yes" data-title="{x2;$point['pointname']}" class="btn btn-primary pull-right ajax" style="border-radius: 0.34rem;">继续做题</a>
						</div>
                        {x2;endif}
					</div>
					<div class="page-ele margin mg1 nomgLR padding pd1 nopdLR">
						<ul class="listmenu">
							{x2;tree:$basic['basicsections'],section,sid}
							<li class="small display dflex">
								<a class="ajax" data-page="yes" data-title="{x2;$sections[v:section]['sectionname']}" href="index.php?exam-mobile-point-points&sectionid={x2;v:section}">
									<div class="col-xs-10-9 cflex">
										<div class="title">
											{x2;$sections[v:section]['sectionname']}
										</div>
										<div class="intro">
											进度：{x2;$right[v:section]}/{x2;$numbers[v:section]} 正确率：{x2;$rate[v:section]}%
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
		</div>
    {x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}