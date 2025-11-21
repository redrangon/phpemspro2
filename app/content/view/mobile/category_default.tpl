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
                <div class="text-center">{x2;$cat['catname']}</div>
            </a>
            <a class="col-xs-2">
                <div class="text-center">
                    <i class="fa fa-option-horizontal"></i>
                </div>
            </a>
        </div>
        <div class="pages-content{x2;if:!$contents['pages']} nofooter{x2;endif}">
            <div class="pages-box nopadding">
                {x2;tree:$contents['data'],content,cid}
                <div class="page-ele contin">
                    <a data-page="yes" data-title="{x2;$cat['catname']}" href="index.php?content-mobile-content&contentid={x2;v:content['contentid']}" class="ajax">
                        <h5 class="title">
                            {x2;v:content['contenttitle']}
                        </h5>
                        {x2;if:v:content['contentintro']}
                        <div class="intro">
                            {x2;v:content['contentintro']}
                        </div>
                        {x2;endif}
                        <div class="time">
                            {x2;v:content['contenttime']}
                        </div>
                    </a>
                </div>
                {x2;endtree}
            </div>
        </div>
        {x2;if:$contents['pages']}
        <div class="pages-footer">
            {x2;$contents['pages']}
        </div>
        {x2;endif}
    </div>
    {x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}