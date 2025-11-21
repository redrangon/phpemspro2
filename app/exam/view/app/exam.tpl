{x2;include:header}
<body>
{x2;include:nav}
<div class="container-fluid pages-content">
	<div class="row-fluid panels">
		<div class="pep panels">
			<div class="col-xs-12 nopadding">
				<div class="panel panel-default pagebox border">
					<div class="panel-heading bold">
						正式考试
						<a class="btn btn-primary pull-right" href="index.php?exam-app-exam-history">考试记录</a>
					</div>
					<div class="panel-body">
						<h2 class="text-center">考试须知</h2>
						<div class="pagebox padding contenttext">
							<p>1．考生应按规定的时间入场，开始考试后15分钟禁止迟到考生进入考场。
							</p>
							<p>2．考生入场时须主动出示《准考证》以及有效身份证件(身份证、军人、武警人员证件、未成年人的户口本、公安户籍部门开具的《身份证》号码证明、护照或者港、澳、台同胞证件)，接受考试工作人员的核验，并按要求在“考生花名册”上签自己的姓名。
							</p>
							<p>3．考生只准携带必要的文具入场,如铅笔、签字笔、毛笔、水粉水彩颜料等，具体要求见招考简章。禁止携带任何已完成作品以及各种无线通信工具(如寻呼机、移动电话)等物品。如发现考生携带以上禁带物品，考生将作为违纪处理，取消该次考试成绩。考场内不得擅自相互借用文具。
							</p>
							<p>4．考生入场后按号入座，将本人《准考证》以及有效身份证件放在课桌上，以便核验。
							</p>
							<p>5．考生答题前应认真填写试卷及答题纸上的姓名、准考证号等栏目并粘贴带有考生个人信息的条形码。凡不按要求填写或字迹不清、无法辨认的，试卷及答题纸一律无效。责任由考生自付。
							</p>
							<p>6．开考后，考生不得中途退场。如因身体不适要求中途退场，须征得监考人员及考点主考批准，并在退场前将试卷、答题纸如数上交。
							</p>
							<p>7．考生遇试卷分发错误或试题字迹不清等情况应及时要求更换；涉及试题内容的疑问，不得向监考人员询问。
							</p>
							<p>8．考生在考场内必须严格遵守考场纪律，对于违反考场规定、不服从监考人员管理和舞弊者，取消当次考试成绩。
							</p>
							<p>9．考试结束铃声响时，考生要立即停止答题，并将试卷、答题纸按要求整理好，翻放在桌上，待监考人员收齐后方可离开考场。任何考生不准携带试卷、答题纸离开考场。离开考场后不准在考场附近逗留和交谈。
							</p>
							<p>10．考生应自觉服从监考人员管理，不得以任何理由妨碍监考人员进行正常工作。对扰乱考场秩序、恐吓、威胁监考人员的考生将交公安机关追究其责任，并通知其所在单位。
							</p>
						</div>
					</div>
				</div>
				{x2;if:$intime}
				{x2;if:$maxnumber}
				{x2;if:$examsession}
				<div class="panel panel-default pagebox border">
					<div class="panel-heading bold">
						意外续考

					</div>
					<div class="panel-body">
						<ul class="list-group">
							<li class="list-group-item">
								<span class="pull-right">
									<a style="margin-top: 15px;" class="btn btn-primary ajax" href="index.php?exam-app-exam-recover">继续考试</a>
									<a style="margin-top: 15px;" class="btn btn-danger confirm" msg="确定要放弃续考吗？" href="index.php?exam-app-exam-cancel">放弃续考</a>
								</span>
								<h4>
									{x2;$examsession['name']}
								</h4>
								<span class="intros">总分：{x2;$examsession['setting']['papersetting']['score']}分</span>
								<span class="intros">时间：{x2;$examsession['setting']['papersetting']['papertime']}分钟</span>
								<span class="intros">及格分：{x2;$examsession['setting']['papersetting']['passscore']}分</span>
								<span class="intros">开始时间：{x2;date:$examsession['starttime'],'Y-m-d H:i:s'}</span>
							</li>
						</ul>
					</div>
				</div>
				{x2;else}
				{x2;if:$basic['basicexam']['selectrule']}
				<p class="text-center pagebox">
					<a class="btn btn-primary ajax" onclick="javascript:clearStorage();" href="index.php?exam-app-exam-selectquestions">开始考试</a>
				</p>
				{x2;else}
				<div class="panel panel-default pagebox border">
					<div class="panel-heading bold">
						试卷列表
					</div>
					<div class="panel-body">
						<ul class="list-group">
                            {x2;tree:$papers,paper,sid}
							<li class="list-group-item">
								<span class="pull-right">
									<a style="margin-top: 25px;" class="btn btn-primary ajax pull-right" onclick="javascript:clearStorage();" href="index.php?exam-app-exam-selectquestions&paperid={x2;v:paper['paperid']}">开始考试</a>
								</span>
								<a class="ajax" href="index.php?exam-app-exam-selectquestions&paperid={x2;v:paper['paperid']}">
                                    {x2;v:paper['papername']}
								</a>
								<br />
								<span class="intros">总分：{x2;v:paper['papersetting']['score']}分</span>
								<span class="intros">时间：{x2;v:paper['papersetting']['papertime']}分钟</span>
								<span class="intros">及格分：{x2;v:paper['papersetting']['passscore']}分</span>
							</li>
                            {x2;endtree}
						</ul>
					</div>
				</div>
				{x2;endif}
				{x2;endif}
				{x2;else}
				<p class="alert alert-danger text-center">
					您的考试次数已经使用完毕！
				</p>
				{x2;endif}
				{x2;else}
				<p class="alert alert-danger text-center">
					考试开放时间为 {x2;date:$basic['basicexam']['opentime']['start'],'Y-m-d H:i'} - {x2;date:$basic['basicexam']['opentime']['end'],'Y-m-d H:i'}，请等待考试开启！
				</p>
				{x2;endif}
			</div>
		</div>
	</div>
</div>
{x2;if:!$intime}
<script>
	setInterval(function(){
		submitAjax({url:'index.php?exam-app-exam-isintime'});
	},10000)
</script>
{x2;endif}
{x2;include:footer}
</body>
</html>