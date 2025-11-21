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
		<div class="pages-content{x2;if:!$questions['pages']} nofooter{x2;endif}">
			<div class="pages-box">
				{x2;if:$search['keywords']}
				<div class="page-ele">
					<ul class="listmenu">
						{x2;if:$search['questiontype']}
						{x2;tree:$questions['data'],question,qid}
						<li class="small">
							<a title="查看试题" class="ajax" href="index.php?exam-mobile-search-qrdetail&qrid={x2;v:question['qrid']}">{x2;substring:strip_tags(html_entity_decode(v:question['qrquestion'])),120}</a>
						</li>
						{x2;endtree}
						{x2;else}
						{x2;tree:$questions['data'],question,qid}
						<li class="small">
							<a title="查看试题" class="ajax" href="index.php?exam-mobile-search-detail&questionid={x2;v:question['questionid']}">{x2;substring:strip_tags(html_entity_decode(v:question['question'])),120}</a>
						</li>
						{x2;endtree}
						{x2;endif}
					</ul>
				</div>
				{x2;endif}
			</div>
		</div>
		{x2;if:$questions['pages']}
		<div class="pages-footer">
			{x2;$questions['pages']}
		</div>
		{x2;endif}
	</div>
    {x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}