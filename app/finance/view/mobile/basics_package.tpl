{x2;if:!$_userhash}
{x2;include:header}
<div class="pages">
{x2;endif}
    <form action="index.php?finance-mobile-basics-openbasic" method="post">
        {x2;if:$page <= 1}
        <div class="pages-tabs">
            <div class="pages-header">
                <a class="col-xs-2" href="javascript:history.back();">
                    <div class="text-center">
                        <i class="fa fa-angle-left"></i>
                    </div>
                </a>
                <a class="col-xs-8 active">
                    <div class="text-center">开通套餐</div>
                </a>
                <a class="col-xs-2">
                    <div class="text-center">
                        <i class="fa fa-option-horizontal"></i>
                    </div>
                </a>
            </div>
            <div class="pages-content" data-pageurl="index.php?finance-mobile-basics-package&search[trid]={x2;$search['trid']}" data-scroll="yes">
                <div class="pages-box">
                    {x2;endif}
                    {x2;tree:$trainings['data'],training,trid}
                    {x2;if:v:training['prices']}
                    <div class="page-ele contin">
                        <h4 class="bigtitle">{x2;v:training['trname']}</h4>
                    </div>
                    <div class="page-ele clear">
                        {x2;tree:v:training['prices'],price,pid}
                        <label class="cartbox padding pd1">
                            <input class="packageradio" id="packageradio_{x2;v:basic['sid']}_{x2;v:key}" type="checkbox" name="packages[{x2;v:training['trid']}][{x2;v:key}]" value="{x2;v:key}" data-price="{x2;v:price['agentprice']}">
                            <div class="cart">
                                <div class="col-xs-8">
                                    <h5 class="title">{x2;v:price['name']}</h5>
                                    <p>
                                        {x2;v:price['time']}天 原价：{x2;v:price['price']}
                                    </p>
                                </div>
                                <div class="col-xs-4 text-warning price">
                                    ￥{x2;v:price['agentprice']}
                                </div>
                            </div>
                        </label>
                        {x2;endtree}
                    </div>
                    {x2;endif}
                    {x2;endtree}
                    {x2;if:$page <= 1}
                </div>
            </div>
            <div class="pages-footer">
                <div class="col-xs-6 text-center menu padding pd1">
                    <h4 class="text-danger">￥<b id="totalprice">0</b></h4>
                </div>
                <div class="col-xs-6 active menu padding pd1">
                    <button class="btn btn-primary btn-block" type="button">下一步</button>
                </div>
            </div>
        </div>
        <div class="pages-tabs">
            <div class="pages-header">
                <a class="col-xs-2" href="javascript:history.back();">
                    <div class="text-center">
                        <i class="fa fa-angle-left"></i>
                    </div>
                </a>
                <a class="col-xs-8 active">
                    <div class="text-center">开通套餐</div>
                </a>
                <a class="col-xs-2">
                    <div class="text-center">
                        <i class="fa fa-option-horizontal"></i>
                    </div>
                </a>
            </div>
            <div class="pages-content">
                <div class="pages-box">
                    <div class="page-form margin mg1 nomgLR">
                        <div class="form-group border">
                            <label class="control-label col-xs-3">手机号</label>
                            <div class="col-xs-9">
                                <input type="text" class="form-control" name="phonenumber" needle="needle" msg="请输入手机号" placeholder="请输入手机号"/>
                            </div>
                        </div>
                        <div class="form-group border">
                            <label class="control-label col-xs-3">姓名</label>
                            <div class="col-xs-9">
                                <input type="text" class="form-control" name="name" needle="needle" msg="请填写姓名" placeholder="请填写姓名"/>
                            </div>
                        </div>
                        <div class="form-group border">
                            <label class="control-label col-xs-3">邮箱</label>
                            <div class="col-xs-9">
                                <input type="text" class="form-control" name="email" needle="needle" msg="请填写邮箱" placeholder="请填写邮箱"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pages-footer">
                <div class="col-xs-6 text-center menu padding pd1">
                    <button class="btn btn-default btn-block" type="button">上一步</button>
                </div>
                <div class="col-xs-6 active menu padding pd1">
                    <button class="btn btn-primary btn-block" type="submit">提交</button>
                </div>
            </div>
        </div>
        <script>
            $(function(){
                function refreshprice()
                {
                    var price = 0;
                    $('.packageradio:checked').each(function(){
                        price = price + parseFloat($(this).data('price'));
                    });
                    $('#totalprice').html(price.toFixed(2));
                }
                $('.packageradio').on('click',function(){
                    setTimeout(refreshprice,50)
                });
                $('#openbasicbtn').on('click',function(){
                    $('#contentphonenumber').val($('#footerphonenumber').val());
                    $('#openbasicform').submit();
                });
            })
        </script>
        {x2;endif}
    </form>
{x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}