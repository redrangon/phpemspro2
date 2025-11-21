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
					<li><a href="index.php?{x2;$_route['app']}-master-exams">考试管理</a></li>
					<li><a href="index.php?{x2;$_route['app']}-master-exams-questions">{x2;$subject['subjectname']}</a></li>
					<li class="active">移动知识点</li>
				</ol>
				<div class="panel panel-default" id="datacontent-container">
					<div class="panel-heading">
						移动知识点
					</div>
					<div class="panel-body">
						<form action="index.php?{x2;$_route['app']}-master-exams-questionbatch" method="post" class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">知识点：</label>
								<div class="col-sm-9">
									<div class="btn-toolbar" id="questionpointsid"></div>
									<span class="help-block">请在下方选择知识点，并点击选定</span>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2"></label>
								<div class="col-sm-9 form-inline">
									<select class="combox form-control" id="isectionselect" target="ipointselect" refUrl="index.php?exam-master-ajax-getpointsbysectionid&sectionid={value}">
										<option value="0">选择章节</option>
										{x2;tree:$sections,section,sid}
										<option value="{x2;v:section['sectionid']}">{x2;v:section['sectionname']}</option>
										{x2;endtree}
									</select>
									<select class="combox form-control" id="ipointselect">
										<option value="0">选择知识点</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2"></label>
								<div class="col-sm-9 form-inline">
									<input type="button" class="btn btn-primary" value="选定" onclick="javascript:setKnowsList('questionpointsid','ipointselect','+');"/>
									<input type="button" class="btn btn-danger" value="清除" onclick="javascript:setKnowsList('questionpointsid','ipointselect','-');"/>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">试题ID</label>
								<div class="col-sm-4">
									<input class="form-control" type="text" name="questionids" value="{x2;$ids}" needle="needle" msg="您必须输入试题ID" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2"></label>
								<div class="col-sm-9">
									<button class="btn btn-primary" type="submit">提交</button>
									<a class="btn btn-primary" href="index.php?{x2;$_route['app']}-master-exams&page={x2;$page}{x2;$u}">取消</a>
									{x2;tree:$search,arg,sid}
									<input type="hidden" name="search[{x2;v:key}]" value="{x2;v:arg}"/>
									{x2;endtree}
									<input type="hidden" name="action" value="setknows">
								</div>
							</div>
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