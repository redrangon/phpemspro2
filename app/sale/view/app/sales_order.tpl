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
						订单查询
					</div>
					<div class="panel-body pagebox padding">
						<table class="table table-striped table-hover">
							<tr>
								<th>订单号</th>
								<th>名称</th>
								<th>价格（元）</th>
								<th>下单时间</th>
							</tr>
							{x2;tree:$orders['data'],order,sid}
							<tr>
								<td>
									{x2;v:order['ordersn']}
								</td>
								<td>
									{x2;v:order['ordername']}
								</td>
								<td>
									{x2;v:order['orderprice']}
								</td>
								<td>
									{x2;v:order['ordertime']}
								</td>
							</tr>
							{x2;endtree}
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>