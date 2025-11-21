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
            <div class="pages-box nopadding" style="position: relative">
                <div id="videobox" class="page-video" style="overflow-y: auto"></div>
                <div class="pdfbtnbox">
                    <a class="pdfbtn fullpage"><i class="fa fa-expand"></i></a>
                    <a class="pdfbtn prevpage"><i class="fa fa-angle-up"></i></a>
                    <a class="pdfbtn nextpage"><i class="fa fa-angle-down"></i></a>
                </div>
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
                            $('.pdfpage').html(pdfpage);
                            if(pdfpage == pdf._pdfInfo.numPages) {
                                $.get('index.php?lesson-mobile-lesson-endstatus&videoid={x2;$video['videoid']}&userhash=' + Math.random());
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
                    $('.fullpage').on('click',function(){
                        if($('.page-videolist').hasClass('hide'))
                        {
                            $('.page-videolist').removeClass('hide');
                            $('#videobox').css("height", "5.6rem");
                            $(this).html('<i class="fa fa-expand"></i>');
                        }
                        else {
                            $('.page-videolist').addClass('hide');
                            $('#videobox').css("height", "auto");
                            $(this).html('<i class="fa fa-compress"></i>');
                        }
                    });
                });
            });
        </script>
    {x2;if:!$_userhash || !$videoid}
    </div>
    {x2;endif}
    {x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}