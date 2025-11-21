{x2;if:!$_userhash}
{x2;include:header}
<div class="pages">
    {x2;endif}
    {x2;if:$page <= 1}
    <div class="pages-tabs">
        <div class="pages-header">
            <a class="col-xs-2" href="javascript:history.back();">
                <div class="text-center">
                    <i class="fa fa-angle-left"></i>
                </div>
            </a>
            <a class="col-xs-8 active">
                <div class="text-center">{x2;$cat['catname']}</div>
            </a>
            <a class="col-xs-2">
                <div class="text-center">
                    <i class="fa fa-option-horizontal"></i>
                </div>
            </a>
        </div>
        <div class="pages-content nofooter" data-pageurl="index.php?lesson-mobile-category&catid={x2;$cat['catid']}" data-scroll="yes">
            <div class="pages-box nopadding">
                {x2;endif}
                {x2;tree:$lessons['data'],lesson,lid}
                <div class="page-ele{x2;if:v:lid == 1 && $page <= 1} margin mg1 nomgLR{x2;endif}">
                    <a data-page="yes" data-title="{x2;$cat['catname']}" href="index.php?lesson-mobile-lesson&lessonid={x2;v:lesson['lessonid']}" class="ajax">
                        <div class="col-xs-4 text-center padding pd2">
                            <img src="{x2;v:lesson['lessonthumb']}" class="img-round" style="width: 86%;"/>
                        </div>
                        <div class="col-xs-8">
                            <div class="title">{x2;v:lesson['lessonname']}</div>
                            <div class="intro">
                                {x2;realsubstring:v:lesson['lessonintro'],120}
                            </div>
                        </div>
                    </a>
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