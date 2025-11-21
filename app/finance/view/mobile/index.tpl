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
                <div class="text-center">代理平台</div>
            </a>
            <a class="col-xs-2">
                <div class="text-center">
                    <i class="fa fa-option-horizontal"></i>
                </div>
            </a>
        </div>
        <div class="pages-content">
            <div class="pages-box nopadding">
                <div class="page-ele margin mg1 nomgLR">
                    <div class="col-xs-2">&nbsp;</div>
                    <div class="col-xs-3 padding pd1">
                        <img class="img-circle" src="public/static/images/icon.jpg" style="width: 100%;"/>
                    </div>
                    <div class="col-xs-5 padding pd1">
                        <div class="title">余额：{x2;$_user['usercoin']}</div>
                        <div class="intro">
                            账号：{x2;$_user['username']}
                        </div>
                    </div>
                    <div class="col-xs-2"></div>
                </div>
                <div class="page-ele margin mg1 nomgLR">
                    <ul class="listmenu">
                        <li><a href="index.php?finance-mobile-basics" class="ajax"><i class="fa fa-tasks"></i> 开通课程 <i class="fa fa-menu-right pull-right"></i></a></li>
                        <li><a href="index.php?finance-mobile-basics-training" class="ajax"><i class="fa fa-gift"></i> 开通套餐 <i class="fa fa-menu-right pull-right"></i></a></li>
                        <li><a href="index.php?finance-mobile-orders" class="ajax"><i class="fa fa-credit-card"></i> 订单管理 <i class="fa fa-menu-right pull-right"></i></a></li>
                        <li class="hide"><a href="index.php?finance-mobile-records" class="ajax"><i class="fa fa-usd"></i> 充值记录 <i class="fa fa-menu-right pull-right"></i></a></li>
                        <li><a href="index.php?finance-mobile-records-recharge" class="ajax"><i class="fa fa-comments-dollar"></i> 我要充值 <i class="fa fa-menu-right pull-right"></i></a></li>
                    </ul>
                </div>
                {x2;if:$_user['userid']}
                <div class="page-ele clear margin mg2">
                    <p class="col-xs-12">
                        <a class="btn btn-primary btn-block ajax" href="index.php?user-mobile-login-logout">退出登录</a>
                    </p>
                </div>
                {x2;endif}
            </div>
        </div>
    </div>
    {x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}