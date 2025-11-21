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
					<div class="text-center">订单详情</div>
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
						<h4 class="title text-center">订单邮件</h4>
						<hr />
						<div class="news">
							{x2;realhtml:v:order['orderdescribe']}
						</div>
					</div>
					{x2;if:(TIME - strtotime($order['ordertime'])) < 432000 && $order['orderstatus'] == 2}
					<div class="page-ele clear">
						<p class="col-xs-12">
							<a class="btn btn-danger btn-block confirm" href="index.php?finance-mobile-basics-cancel&ordersn={x2;v:order['ordersn']}">取消订单</a>
						</p>
					</div>
					{x2;endif}
					<div class="page-ele clear">
						<p class="col-xs-12">
							<a class="btn btn-primary btn-block confirm" href="index.php?finance-mobile-basics-email&ordersn={x2;v:order['ordersn']}&ordersn={x2;v:order['ordersn']}" msg="确定要重发邮件吗？">重发邮件</a>
						</p>
					</div>
				</div>
			</div>
		</div>
    {x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}