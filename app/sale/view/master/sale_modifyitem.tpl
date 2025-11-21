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
					<li><a href="index.php?{x2;$_route['app']}-master-sales-items&goodsid={x2;$goods['sgdid']}">{x2;$goods['sgdtitle']}</a></li>
					<li class="active">修改配置</li>
				</ol>
				<div class="panel panel-default" id="datacontent-container">
					<div class="panel-heading">
						修改配置
						<a href="index.php?{x2;$_route['app']}-master-sales-items&goodsid={x2;$goods['sgdid']}" class="pull-right">产品管理</a>
					</div>
					<div class="panel-body">
						<form action="index.php?{x2;$_route['app']}-master-sales-modifyitem" method="post" class="form-horizontal">
							<fieldset>
								<div class="form-group">
									<label for="questsort" class="control-label col-sm-2">数据库：</label>
									<div class="col-sm-4">
										<select class="form-control" id="subjectdb" name="args[simdb]">
											{x2;tree:$databases,db,did}
											<option value="{x2;v:key}"{x2;if:$item['simdb'] == v:key} selected{x2;endif}>{x2;v:db['host']}/{x2;v:db['name']}</option>
											{x2;endtree}
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-2">类型：</label>
									<div class="col-sm-9">
										<label class="radio-inline">
											<input type="radio" name="args[simtype]" value="exam"{x2;if:$item['simtype'] == 'exam'} checked{x2;endif}/> 题库
										</label>
										<label class="radio-inline">
											<input type="radio" name="args[simtype]" value="lesson"{x2;if:$item['simtype'] == 'lesson'} checked{x2;endif}/> 课程
										</label>
									</div>
								</div>
								<div class="form-group">
									<label for="modulename" class="control-label col-sm-2">ID</label>
									<div class="col-sm-4">
										<input class="form-control" type="text" id="input1" name="args[simitemid]" value="{x2;$item['simitemid']}" needle="needle" msg="您必须输入ID">
									</div>
								</div>
								<div class="form-group">
									<label for="modulename" class="control-label col-sm-2">原价</label>
									<div class="col-sm-4">
										<input class="form-control" type="text" name="args[simprice]" needle="needle" value="{x2;$item['simprice']}" msg="您必须输入原价">
									</div>
								</div>
								<div class="form-group">
									<label for="catdes" class="control-label col-sm-2">时长</label>
									<div  class="col-sm-10 form-inline">
										<input class="form-control" name="args[simday]" type="text" msg="您必须输入时长" value="{x2;$item['simday']}" needle="needle"/> 天
									</div>
								</div>
								<div class="form-group">
									<label for="catdes" class="control-label col-sm-2"></label>
									<div class="col-sm-10">
										<button class="btn btn-primary" type="submit">提交</button>
										<input type="hidden" name="modifyitem" value="1"/>
										<input type="hidden" name="itemid" value="{x2;$item['simid']}"/>
										{x2;tree:$search,arg,aid}
										<input type="hidden" name="search[{x2;v:key}]" value="{x2;v:arg}"/>
										{x2;endtree}
									</div>
								</div>
							</fieldset>
						</form>
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