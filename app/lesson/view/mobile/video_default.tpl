{x2;if:!$_userhash}
{x2;include:header}
<div class="pages">
    {x2;endif}
    {x2;if:!$_userhash || !$videoid}
    <div class="pages-tabs" id="lesson-videos">
    {x2;endif}
        <div class="pages-header">
            <a class="col-xs-2" href="javascript:history.back();">
                <div class="text-center">
                    <i class="fa fa-angle-left"></i>
                </div>
            </a>
            <a class="col-xs-8 active">
                <div class="text-center">{x2;substring:$lesson['lessonname'],42}</div>
            </a>
            {x2;if:$status}
            <a class="col-xs-2">
                <div class="text-center">
                    <i class="fa fa-option-horizontal"></i>
                </div>
            </a>
            {x2;else}
            <a class="col-xs-2 ajax" data-title="购买课程" data-page="yes" href="index.php?lesson-mobile-lesson-open&lessonid={x2;$lesson['lessonid']}">
                <div class="text-center">
                    <i class="fa fa-shopping-cart"></i>
                </div>
            </a>
            {x2;endif}
        </div>
        <div class="pages-content nofooter">
            <div class="pages-box nopadding">
                <div id="videobox" class="page-video"></div>
            </div>
            <div class="pages-box nopadding">
                <div class="page-ele margin videos page-videolist">
                    <h5 class="bigtitle">{x2;$lesson['lessonname']}</h5>
                    <ul class="listmenu">
                        {x2;tree:$videos,video,vid}
                        <li class="small">
                            <a class="ajax{x2;if:$video['videoid'] == v:video['videoid']} playing{x2;endif}" href="index.php?lesson-mobile-lesson&lessonid={x2;$lesson['lessonid']}&videoid={x2;v:video['videoid']}" data-target="lesson-videos">
                                {x2;v:video['videoname']}
                                <i class="fa fa-play-circle pull-right"></i>
                            </a>
                        </li>
                        {x2;endtree}
                    </ul>
                </div>
            </div>
        </div>
        <script>
            $(function(){
                var playerElement = $("#videobox").empty();
                var options = {
                    source:'{x2;$video['videopath']}',
                    poster:'{x2;$video['videothumb']}',
                    controls:true,
                    loop:false,
                    volume:1,
                    width:window.screen.width + 'px',
                    height:window.screen.width * 0.6 + 'px',
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
                playerElement[0].appendChild(video);
                var player = videojs(video,options);
                {x2;if:$record['recrate']}
                player.currentTime({x2;$logs[$content['courseid']]['logprogress']});
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
                pep.recordVideo = setInterval(function(){
                    $.get('index.php?lesson-mobile-lesson-recordtime&videoid={x2;$video['videoid']}&time='+player.currentTime()+'&userhash='+Math.random());
                },20000);
                player.on('ended',function(){
                    $.get('index.php?lesson-mobile-lesson-endstatus&videoid={x2;$video['videoid']}&userhash='+Math.random(),function(){
                        //window.location.reload();
                    });
                })
                pep.allowpre = true;
                $('.page-videolist:first')[0].scrollTo({
                    top:$('.page-videolist .playing')[0].offsetTop - $("#videobox")[0].offsetHeight - $(".pages-header:first")[0].offsetHeight,
                    behavior:'smooth'
                });
                pep.injectEvent(function(){
                    clearInterval(pep.recordVideo);
                })
            });
        </script>
    {x2;if:!$_userhash || !$videoid}
    </div>
    {x2;endif}
    {x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}