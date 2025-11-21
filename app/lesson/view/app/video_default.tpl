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
				<div id="videobox" class="videopage" style="height: 600px;"></div>
				<div class="panel panel-default pagebox margin">
					<div class="panel-heading">
						<a class="btn btn-primary" onclick="javascript:$(this).addClass('btn-primary').removeClass('btn-default').siblings().removeClass('btn-primary').addClass('btn-default');$('#videolists').removeClass('hide');$('#videotext').addClass('hide');"><i class="glyphicon glyphicon-th-list"></i> 视频简介</a>
						<a class="btn btn-default" onclick="javascript:$(this).addClass('btn-primary').removeClass('btn-default').siblings().removeClass('btn-primary').addClass('btn-default');$('#videolists').addClass('hide');$('#videotext').removeClass('hide');"><i class="glyphicon glyphicon-list-alt"></i> 课程大纲</a>
					</div>
					<div class="panel-body pagebox padding">
						<div class="col-xs-12 clear" id="videolists">
                            {x2;$video['videointro']}
						</div>
						<div class="col-xs-12 clear hide" id="videotext">
							{x2;realhtml:$lesson['lessontext']}
						</div>
					</div>
				</div>
			</div>
			<div class="col-xs-4 nopadding">
				<div class="panel panel-default pagebox border">
					<div class="panel-heading bold">
                        视频列表
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
<script>
    $(function(){
		var options = {
			source:'{x2;$video['videopath']}',
			poster:'{x2;$video['videopthumb']}',
			controls:true,
			loop:false,
			volume:1,
			width:'900px',
			height: '600px',
			language:'zh-CN',
			disablePictureInPicture:true,
			children:{
				"mediaLoader":true,
				"posterImage":true,
				"titleBar":true,
				"textTrackDisplay":true,
				"loadingSpinner":true,
				"bigPlayButton":true,
				"liveTracker":true,
				"controlBar":{
					progressControl:true
				},
				"errorDisplay":true,
				"textTrackSettings":true,
				"resizeManager":true
			}
		};
		var source = document.createElement('source');
		source.setAttribute('src',options.source);
		var video = document.createElement('video');
		video.setAttribute('class', "video-js vjs-default-skin");
		video.setAttribute('webkit-playsinline', 'true');
		video.setAttribute('playsinline', 'true');
		video.appendChild(source);
		document.getElementById('videobox').appendChild(video);
		var player = videojs(video,options);
		{x2;if:$record['recrate']}
		player.currentTime({x2;$record['recrate']});
		{x2;endif}
		{x2;if:$lesson['lessonprogress']}
		var ctime = 0;
		var stime = 0;
		player.on('timeupdate',function(){
			var time = parseInt(player.currentTime());
			ctime = time;
			if(stime == 0)
			{
				stime = ctime;
			}
			else
			{
				if(ctime - stime > 1 || ctime - stime < -1)
				{
					player.currentTime(stime);
					return ;
				}
				else
				{
					stime = ctime;
				}
			}
		})
		{x2;endif}
		$.recordVideo = setInterval(function(){
			$.get('index.php?lesson-app-lesson-recordtime&videoid={x2;$video['videoid']}&time='+player.currentTime()+'&userhash='+Math.random());
		},20000);
		player.on('ended',function(){
			$.get('index.php?lesson-app-lesson-endstatus&videoid={x2;$video['videoid']}&userhash='+Math.random(),function(){
				window.location.reload();
			});
		})
    });
</script>
{x2;include:footer}
</body>
</html>