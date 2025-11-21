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
					<div class="text-center">{x2;if:$type == 2}正式考试{x2;else}模拟考试{x2;endif}</div>
				</a>
				<a class="col-xs-2">
					<div class="text-center">
						<i class="fa fa-option-horizontal"></i>
					</div>
				</a>
			</div>
			<div class="pages-content{x2;if:!$histories['pages']} nofooter{x2;endif}">
				<div class="pages-box">
					<div class="page-ele margin">
						<ul class="listmenu">
                            {x2;tree:$histories['data'],history,sid}
							<li class="small" style="position: relative">
								<a class="ajax" data-page="yes" data-title="{x2;v:history['ehexam']}" href="index.php?exam-mobile-history-detail&ehid={x2;v:history['ehid']}">
									<div class="col-xs-12">
										<p class="title">
                                            {x2;v:history['ehexam']}
										</p>
										<div class="intro">
											得分：{x2;eval: echo intval(v:history['ehscore'])} &nbsp;&nbsp; 考试时间：{x2;v:history['ehstarttime']}
                                        </div>
									</div>
									{x2;if:v:history['ehispass']}
									<img src="public/static/images/pass.png" class="passlogo"/>
									{x2;else}
									<img src="public/static/images/notpass.png" class="passlogo"/>
									{x2;endif}
								</a>
							</li>
                            {x2;endtree}
						</ul>
					</div>
				</div>
			</div>
			{x2;if:$histories['pages']}
			<div class="pages-footer">
				{x2;$histories['pages']}
			</div>
			{x2;endif}
		</div>
    {x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}