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
                <div class="text-center">实名认证</div>
            </a>
            <a class="col-xs-2"></a>
        </div>
        <div class="pages-content nofooter">
            {x2;if:$_user['userverify']}
            <div class="pages-box">
                <div class="page-ele clear margin mg1">
                    <p class="alert alert-success">
                        您已通过实名认证，如果需要修改认证信息，请与管理员联系！
                    </p>
                </div>
            </div>
            <div class="pages-box">
                <div class="page-form">
                    {x2;tree:$forms,form,fid}
                    <div class="form-group border">
                        <label class="control-label col-xs-4">{x2;v:form['title']}：</label>
                        <div class="col-xs-8">
                            {x2;v:form['html']}
                        </div>
                    </div>
                    {x2;endtree}
                </div>
            </div>
            {x2;else}
            <div class="pages-box">
                <div class="page-ele clear margin mg1">
                    <p class="alert alert-warning">您尚未通过实名认证，不能参加带有人脸识别认证的考试，请补全您的信息并通过管理员审核！管理员审核后，您的信息将不能更改！</p>
                </div>
            </div>
            <div class="pages-box">
                <div class="page-form">
                    <form method="post" action="index.php?user-mobile-verify">
                        {x2;tree:$forms,form,fid}
                        <div class="form-group border">
                            <label class="control-label col-xs-4">{x2;v:form['title']}：</label>
                            <div class="col-xs-8">
                                {x2;v:form['html']}
                            </div>
                        </div>
                        {x2;endtree}
                        <div class="form-group">
                            <div class="controls">
                                <button class="btn btn-primary btn-block" type="submit">提交实名信息</button>
                                <input type="hidden" name="modifyuser" value="1"/>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            {x2;endif}
        </div>
    </div>
    {x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}