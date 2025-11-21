{x2;include:header}
<body>
<style>
	.pep{
		width: 100%;
	}
	.pep .regbox{
		width: 88%;
		margin-top: 10px;
	}
</style>
<div class="container-fluid">
	<div class="row-fluid panels">
		<div class="pep panels">
			<div class="col-xs-12">
				<div class="panel panel-default pagebox margin">
					<div class="panel-heading">
						订单详情
						{x2;if:$order['orderstatus'] == 2}
						<a class="btn btn-primary pull-right" style="margin: 0px;">支付成功</a>
						{x2;elseif:$order['orderstatus'] != 1}
						<a class="btn btn-danger pull-right" style="margin: 0px;">订单已取消</a>
						{x2;endif}
					</div>
					<div class="panel-body pagebox padding">
						<table class="table table-striped table-hover table-bordered">
							<tr>
								<th>商品名</th>
								<th width="180">时长</th>
							</tr>
							{x2;tree:$order['orderitems'],item,sid}
							<tr>
								<td>
									{x2;v:item['lessonname']}{x2;v:item['basicname']}
								</td>
								<td>
									{x2;v:item['time']}天
								</td>
							</tr>
							{x2;endtree}
							<tr>
								<td colspan="2" class="text-right" style="padding-right: 15px;">
									合计：<b class="text-warning">{x2;$order['orderprice']}元</b>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>