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
					<li><a href="index.php?{x2;$_route['app']}-agent">代理平台</a></li>
					<li class="active">学员管理</li>
				</ol>
				<div class="panel panel-default" id="datacontent-container">
					<div class="panel-heading">学员管理</div>
					<div class="panel-body">
						<form action="index.php?finance-agent-users" method="post" class="form-inline">
							<table class="table">
								<thead>
								<tr>
									<td>
										电子邮箱：
									</td>
									<td>
										<input class="form-control" name="search[useremail]" size="15" type="text" value="{x2;$search['useremail']}"/>
									</td>
									<td>
										手机号码：
									</td>
									<td>
										<input class="form-control" name="search[userphone]" size="15" type="text" value="{x2;$search['userphone']}"/>
									</td>
									<td>
										<button class="btn btn-primary" type="submit">提交</button>
									</td>
								</tr>
								</thead>
							</table>
							<div class="input">
								<input type="hidden" value="1" name="search[argsmodel]" />
							</div>
						</form>
						<blockquote style="background-color:#f3f3f3;font-size:14px;border-left:5px solid #02756e;">
							共计用户 {x2;$users['number']} 个；
						</blockquote>
						<table class="table table-hover table-bordered">
							<thead>
							<tr class="info">
								<th width="120">手机号码</th>
								<th width="120">姓名</th>
								<th width="180">邮箱</th>
								<th>一年内订单数量</th>
								<th>一年内订单金额</th>
								<th width="120">操作</th>
							</tr>
							</thead>
							<tbody>
							{x2;tree:$users['data'],user,uid}
							<tr>
								<td>{x2;v:user['userphone']}</td>
								<td>{x2;v:user['userrealname']}</td>
								<td>{x2;v:user['useremail']}</td>
								<td>{x2;v:user['number']}</td>
								<td>{x2;v:user['price']}</td>
								<td>
									<ul class="list-unstyled list-inline">
										<li><a href="index.php?finance-agent-users-status&username={x2;v:user['username']}&page={x2;$page}{x2;$u}" title="学习进度">学习进度</a></li>
									</ul>
								</td>
							</tr>
							{x2;endtree}
							</tbody>
						</table>
						{x2;if:$users['pages']}
						<ul class="pagination pull-right">
                            {x2;$users['pages']}
						</ul>
						{x2;endif}
					</div>
				</div>
				{x2;include:footer}
                {x2;if:!$_userhash}
			</div>
		</div>
	</div>
</div>
</body>
</html>
{x2;endif}