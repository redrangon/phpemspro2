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
				<div class="text-center">充值记录</div>
			</a>
			<a class="col-xs-2">
				<div class="text-center">
					<i class="fa fa-option-horizontal"></i>
				</div>
			</a>
		</div>
		<div class="pages-content{x2;if:!$orders['pages']} nofooter{x2;endif}">
			<div class="pages-box">
				<div class="page-ele">
					<p class="bigtitle">共 {x2;$orders['number']} 个订单，合计 {x2;$money} 元</p>
				</div>
				{x2;tree:$orders['data'],order,oid}
				<div class="page-ele margin mg1 nomgLR">
					<ul class="orders">
						<li class="title">订单号：{x2;v:order['ordersn']} <span class="pull-right">{x2;v:order['ordertime']}</span></li>
						<li class="detail">
							{x2;v:order['ordername']}
						</li>
						<li class="tip">合计：<span class="price">￥ {x2;v:order['orderprice']}</span></li>
					</ul>
				</div>
				{x2;endtree}
			</div>
		</div>
		{x2;if:$orders['pages']}
		<div class="pages-footer">
			{x2;$orders['pages']}
		</div>
		{x2;endif}
	</div>
    {x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}