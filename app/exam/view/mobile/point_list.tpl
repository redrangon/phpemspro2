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
					<div class="page-ele margin">
						<ul class="listmenu">
							{x2;tree:$basic['basicpoints'][$sectionid],point,pid}
							<li class="small display dflex">
								<a class="ajax" href="index.php?exam-mobile-point-paper&pointid={x2;v:point}" data-page="yes" data-title="{x2;$points[v:point]['pointname']}">
									<div class="col-xs-10-9 cflex">
										<div class="title">
											{x2;$points[v:point]['pointname']}
										</div>
										<div class="intro">
											进度：{x2;$right[v:point]}/{x2;$numbers[v:point]} 正确率：{x2;$rate[v:point]}%
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