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
					<li><a href="index.php?{x2;$_route['app']}-agent-users">学员管理</a></li>
					<li class="active">学习进度</li>
				</ol>
				<div class="panel panel-default" id="datacontent-container">
					<div class="panel-heading">{x2;$username} 的学习进度</div>
					<div class="panel-body">
						<table class="table table-hover table-bordered">
							<tr class="info">
								<th width="120">培训类型</th>
								<th>课程名称</th>
								<th width="180">到期时间</th>
								<th width="360">学习进度</th>
								<th width="200">操作</th>
							</tr>
							{x2;tree:$trainings,training,tid}
							{x2;if:$usetraining[v:training['trid']]}
							{x2;tree:$subjects[v:training['trid']],subject,sid}
							{x2;tree:$basics[v:subject['subjectid']],basic,bid}
							<tr>
								<td>{x2;v:training['trname']}</td>
								<td>{x2;v:basic['basic']}</td>
								<td>{x2;v:basic['obendtime']}</td>
								<td>
									{x2;if:$status[v:subject['subjectid']][v:basic['basicid']]['point']}
									{x2;$status[v:subject['subjectid']][v:basic['basicid']]['point']}
									{x2;if:$status[v:subject['subjectid']][v:basic['basicid']]['index']}
									第{x2;$status[v:subject['subjectid']][v:basic['basicid']]['index']}题
									{x2;endif}
									{x2;else}
									未学习
									{x2;endif}
								</td>
								<td>
									<a class="btn" href="index.php?finance-agent-users-schedule&subjectid={x2;v:subject['subjectid']}&basicid={x2;v:basic['basicid']}&username={x2;$username}">练习进度</a>
									<a class="btn" href="index.php?finance-agent-users-history&subjectid={x2;v:subject['subjectid']}&basicid={x2;v:basic['basicid']}&username={x2;$username}">考试记录</a>
								</td>
							</tr>
							{x2;endtree}
							{x2;endtree}
							{x2;endif}
							{x2;endtree}
						</table>
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