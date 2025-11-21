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
			<div class="pages-content nofooter">
				<div class="pages-box">
					<div class="page-ele margin mg1 nomgLR">
						<div class="scorearea">
							<div class="col-xs-6">
								<span class="score">{x2;eval: echo intval($history['ehscore'])}</span>
							</div>
							<div class="col-xs-6 padding pd2">
								<p class="intro">总分：{x2;$history['ehsetting']['papersetting']['score']}分</p>
								<p class="intro">合格分数：{x2;$history['ehsetting']['papersetting']['passscore']}分</p>
								<p class="intro">用时：{x2;eval: echo ceil($history['ehtime']/60)}分</p>
							</div>
						</div>
						{x2;if:$history['ehispass']}
						<p class="title text-center text-success">恭喜您通过了本次考试</p>
						{x2;else}
						<p class="title text-center text-danger">很遗憾，您未通过本次考试</p>
						{x2;endif}
						<div class="text-center padding pd3">
							<a href="javascript:history.back();" class="btn btn-primary btn-circle long">
                                返回
                            </a>
						</div>
					</div>
					<div class="page-ele">
						<div class="bigtitle">得分分析</div>
						<div class="news padding pd1">
							<table class="table table-bordered">
								<tr class="info">
									<th>题型</th>
									<th>题数</th>
									<th>分数</th>
									<th>答对数</th>
									<th>得分</th>
								</tr>
                                {x2;tree:$number,num,nid}
                                {x2;if:v:num}
								<tr>
									<td>{x2;$questypes[v:key]['questype']}</td>
									<td>{x2;v:num}</td>
									<td>{x2;eval: echo number_format(v:num*$history['ehsetting']['papersetting']['questype'][v:key]['score'],1)}</td>
									<td>{x2;$right[v:key]}</td>
									<td>{x2;eval: echo number_format($score[v:key],1)}</td>
								</tr>
                                {x2;endif}
                                {x2;endtree}
							</table>
						</div>
                    </div>
				</div>
			</div>
		</div>
    {x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}