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
				<div class="text-center">扫码登陆</div>
			</a>
			<a class="col-xs-2">
				<div class="text-center">
					<i class="fa fa-option-horizontal"></i>
				</div>
			</a>
		</div>
		<div class="pages-content nofooter" data-refresh="yes">
			<div class="pages-box">
				<div class="page-ele margin">
					<ul class="orders">
						<li>
							<div class="text-center">
								<img src="{x2;$user['userphoto']}" class="circle" style="width: 0.96rem;"/>
							</div>
						</li>
					</ul>
				</div>
				<div class="page-ele text-center">
					<ul class="orders">
						<li class="title">用户{x2;$user['username']}已成功登陆！</li>
					</ul>
				</div>
				<div class="page-form">
					<div class="form-group">
						<div class="controls">
							<a href="javascript:;" onclick="javascript:WeixinJSBridge.call('closeWindow');" class="btn btn-primary btn-block">关闭</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
    {x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}