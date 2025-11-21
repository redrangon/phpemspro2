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
                <div class="text-center">激活码激活</div>
            </a>
            <a class="col-xs-2">
                <div class="text-center">
                    <i class="fa fa-option-horizontal"></i>
                </div>
            </a>
        </div>
        <div class="pages-content nofooter" data-savehtml="yes">
            <div class="pages-box">
                <div class="page-form">
                    <form method="post" action="index.php?user-mobile-coupon-active">
                        <div class="form-group border">
                            <label class="control-label col-xs-3">激活码</label>
                            <div class="col-xs-9">
                                <input type="text" name="cpnsn" needle="needle" msg="请输入激活码" placeholder="请输入激活码"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="controls">
                                <button class="btn btn-primary btn-block" type="submit">立即激活</button>
                                <input type="hidden" value="1" name="active"/>
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