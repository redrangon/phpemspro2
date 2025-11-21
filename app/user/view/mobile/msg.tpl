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
				<div class="text-center">站内信</div>
			</a>
			<a class="col-xs-2">
				<div class="text-center">
					<i class="fa fa-option-horizontal"></i>
				</div>
			</a>
		</div>
		<div class="pages-content{x2;if:!$msgs['pages']} nofooter{x2;endif}" data-nocache="yes">
			<div class="pages-box">
				{x2;tree:$msgs['data'],msg,mid}
				<a href="index.php?user-mobile-msg-detail&msrid={x2;v:msg['msrid']}" class="ajax">
					<div class="page-ele contin">
						<ul class="orders">
							<li class="title">
								{x2;v:msg['msgposter']}
								<span class="pull-right">
									{x2;if:v:msg['msrstatus']}
									已读
									{x2;else}
									未读
									{x2;endif}
								</span>
							</li>
							<li class="news padding pd2">
								{x2;v:msg['msgtitle']}
							</li>
						</ul>
					</div>
				</a>
				{x2;endtree}
			</div>
		</div>
		{x2;if:$msgs['pages']}
		<div class="pages-footer">
			{x2;$msgs['pages']}
		</div>
		{x2;endif}
	</div>
    {x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}