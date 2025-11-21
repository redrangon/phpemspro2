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
					<li class="active">增加产品</li>
				</ol>
				<div class="panel panel-default" id="datacontent-container">
					<div class="panel-heading">
						增加产品
						<a href="index.php?{x2;$_route['app']}-master-sales-goods&saleid={x2;$sale['saleid']}" class="pull-right">产品管理</a>
					</div>
					<div class="panel-body">
						<form action="index.php?{x2;$_route['app']}-master-sales-addgoods" method="post" class="form-horizontal">
							<fieldset>
								<div class="form-group">
									<label for="modulename" class="control-label col-sm-2">产品名称</label>
									<div class="col-sm-4">
										<input class="form-control" type="text" name="args[sgdtitle]" value="{x2;$goods['sgdtitle']}" needle="needle" msg="您必须输入产品名称">
									</div>
								</div>
								<div class="form-group">
									<label for="moduledescribe" class="control-label col-sm-2">产品图片</label>
									<div class="col-sm-9">
										<script type="text/template" id="pe-template-photo">
											<div class="qq-uploader-selector" style="width:30%" qq-drop-area-text="可将图片拖拽至此处上传" style="clear:both;">
												<div class="qq-upload-button-selector" style="clear:both;">
													<ul class="qq-upload-list-selector list-unstyled" aria-live="polite" aria-relevant="additions removals" style="clear:both;">
														<li class="text-center">
															<div class="thumbnail">
																<img class="qq-thumbnail-selector" alt="点击上传新图片">
																<input type="hidden" class="qq-edit-filename-selector" name="args[sgdthumb]" tabindex="0">
															</div>
														</li>
													</ul>
													<ul class="qq-upload-list-selector list-unstyled" aria-live="polite" aria-relevant="additions removals" style="clear:both;">
														<li class="text-center">
															<div class="thumbnail">
																<img class="qq-thumbnail-selector" src="public/static/images/noimage.gif" alt="点击上传新图片">
																<input type="hidden" class="qq-edit-filename-selector" name="args[sgdthumb]" tabindex="0" value="public/static/images/noimage.gif">
															</div>
														</li>
													</ul>
												</div>
											</div>
										</script>
										<div class="fineuploader" attr-type="thumb" attr-template="pe-template-photo"></div>
									</div>
								</div>
								<div class="form-group">
									<label for="catdes" class="control-label col-sm-2">价格</label>
									<div  class="col-sm-3 form-inline">
										<input class="form-control" name="args[sgdprice]" value="{x2;$goods['sgdprice']}" type="text" needle="needle" msg="您必须输入价格" /> 元
									</div>
								</div>
								<div class="form-group">
									<label for="contenttitle" class="control-label col-sm-2">产品说明：</label>
									<div class="col-sm-9">
										<textarea name="args[sgdinfo]" class="form-control ckeditor" id="sgdinfo" rows="4">{x2;$goods['sgdinfo']}</textarea>
									</div>
								</div>
								<div class="form-group">
									<label for="catdes" class="control-label col-sm-2"></label>
									<div class="col-sm-10">
										<button class="btn btn-primary" type="submit">提交</button>
										<input type="hidden" name="addgoods" value="1"/>
										<input type="hidden" name="saleid" value="{x2;$sale['saleid']}"/>
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