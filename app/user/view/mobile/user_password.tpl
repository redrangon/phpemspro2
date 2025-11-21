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
                <div class="text-center">修改密码</div>
            </a>
            <a class="col-xs-2">
                <div class="text-center">
                    <i class="fa fa-option-horizontal"></i>
                </div>
            </a>
        </div>
        <div class="pages-content nofooter" data-savehtml="yes">
            <div class="pages-box">
                <div class="page-ele clear">
                    <div class="text-center">
                        <img src="public/static/images/icon.jpg" class="img-circle margin mg2" style="width: 2.1rem;"/>
                    </div>
                </div>
                <div class="page-form">
                    <form method="post" action="index.php?user-mobile-user-password">
                        <div class="form-group border">
                            <label class="control-label col-xs-10-3">旧密码</label>
                            <div class="col-xs-7">
                                <input type="password" name="oldpassword" needle="needle" datatype="password" msg="请输入旧密码" placeholder="请输入旧密码"/>
                            </div>
                        </div>
                        <div class="form-group border">
                            <label class="control-label col-xs-10-3">新密码</label>
                            <div class="col-xs-7">
                                <input type="password" name="newpassword" id="newpassword" needle="needle" datatype="password" msg="请输入新密码" placeholder="请输入新密码"/>
                            </div>
                        </div>
                        <div class="form-group border">
                            <label class="control-label col-xs-10-3">重复密码</label>
                            <div class="col-xs-7">
                                <input type="password" equ="newpassword" name="reuserpassword" needle="needle" datatype="password" msg="两次输入密码必须一致" placeholder="请再次输入新密码"/>
                            </div>
                        </div>
                        <div class="form-group margin mg1">
                            <button class="btn btn-primary btn-block" type="submit">修改密码</button>
                            <input type="hidden" value="1" name="modifypassword"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}