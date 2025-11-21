{x2;if:!$_userhash}
{x2;include:header}
<div class="pages">
    {x2;endif}
	<div class="pages-tabs">
		<div class="pages-header">
			<a class="col-xs-2" href="javascript:history.back();"><div class="text-center">
				<i class="fa fa-angle-left"></i>
			</div></a>
			<a class="col-xs-8 active">
				<div class="text-center">试题搜索</div>
			</a>
			<a class="col-xs-2">
				<div class="text-center">
					<i class="fa fa-option-horizontal"></i>
				</div>
			</a>
		</div>
		<div class="pages-content nofooter">
			<div class="pages-box">
				<div class="page-form">
					<form action="index.php?exam-mobile-search-result" method="get">
						<div class="form-group border">
							<label class="control-label col-xs-3">关键字</label>
							<div class="col-xs-9">
								<input type="search" value="{x2;$search['keywords']}" name="search[keywords]" placeholder="请输入题库关键字">
								<input name="search[questiontype]" type="hidden" value="0">
							</div>
						</div>
						<div class="form-group border">
							<label class="control-label col-xs-3">类型</label>
							<div class="col-xs-9">
								<label class="radio-inline"><input type="radio" name="search[questiontype]" value="0" checked=""><span>普通试题</span></label>
								<label class="radio-inline"><input type="radio" name="search[questiontype]" value="1"><span>题帽题</span></label>
							</div>
						</div>
						<div class="form-group">
							<div class="controls">
								<button class="btn btn-primary btn-block" type="submit">搜索</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
    {x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}