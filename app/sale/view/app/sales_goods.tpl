{x2;include:header}
<body>
{x2;include:nav}
<div class="container-fluid pages-content">
	<div class="row-fluid panels">
		<div class="pep panels">
			<div class="col-xs-3">
				<div class="panel panel-default pagebox border">
					<div class="panel-heading bold">
						相关活动
					</div>
					<div class="panel-body">
						<ul class="list-group">
							{x2;tree:$salegoods,goods,gid}
							<li class="list-group-item">
								<a href="index.php?sale-app-sales-goods&sgdid={x2;v:goods['sgdid']}">
									{x2;v:goods['sgdtitle']}
								</a>
							</li>
							{x2;endtree}
						</ul>
					</div>
				</div>
			</div>
			<div class="col-xs-9 nopadding">
				<div class="panel panel-default pagebox">
					<div class="panel-heading">
						{x2;$sale['saletitle']}
						{x2;if:strtotime($sale['salestime']) < TIME && strtotime($sale['saleetime']) > TIME}
						<a class="btn btn-primary pull-right ajax" href="index.php?sale-app-sales-order&sgdid={x2;v:goods['sgdid']}"><i class="glyphicon glyphicon-shopping-cart"></i> 立即购买</a>
						{x2;else}
						<a class="btn btn-default pull-right" href="javascript:;">未在活动时间内，不能购买</a>
						{x2;endif}
					</div>
					<div class="panel-body pagebox padding">
						<h2 class="text-center">{x2;$goods['sgdtitle']}</h2>
						<p class="text-center intros">活动时间：{x2;$sale['salestime']} - {x2;$sale['saleetime']}</p>
						<hr />
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
						<div class="col-xs-12 clear">
							{x2;$goods['sgdinfo']}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
{x2;include:footer}
</body>
</html>