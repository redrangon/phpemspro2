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
					<div class="text-center">考试记录</div>
				</a>
				<a class="col-xs-2">
					<div class="text-center">
						<i class="fa fa-option-horizontal"></i>
					</div>
				</a>
			</div>
			<div class="pages-content nofooter">
				<div class="pages-box">
					<div class="page-ele margin mg1 nomgLR">
						<ul class="listmenu">
							<li class="small display dflex">
								<a class="ajax" href="index.php?exam-mobile-history-history">
									<div class="title">模拟考试</div>
									<div class="intro">
										模拟考试考试记录、阅卷
									</div>
								</a>
							</li>
							<li class="small display dflex">
								<a class="ajax" href="index.php?exam-mobile-history-history&type=2">
									<div class="title">正式考试</div>
									<div class="intro">
										正式考试考试记录、阅卷
									</div>
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
    {x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}