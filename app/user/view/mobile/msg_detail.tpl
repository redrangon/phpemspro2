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
                <div class="text-center">站内信</div>
            </a>
            <a class="col-xs-2"></a>
        </div>
        <div class="pages-content nofooter" data-refresh="yes">
            <div class="pages-box">
                <div class="page-ele clear margin mg1 nomgLR">
                    <div class="title text-center">{x2;$msg['msgtitle']}</div>
                    <div class="news padding pd2">
                        {x2;$msg['msgcontent']}
                    </div>
                </div>
                {x2;if:$msg['msgurl']}
                <div class="page-ele contin">
                    <h4 class="bigtitle">相关详情</h4>
                </div>
                <div class="page-ele contin autoloaditem" autoload="{x2;$msg['msgurl']}">
                </div>
                {x2;endif}
                <div class="page-ele clear margin mg2">
                    <a class="btn btn-primary btn-block confirm" message="确定要删除吗？" href="index.php?user-mobile-msg-del&msrid={x2;$msg['msrid']}">删除</a>
                </div>
            </div>
        </div>
    </div>
    {x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}