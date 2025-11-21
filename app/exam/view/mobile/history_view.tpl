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
					<div class="text-center">{x2;$history['ehexam']}</div>
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
						{x2;tree:$history['ehsetting']['papersetting']['questypelite'],lite,lid}
						{x2;eval:v:questype = v:key}
						{x2;eval:v:questypeindex = 0;}
                        {x2;eval:v:innerquestionindex = 0;}
						{x2;if:$history['ehquestion']['questions'][v:questype] || $history['ehquestion']['questionrows'][v:questype]}
                        {x2;eval:v:questypeindex++;}
						{x2;if:$history['ehquestion']['questions'][v:questype]}
						{x2;tree:$history['ehquestion']['questions'][v:questype],question,qid}
                        {x2;eval:v:innerquestionindex ++;}

						<div class="pages-box swiper-slide" data-questionid="{x2;v:question['questionid']}"{x2;if:$history['ehuseranswer'][v:question['questionid']] && !$questypes[v:question['questiontype']]['questsort']} data-useranswer="{x2;$history['ehuseranswer'][v:question['questionid']]}"{x2;endif} {x2;if:$questypes[v:question['questiontype']]['questsort']}data-sort="1" data-answer="A"{x2;else}data-sort="0" data-answer="{x2;v:question['questionanswer']}"{x2;endif}{x2;if:$favors[v:question['questionid']]} data-favor="1"{x2;endif}>
							<div class="page-ele margin mg1 nomgLR">
								<h5 class="bigtitle col-xs-12">{x2;v:qid}/{x2;$history['ehsetting']['papersetting']['questype'][v:questype]['number']} {x2;$questypes[v:question['questiontype']]['questype']}</h5>
								<div class="clear question">
                                    {x2;v:question['question']}
								</div>
								{x2;eval:$question = v:question}
								{x2;include:plugin_question}
							</div>
                            {x2;if:$questypes[v:question['questiontype']]['questsort']}
							<div class="page-ele margin mg1 nomgLR">
								<h5 class="bigtitle col-xs-12">我的答案</h5>
								<div>
									{x2;if:is_array($history['ehuseranswer'][v:question['questionid']])}{x2;eval: echo implode("",$history['ehuseranswer'][v:question['questionid']])}{x2;else}{x2;$history['ehuseranswer'][v:question['questionid']]}{x2;endif}
								</div>
							</div>
							{x2;endif}
							<div class="page-ele margin mg1 nomgLR">
								<h5 class="bigtitle col-xs-12">参考答案</h5>
								<div class="clear question">
									{x2;v:question['questionanswer']}
								</div>
							</div>
							<div class="page-ele margin mg1 nomgLR">
								<h5 class="bigtitle col-xs-12">解析</h5>
								<div class="clear question">
                                    {x2;if:v:question['questionintro']}
                                    {x2;v:question['questionintro']}
                                    {x2;else}
									本题暂无解析
                                    {x2;endif}
								</div>
							</div>
						</div>

						{x2;endtree}
						{x2;endif}
                        {x2;if:$history['ehquestion']['questionrows'][v:questype]}
						{x2;tree:$history['ehquestion']['questionrows'][v:questype],questionrows,qrid}
						{x2;tree:v:questionrows['data'],question,qid}
                        {x2;eval:v:innerquestionindex ++;}

						<div class="pages-box swiper-slide" data-questionid="{x2;v:question['questionid']}"{x2;if:$history['ehuseranswer'][v:question['questionid']] && !$questypes[v:question['questiontype']]['questsort']} data-useranswer="{x2;$history['ehuseranswer'][v:question['questionid']]}"{x2;endif} {x2;if:$questypes[v:question['questiontype']]['questsort']}data-sort="1" data-answer="A"{x2;else}data-sort="0" data-answer="{x2;v:question['questionanswer']}"{x2;endif}{x2;if:$favors[v:question['questionid']]} data-favor="1"{x2;endif}>
							<div class="page-ele margin mg1 nomgLR">
								<h5 class="bigtitle col-xs-12">{x2;v:qid}/{x2;$history['ehsetting']['papersetting']['questype'][v:questype]['number']} {x2;$questypes[v:questionrows['qrtype']]['questype']}（{x2;$questypes[v:question['questiontype']]['questype']}）</h5>
                                <div class="clear question">
                                    {x2;v:questionrows['qrquestion']}
								</div>
								<div class="clear question">
                                    {x2;v:question['question']}
								</div>
								{x2;eval:$question = v:question}
								{x2;include:plugin_question}
							</div>
                            {x2;if:$questypes[v:question['questiontype']]['questsort']}
							<div class="page-ele margin mg1 nomgLR">
								<h5 class="bigtitle col-xs-12">我的答案</h5>
								<div>
									{x2;if:is_array($history['ehuseranswer'][v:question['questionid']])}{x2;eval: echo implode("",$history['ehuseranswer'][v:question['questionid']])}{x2;else}{x2;$history['ehuseranswer'][v:question['questionid']]}{x2;endif}
								</div>
							</div>
                            {x2;endif}
							<div class="page-ele margin mg1 nomgLR">
								<h5 class="bigtitle col-xs-12">参考答案</h5>
								<div class="clear question">
                                    {x2;v:question['questionanswer']}
								</div>
							</div>
							<div class="page-ele margin mg1 nomgLR">
								<h5 class="bigtitle col-xs-12">解析</h5>
								<div class="clear question">
                                    {x2;if:v:question['questionintro']}
                                    {x2;v:question['questionintro']}
                                    {x2;else}
									本题暂无解析
                                    {x2;endif}
								</div>
							</div>
						</div>
						{x2;endtree}
						{x2;endtree}
                        {x2;endif}
						{x2;endif}
						{x2;endtree}
					</div>
				</div>
			</div>
			<div class="pages-content hide">
				<div class="pages-box">
					<div class="page-ele margin mg1 nomgLR">
						<div class="clear question">
                            {x2;eval:v:qtid = 0}
							{x2;tree:$history['ehsetting']['papersetting']['questypelite'],lite,lid}
                            {x2;eval:v:qtindex = 0}
                            {x2;eval:v:questype = v:key}
                            {x2;if:$history['ehquestion']['questions'][v:questype] || $history['ehquestion']['questionrows'][v:questype]}
							<h5 class="bigtitle col-xs-12">{x2;$questypes[v:questype]['questype']}</h5>
                            {x2;if:$history['ehquestion']['questions'][v:questype]}
							{x2;tree:$history['ehquestion']['questions'][v:questype],question,qid}
                            {x2;eval:v:qtid++}
                            {x2;eval:v:qtindex++}
							<label class="selectbox col-xs-2 questionindex" data-index="{x2;v:qtid}" data-questionid="{x2;v:question['questionid']}">
								<span class="selector{x2;if:$history['ehscorelist'][v:question['questionid']] != $history['ehsetting']['papersetting']['questype'][v:questype]['score']} sign{x2;endif}">{x2;v:qtindex}</span>
							</label>
                            {x2;endtree}
							{x2;endif}
                            {x2;if:$history['ehquestion']['questionrows'][v:questype]}
                            {x2;tree:$history['ehquestion']['questionrows'][v:questype],questionrows,qrid}
							{x2;tree:v:questionrows['data'],question,qid}
                            {x2;eval:v:qtid++}
                            {x2;eval:v:qtindex++}
							<label class="selectbox col-xs-2 questionindex" data-index="{x2;v:qtid}" data-questionid="{x2;v:question['questionid']}">
								<span class="selector{x2;if:$history['ehscorelist'][v:question['questionid']] != $history['ehsetting']['papersetting']['questype'][v:questype]['score']} sign{x2;endif}">{x2;v:qtindex}</span>
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
				questionPaper({subjectmodel,pointid:0,lastquestion:0});
			});
		</script>
    {x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}