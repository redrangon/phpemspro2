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
				<div class="text-center">{x2;substring:$goods['sgdtitle'],36}</div>
			</a>
			<a class="col-xs-2">
				<div class="text-center">
					<i class="fa fa-option-horizontal"></i>
				</div>
			</a>
		</div>
		<div class="pages-content nofooter">
			<div class="pages-box">
				<div class="page-ele padding pd2">
					<div class="text-center title">{x2;$goods['sgdtitle']}</div>
					<div class="text-center time">{x2;$sale['salestime']} - {x2;$sale['saleetime']}</div>
					<table class="table table-hover table-bordered">
						<tbody> 
						{x2;tree:$items,item,lid}
						<tr>
							<td>
								{x2;$titles[v:item['simid']]}
							</td>
							<td width="120">
								{x2;$itemtypes[v:item['simtype']]}
							</td>
							<td width="150">
								{x2;v:item['simday']} 天
							</td>
						</tr>
						{x2;endtree}
						</tbody>
					</table>
				</div>
			</div>
			<div class="pages-box">
				<div class="page-ele padding pd1 nopdLRT margin mg1 clear">
					<div class="cartbox">
						<div class="cart">
							<div class="col-xs-4">
								<div class="title">活动价</div>
								<div class="intro">
									<s>原价：{x2;eval: echo intval($goods['sgdprice'] * 3)} 元</s>
								</div>
							</div>
							<div class="col-xs-8 text-warning price">
								￥{x2;$goods['sgdprice']}
							</div>
						</div>
					</div>
				</div>
				<form action="index.php?sale-mobile-sales-order&sgdid={x2;$goods['sgdid']}" method="post">
					{x2;if:!\PHPEMSPRO\route::isWeixin()}
					<div class="page-ele padding pd1 nopdLRT clear">
						<div class="col-xs-6">
							<label class="cartbox margin mg1">
								<input type="radio" name="paytype" value="alipay" checked />
								<div class="cards cart">
									<div class="col-xs-4 text-center">
										<img src="public/static/images/zhifubao.png" style="width: 76%;"/>
									</div>
									<div class="col-xs-8 padding pd1">
										<p>支付宝支付</p>
									</div>
								</div>
							</label>
						</div>
						<div class="col-xs-6">
							<label class="cartbox margin mg1">
								<input type="radio" name="paytype" value="wxpay" />
								<div class="cards cart">
									<div class="col-xs-4 text-center">
										<img src="public/static/images/wxpay.png" style="width: 76%;"/>
									</div>
									<div class="col-xs-8 padding pd1">
										<p>微信支付</p>
									</div>
								</div>
							</label>
						</div>
					</div>
					{x2;endif}
					<div class="page-ele margin mg1 clear">
						<button class="btn btn-primary btn-block" type="submit">购买</button>
						<a class="btn btn-danger btn-block hide" href="submit">充值码购买</a>
						<input type="hidden" value="1" name="openbasic" />
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
{x2;include:footer}
</body>
</html>