{x2;if:!$_userhash}
{x2;include:header}
<div class="pages">
    {x2;endif}
	{x2;if:$page <= 1}
		<div class="pages-tabs">
			<div class="pages-header">
				<a class="col-xs-2" href="javascript:history.back();"><div class="text-center">
						<i class="fa fa-angle-left"></i>
					</div></a>
				<a class="col-xs-8 active">
					<div class="text-center">我的提问</div>
				</a>
				<a class="col-xs-2">
					<div class="text-center">
						<i class="fa fa-option-horizontal"></i>
					</div>
				</a>
			</div>
			<div class="pages-content nofooter" data-pageurl="index.php?exam-mobile-point-faq&questionid={x2;$question['questionid']}" data-scroll="yes">
				<div class="pages-box">
					<form action="index.php?exam-mobile-point-faq" method="post" id="faqform">
						<div class="page-ele">
							<div class="bigtitle">新的提问</div>
							<div class="clear question">
								<textarea class="form-control hasborder" rows="5" name="args[faqask]" needle="needle" msg="请输入提问内容"></textarea>
							</div>
						</div>
						<div class="page-ele clear padding pd2">
							<input type="hidden" name="savefaq" value="1" />
							<input type="hidden" name="args[faqquestionid]" value="{x2;$question['questionid']}" />
							<a onclick="javascript:$('#faqform').submit();" class="btn btn-primary btn-block ajax">保存</a>
						</div>
					</form>
					{x2;endif}
					{x2;tree:$faqs['data'],faq,fid}
					<div class="page-ele contin">
						<div class="title">{x2;v:faq['faqusername']}</div>
						<div class="clear intro">
							{x2;realhtml:v:faq['faqask']}
						</div>
						{x2;if:v:faq['faqanswer']}
						<div class="title">
							{x2;v:faq['faqteacher']} 回复
						</div>
						<div class="clear intro">
							{x2;realhtml:v:faq['faqanswer']}
						</div>
						{x2;endif}
					</div>
					{x2;endtree}
				{x2;if:$page <= 1}
				</div>
			</div>
		</div>
		{x2;endif}
    {x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}