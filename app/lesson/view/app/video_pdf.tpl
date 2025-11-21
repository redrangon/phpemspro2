{x2;include:header}
<body>
<div class="container-fluid pages-header">
	{x2;include:top}
	<div class="row-fluid nav">
		<div class="pep nav">
			<div class="col-xs-9">
				<ul class="list-unstyled list-inline">
					<li class="title">{x2;$lesson['lessonname']}</li>
				</ul>
			</div>
			<div class="col-xs-3 text-right">
				{x2;if:!$status}
				<a class="btn btn-primary" style="margin-top: 20px;" href="index.php?lesson-app-lesson-open&lessonid={x2;$lesson['lessonid']}"><i class="glyphicon glyphicon-yen"></i> 开通  </a>
				{x2;endif}
				<a class="btn btn-default" style="margin-top: 20px;" href="index.php?lesson"><i class="glyphicon glyphicon-chevron-left"></i> 返回  </a>
			</div>
		</div>
	</div>
</div>
<div class="container-fluid pages-content">
	<div class="row-fluid panels">
		<div class="pep panels">
			<div class="col-xs-8">
				<div id="videobox" class="videopage" style="overflow: hidden"></div>
				<div class="panel panel-default pagebox margin">
					<div class="panel-heading" style="overflow: hidden">
						<div class="col-xs-3">
							<a class="btn btn-default prevpage"><i class="glyphicon glyphicon-chevron-left"></i> 上一页</a>
						</div>
						<div class="col-xs-6 text-center">
							<span class="pdfpage">1</span>/<span class="allpage">1</span>
						</div>
						<div class="col-xs-3">
							<a class="btn btn-default pull-right nextpage">下一页 <i class="glyphicon glyphicon-chevron-right"></i></a>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xs-4 nopadding">
				<div class="panel panel-default pagebox border">
					<div class="panel-heading bold">
						课程列表
					</div>
					<div class="panel-body">
						<ul class="list-group" style="max-height: 750px;overflow-y: scroll;">
							{x2;tree:$videos,video,vid}
							<li class="list-group-item">
								<a class="{x2;if:$video['videoid'] == v:video['videoid']} playing{x2;endif}" href="index.php?lesson-app-lesson&lessonid={x2;$lesson['lessonid']}&videoid={x2;v:video['videoid']}">
									{x2;v:video['videoname']}
								</a>
							</li>
							{x2;endtree}
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="public/static/js/pdf/pdf.js"></script>
<script>
	$(function(){
		var loadingTask = pdfjsLib.getDocument('{x2;$video['videopath']}');
		var pdfpage = 1;
		function drawpage(pdf)
		{
			if(pdfpage >= 1 && pdfpage <= pdf._pdfInfo.numPages)
			{
				pdf.getPage(pdfpage).then(function (page) {
					var scale = 1080 / page.view[2];
					var viewport = page.getViewport({scale:scale});
					var canvas = ($('<canvas style="display: block;width: 100%;"></canvas>'))[0];
					$('#videobox').html(canvas);
					var context = canvas.getContext('2d');
					canvas.height = viewport.height;
					canvas.width = viewport.width;
					var renderContext = {
						canvasContext: context,
						viewport: viewport
					};
					page.render(renderContext);
					height = canvas.height*$('#videobox').width()/canvas.width;
					$('body').scrollTop(0);
					$('#videobox').css("height",height);
					$('.pdfpage').html(pdfpage);
					if(pdfpage == pdf._pdfInfo.numPages) {
						$.get('index.php?lesson-app-lesson-endstatus&videoid={x2;$video['videoid']}&userhash=' + Math.random());
					}
				});
			}
		}
		loadingTask.promise.then(function(pdf) {
			$('.allpage').html(pdf._pdfInfo.numPages);
			drawpage(pdf);
			$('.nextpage').on('click',function(){
				if(pdfpage >= 1 && pdfpage < pdf._pdfInfo.numPages)
				{
					pdfpage++;
					drawpage(pdf);
				}
			});
			$('.prevpage').on('click',function(){
				if(pdfpage > 1 && pdfpage <= pdf._pdfInfo.numPages)
				{
					pdfpage--;
					drawpage(pdf);
				}
			});
		});
	});
</script>
{x2;include:footer}
</body>
</html>