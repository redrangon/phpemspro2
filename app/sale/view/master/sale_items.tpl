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
					<li><a href="index.php?{x2;$_route['app']}-master-sales">活动管理</a></li>
					<li><a href="index.php?{x2;$_route['app']}-master-sales-goods&saleid={x2;$sale['saleid']}">{x2;$sale['saletitle']}</a></li>
					<li class="active">{x2;$goods['sgdtitle']}</li>
				</ol>
				<div class="panel panel-default" id="datacontent-container">
					<div class="panel-heading">
						{x2;$goods['sgdtitle']}
						<a href="index.php?{x2;$_route['app']}-master-sales-additem&goodsid={x2;$goods['sgdid']}" class="pull-right">增加</a>
					</div>
					<div class="panel-body">
						<table class="table table-hover table-bordered">
							<thead>
							<tr class="info">
								<th width="40">ID</th>
								<th>名称</th>
								<th width="120">数据库</th>
								<th width="120">类型</th>
								<th width="120">时长</th>
								<th width="180">发布时间</th>
								<th width="100">操作</th>
							</tr>
							</thead>
							<tbody>
							{x2;tree:$items['data'],item,lid}
							<tr>
								<td>{x2;v:item['simid']}</td>
								<td>
									{x2;$titles[v:item['simid']]}
								</td>
								<td>
									{x2;v:item['simdb']}
								</td>
								<td>
									{x2;v:item['simtype']}
								</td>
								<td>
									{x2;v:item['simday']}
								</td>
								<td>
									{x2;v:item['simtime']}
								</td>
								<td class="actions">
									<ul class="list-unstyled list-inline">
										<li><a href="index.php?{x2;$_route['app']}-master-sales-modifyitem&itemid={x2;v:item['simid']}&page={x2;$page}{x2;$u}" title="修改">修改</a></li>
										<li><a class="confirm" href="index.php?{x2;$_route['app']}-master-sales-delitem&itemid={x2;v:item['simid']}&page={x2;$page}{x2;$u}" title="删除">删除</a></li>
									</ul>
								</td>
							</tr>
							{x2;endtree}
							</tbody>
						</table>
						{x2;if:$items['pages']}
						<ul class="pagination pull-right">
							{x2;$items['pages']}
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