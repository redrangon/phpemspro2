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
				<div class="text-center">模拟考试</div>
			</a>
			<a class="col-xs-2">
				<div class="text-center">
					<i class="fa fa-option-horizontal"></i>
				</div>
			</a>
		</div>
		<div class="pages-content nofooter" data-nocache="yes">
			<div class="pages-box">
                {x2;if:$papers}
				<div class="page-ele margin mg1 nomgLR">
					<ul class="listmenu">
						{x2;tree:$papers,paper,pid}
						<li class="small display dflex">
							{x2;if:$examsessions[v:paper['paperid']]}
							<a class="ajax" data-page="yes" data-title="{x2;v:paper['papername']}" href="index.php?exam-mobile-exampaper-recover&paperid={x2;$examsessions[v:paper['paperid']]['esid']}">
							{x2;else}
							<a class="ajax" data-page="yes" data-title="{x2;v:paper['papername']}" href="index.php?exam-mobile-exampaper-selectquestions&paperid={x2;v:paper['paperid']}">
							{x2;endif}
								<div class="col-xs-9 cflex">
									<div class="title">{x2;substring:v:paper['papername'],63}</div>
									<div class="intro">
										总分：{x2;v:paper['papersetting']['score']}分 时间：{x2;v:paper['papersetting']['papertime']}分钟
									</div>
								</div>
								<div class="col-xs-3 text-center cflex bg">
									<span class="ccmiddle" style="width: 100%;">{x2;if:$examsessions[v:paper['paperid']]}继续作题{x2;else}开始考试{x2;endif}</span>
								</div>
							</a>
							<a class="btn btn-primary hide ajax" data-page="yes" data-title="{x2;v:paper['papername']}" href="index.php?exam-mobile-exampaper-selectquestions&paperid={x2;v:paper['paperid']}">重做</a>
						</li>
						{x2;endtree}
					</ul>
				</div>
				{x2;else}
				<div class="page-ele margin">
					<ul class="listmenu">
						<li>目前没有试卷</li>
					</ul>
				</div>
                {x2;endif}
			</div>
		</div>
	</div>
    {x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}