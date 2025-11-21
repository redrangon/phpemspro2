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
					<div class="text-center">{x2;$point['pointname']}</div>
				</a>
				<a class="col-xs-2">
					<div class="text-center">
						<i class="fa fa-option-horizontal"></i>
					</div>
				</a>
			</div>
			<div class="pages-content" data-nocache="yes">
				<div class="swiper">
					<div class="swiper-wrapper">
						{x2;eval: v:totalnumber = count($questions) }
						{x2;eval:v:index = 0}
						{x2;tree:$questions,question,qid}
						<div class="pages-box swiper-slide hide" data-index="{x2;v:index}" data-questionid="{x2;v:question['questionid']}"{x2;if:$useranswer[v:question['questionid']]} data-useranswer="{x2;$useranswer[v:question['questionid']]}"{x2;endif} {x2;if:$questypes[v:question['questiontype']]['questsort']}data-sort="1" data-answer="A"{x2;else}data-sort="0" data-answer="{x2;v:question['questionanswer']}"{x2;endif}{x2;if:$favors[v:question['questionid']]} data-favor="1"{x2;endif}>
							{x2;eval:v:index++}
							<form action="index.php?exam-mobile-point-save" method="post">
								<input type="hidden" name="pointid" value="{x2;$point['pointid']}"/>
								<div class="page-ele margin mg1 nomgLR">
									<h5 class="bigtitle col-xs-12">{x2;v:qid}/{x2;v:totalnumber} {x2;$questypes[v:question['questiontype']]['questype']}</h5>
									{x2;if:v:question['questionparent']}
									<div class="clear question">
										{x2;$parent[v:question['questionparent']]['qrquestion']}
									</div>
									{x2;endif}
									<div class="clear question">
										{x2;v:question['question']}
									</div>
									{x2;eval:$question = v:question}
									{x2;include:plugin_question}
								</div>
								{x2;if:$questypes[v:question['questiontype']]['questsort']}
								<div class="page-ele hide margin mg1 nomgLR">
									<div class="clear question" >
										<div class="col-xs-6 text-center">
											习题编号<br/>
											{x2;v:question['questionid']}
										</div>
										<div class="col-xs-6 text-center">
											难度<br/>
											{x2;if:v:question['questionlevel'] == 1}易{x2;elseif:v:question['questionlevel'] == 2}中{x2;else}难{x2;endif}
										</div>
									</div>
								</div>
								{x2;else}
								<div class="page-ele hide margin mg1 nomgLR">
									<div class="clear question" >
										<div class="col-xs-3 text-center">
											我的答案<br/>
											<span class="myanswer">-</span>
										</div>
										<div class="col-xs-3 text-center">
											正确答案<br/>
											{x2;v:question['questionanswer']}
										</div>
										<div class="col-xs-3 text-center">
											习题编号<br/>
											{x2;v:question['questionid']}
										</div>
										<div class="col-xs-3 text-center">
											难度<br/>
											{x2;if:v:question['questionlevel'] == 1}易{x2;elseif:v:question['questionlevel'] == 2}中{x2;else}难{x2;endif}
										</div>
									</div>
								</div>
								{x2;endif}
								{x2;if:$questypes[v:question['questiontype']]['questsort']}
								<div class="page-ele hide margin mg1 nomgLR">
									<h5 class="bigtitle col-xs-12">参考答案</h5>
									<div class="clear question">
										{x2;if:v:question['questionanswer']}
										{x2;v:question['questionanswer']}
										{x2;else}
										本题暂无参考答案
										{x2;endif}
									</div>
								</div>
								{x2;endif}
								<div class="page-ele hide margin mg1 nomgLR">
									<h5 class="bigtitle col-xs-12">解析</h5>
									<div class="clear question">
										{x2;if:v:question['questionintro']}
										{x2;v:question['questionintro']}
										{x2;else}
										本题暂无解析
										{x2;endif}
									</div>
								</div>
							</form>
						</div>
						{x2;endtree}
					</div>
				</div>				
			</div>
			<div class="pages-content hide">
				<div class="pages-box">
					<div class="page-ele clear">
						<div class="clear question">
                            {x2;tree:$questions,question,qid}
							<label class="selectbox col-xs-2 questionindex" data-index="{x2;v:qid}">
								<span class="selector">{x2;v:qid}</span>
							</label>
                            {x2;endtree}
						</div>
					</div>
				</div>
			</div>
			<div class="pages-footer">
				<a class="col-xs-10-2">
					<div class="text-center">
						<span class="fa fa-star"></span><br />收藏
					</div>
				</a>
				<a class="col-xs-10-2">
					<div class="text-center">
						<span class="fa fa-pencil"></span><br />笔记
					</div>
				</a>
				<a class="col-xs-10-2">
					<div class="text-center">
						<span class="fa fa-bookmark"></span><br />题卡
					</div>
				</a>
				<a class="col-xs-10-2">
					<div class="text-center">
						<span class="fa fa-cloud-upload"></span><br />纠错
					</div>
				</a>
				<a class="col-xs-10-2">
					<div class="text-center">
						<span class="fa fa-certificate"></span><br />答疑
					</div>
				</a>
			</div>
		</div>
		<script>
			$(function() {
                var subjectmodel = {x2;if:!$subject['subjectmodel']}true{x2;else}false{x2;endif};
				var pointid = '{x2;$point['pointid']}';
				var lastquestion = '{x2;$lastquestion}';
				questionPaper({subjectmodel,pointid,lastquestion,recordprogress:true});
			});
		</script>
    {x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}