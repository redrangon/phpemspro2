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
                <div class="text-center">用户登陆</div>
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
                        <img src="public/static/images/icon.jpg" class="img-circle" style="width: 2.1rem;margin-top: 0.5rem;"/>
                    </div>
                </div>
                <div class="page-form">
                    <form method="post" action="index.php?user-mobile-login">
                        <div class="form-group border">
                            <label class="control-label col-xs-3">用户名</label>
                            <div class="col-xs-9">
                                <input type="text" class="" name="args[username]" needle="needle" datatype="userName" msg="请输入登陆用户名" placeholder="请输入登陆用户名" autocomplete="off"/>
                            </div>
                        </div>
                        <div class="form-group border">
                            <label class="control-label col-xs-3">密码</label>
                            <div class="col-xs-9">
                                <input type="password" class="" name="args[userpassword]" needle="needle" datatype="password" msg="请输入登陆密码" placeholder="请输入登陆密码" autocomplete="off"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="controls">
                                <a class="pull-right ajax" data-page="yes" data-title="忘记密码" href="index.php?user-mobile-login-findpassword">忘记密码</a>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="controls">
                                <button class="btn btn-primary btn-block" type="submit">登录</button>
                            </div>
                        </div>
						{x2;if:\PHPEMSPRO\route::isWeixin()}
                        <div class="form-group">
                            <div class="controls">
                                <a href="index.php?wxlogin-mobile-index-getopenid" class="btn btn-success btn-block" type="submit">微信一键登录</a>
                            </div>
                        </div>
                        {x2;endif}
                        <div class="form-group">
                            <div class="text-center">
                                还没有注册帐号？
                                <a href="index.php?user-mobile-login-register" class="ajax" data-title="用户注册" data-page="yes">立即注册</a>
                                <input type="hidden" value="1" name="userlogin"/>
                            </div>
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