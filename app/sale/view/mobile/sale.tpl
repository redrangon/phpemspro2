{x2;if:!$_userhash}
{x2;include:header}
<div class="pages">
	{x2;endif}
	<div class="pages-tabs">
		<div class="pages-header">
			<a class="col-xs-2" href="javascript:history.back();">
				<div class="text-center">
					<i class="fa fa-angle-left"></i>
				</div>
			</a>
			<a class="col-xs-8 active">
				<div class="text-center">{x2;substring:$sale['saletitle'],36}</div>
			</a>
			<a class="col-xs-2">
				<div class="text-center">
					<i class="fa fa-option-horizontal"></i>
				</div>
			</a>
		</div>
		<div class="pages-content nofooter">
			<div class="pages-box nopadding">
				{x2;tree:$goods,goods,gid}
				<div class="page-ele padding pd1">
					<a data-page="yes" data-title="{x2;v:goods['sgdtitle']}" href="index.php?sale-mobile-sales-goods&sgdid={x2;v:goods['sgdid']}" class="ajax">
						<div class="col-xs-4 padding pd2 text-center">
							<img src="{x2;v:goods['sgdthumb']}" class="img-circle" style="width: 86%;"/>
						</div>
						<div class="col-xs-8">
							<div class="title">{x2;v:goods['sgdtitle']}</div>
							<div class="time">
								价格：{x2;v:goods['sgdprice']}元
							</div>
							<div class="intro">
								{x2;substring:v:goods['sgdinfo'],72}
							</div>
						</div>
					</a>
				</div>
				{x2;endtree}
			</div>
		</div>
	</div>
	{x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}