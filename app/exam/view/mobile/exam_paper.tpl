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
					<div class="text-center">{x2;$paper['name']}</div>
				</a>
				<a class="col-xs-2">
					<div class="text-center">
						<i class="fa fa-option-horizontal"></i>
					</div>
				</a>
			</div>
			<div class="pages-content" data-callback="savedata" data-nocache="yes">
				<form action="index.php?exam-mobile-exam-save" method="post" id="exampaperForm">
					<input type="hidden" name="token" value="{x2;$token}">
					<input type="hidden" name="sign" value="{x2;$sign}">
					<input type="hidden" name="answers" id="answers">
				</form>
				<div class="swiper">
					<div class="swiper-wrapper">
						{x2;eval:v:innerquestionindex = 0;}
						{x2;tree:$paper['setting']['papersetting']['questypelite'],lite,lid}
						{x2;eval:v:questype = v:key}
						{x2;eval:v:questypeindex = 0;}
						{x2;if:$paper['question']['questions'][v:questype] || $paper['question']['questionrows'][v:questype]}
						{x2;eval:v:questypeindex++;}
						{x2;if:$paper['question']['questions'][v:questype]}
						{x2;if:$basic['basicexam']['changesequence']}
						{x2;eval: shuffle($paper['question']['questions'][v:questype]);}
						{x2;endif}
						{x2;tree:$paper['question']['questions'][v:questype],question,qid}

						<div class="pages-box swiper-slide" data-questionid="{x2;v:question['questionid']}" data-index="{x2;v:innerquestionindex}">
							<div class="page-ele margin mg1 nomgLR">
								<h5 class="bigtitle col-xs-12">{x2;v:qid}/{x2;$paper['setting']['papersetting']['questype'][v:questype]['number']} {x2;$questypes[v:question['questiontype']]['questype']}</h5>
								<div class="clear question">
									{x2;v:question['question']}
								</div>
								{x2;eval:$question = v:question}
								{x2;include:plugin_examquestion}
							</div>
						</div>
						{x2;eval:v:innerquestionindex ++;}

						{x2;endtree}
						{x2;endif}
						{x2;if:$paper['question']['questionrows'][v:questype]}
						{x2;if:$basic['basicexam']['changesequence']}
						{x2;eval: shuffle($paper['question']['questionrows'][v:questype]);}
						{x2;endif}
						{x2;tree:$paper['question']['questionrows'][v:questype],questionrows,qrid}
						{x2;tree:v:questionrows['data'],question,qid}
						<div class="pages-box swiper-slide" data-questionid="{x2;v:question['questionid']}" data-index="{x2;v:innerquestionindex}">
							<div class="page-ele margin mg1 nomgLR">
								<h5 class="bigtitle">{x2;v:qid}/{x2;$paper['setting']['papersetting']['questype'][v:questype]['number']} {x2;$questypes[v:questionrows['qrtype']]['questype']}（{x2;$questypes[v:question['questiontype']]['questype']}）</h5>
								<div class="clear question">
									{x2;v:questionrows['qrquestion']}
								</div>
								<div class="clear question">
									{x2;v:question['question']}
								</div>
								{x2;eval:$question = v:question}
								{x2;include:plugin_examquestion}
							</div>
						</div>
						{x2;eval:v:innerquestionindex ++;}
						{x2;endtree}
						{x2;endtree}
						{x2;endif}
						{x2;endif}
						{x2;endif}
					</div>
				</div>
			</div>
			<div class="pages-content hide">
				<div class="pages-box">
					<div class="page-ele radius margin">
						<div class="clear question">
                            {x2;eval:v:qtid = 0}
							{x2;tree:$paper['setting']['papersetting']['questypelite'],lite,lid}
                            {x2;eval:v:qtindex = 0}
                            {x2;eval:v:questype = v:key}
                            {x2;if:$paper['question']['questions'][v:questype] || $paper['question']['questionrows'][v:questype]}
							<h5 class="bigtitle col-xs-12">{x2;$questypes[v:questype]['questype']}</h5>
                            {x2;if:$paper['question']['questions'][v:questype]}
							{x2;tree:$paper['question']['questions'][v:questype],question,qid}
                            {x2;eval:v:qtid++}
                            {x2;eval:v:qtindex++}
							<label class="selectbox col-xs-2 questionindex" data-index="{x2;v:qtid}" data-questionid="{x2;v:question['questionid']}">
								<span class="selector">{x2;v:qid}</span>
							</label>
                            {x2;endtree}
							{x2;endif}
                            {x2;if:$paper['question']['questionrows'][v:questype]}
                            {x2;tree:$paper['question']['questionrows'][v:questype],questionrows,qrid}
							{x2;tree:v:questionrows['data'],question,qid}
                            {x2;eval:v:qtid++}
                            {x2;eval:v:qtindex++}
							<label class="selectbox col-xs-2 questionindex" data-index="{x2;v:qtid}" data-questionid="{x2;v:question['questionid']}">
								<span class="selector">{x2;v:qrid}.{x2;v:qid}</span>
							</label>
                            {x2;endtree}
                            {x2;endtree}
							{x2;endif}
							{x2;endif}
                            {x2;endtree}
						</div>
					</div>
				</div>
			</div>
			<div class="pages-footer">
				<a class="col-xs-4">
					<div class="text-center timer">
						<span id="exampaper-timer_h">00</span>:<span id="exampaper-timer_m">00</span>:<span id="exampaper-timer_s">00</span>
					</div>
				</a>
				<a class="col-xs-4">
					<div class="text-center">
						<span class="btn btn-primary btn-block subpaperbtn">交卷</span>
					</div>
				</a>
				<a class="col-xs-2">
					<div class="text-center">
						<span class="fa fa-bookmark"></span><br />题卡
					</div>
				</a>
				<a class="col-xs-2">
					<div class="text-center">
						<span class="fa fa-star"></span><br />标记
					</div>
				</a>
			</div>
		</div>
		<script>
			var savedata = function(){
				pep.mask.show('tips',{message:'请先交卷'});
                history.pushState({id:'index.php?exam-mobile-exam-paper'},'{x2;$paper['name']}','index.php?exam-mobile-exam-paper');
			}
			$(function() {
				var time = '{x2;$paper['time']}';
				var token = '{x2;$token}';
				var subjectmodel = {x2;if:!$subject['subjectmodel']}true{x2;else}false{x2;endif};
				var useranswer = $.parseJSON('{x2;if:$useranswer}{x2;$useranswer}{x2;else}{}{x2;endif}');

				examPaper({subjectmodel,token,useranswer,time});
			});
		</script>
    {x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}