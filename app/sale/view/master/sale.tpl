{x2;if:!$_userhash}
{x2;include:header}
<body>
{x2;include:nav}
<div class="container-fluid">
	<div class="row-fluid">
		<div class="pep">
			<div class="col-xs-2 leftmenu">
				{x2;include:menu}
			</div>
			<div id="datacontent">
{x2;endif}
				<ol class="breadcrumb">
					<li><a href="index.php?{x2;$_route['app']}-master">{x2;$apps[$_route['app']]['appname']}</a></li>
                    <li class="active">活动管理</li>
				</ol>
				<div class="panel panel-default" id="datacontent-container">
					<div class="panel-heading">
						活动管理
						<a href="index.php?{x2;$_route['app']}-master-sales-add" class="pull-right">增加</a>
					</div>
					<div class="panel-body">
						<table class="table table-hover table-bordered">
							<thead>
							<tr class="info">
								<th width="40">ID</th>
								<th>活动名称</th>
								<th width="300">起止时间</th>
								<th width="180">发布时间</th>
								<th width="140">操作</th>
							</tr>
							</thead>
							<tbody>
							{x2;tree:$sales['data'],sale,lid}
							<tr>
								<td>{x2;v:sale['saleid']}</td>
								<td>
									{x2;v:sale['saletitle']}
								</td>
								<td>
									{x2;v:sale['salestime']} - {x2;v:sale['saleetime']}
								</td>
								<td>
									{x2;v:sale['saletime']}
								</td>
								<td class="actions">
									<ul class="list-unstyled list-inline">
										<li><a href="index.php?{x2;$_route['app']}-master-sales-goods&saleid={x2;v:sale['saleid']}&page={x2;$page}{x2;$u}" title="产品">产品</a></li>
										<li><a href="index.php?{x2;$_route['app']}-master-sales-modify&saleid={x2;v:sale['saleid']}&page={x2;$page}{x2;$u}" title="修改">修改</a></li>
										<li><a class="confirm" href="index.php?{x2;$_route['app']}-master-sales-del&saleid={x2;v:sale['saleid']}&page={x2;$page}{x2;$u}" title="删除">删除</a></li>
									</ul>
								</td>
							</tr>
							{x2;endtree}
							</tbody>
						</table>
						{x2;if:$sales['pages']}
						<ul class="pagination pull-right">
							{x2;$sales['pages']}
						</ul>
						{x2;endif}
					</div>
				</div>
				{x2;if:!$_userhash}
				{x2;include:footer}
			</div>
		</div>
	</div>
</div>
</body>
</html>
{x2;endif}