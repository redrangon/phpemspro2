{x2;if:!$_userhash}
{x2;include:header}
<div class="pages">
    {x2;endif}
    <div class="pages-tabs">
        <div class="pages-header">
            <a class="col-xs-2" href="javascript:history.back();">
                <div class="text-center">
                    <i class="fa fa-angle-left"></i>
                </div>
            </a>
            <a class="col-xs-8 active">
                <div class="text-center">开通课程</div>
            </a>
            <a class="col-xs-2">
                <div class="text-center">
                    <i class="fa fa-option-horizontal"></i>
                </div>
            </a>
        </div>
        <div class="pages-content" data-savehtml="yes">
            <div class="pages-box nopadding">
                <div class="page-ele">
                    <ul class="listmenu">
                        {x2;tree:$trainings,training,tid}
                        <li><a href="index.php?finance-mobile-basics-basics&search[trid]={x2;v:training['trid']}" class="ajax">{x2;v:training['trname']} <i class="fa fa-menu-right pull-right"></i></a></li>
                        {x2;endtree}
                    </ul>
                </div>
            </div>
        </div>
    </div>
    {x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}