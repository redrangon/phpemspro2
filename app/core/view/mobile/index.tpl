{x2;if:!$_userhash}
{x2;include:header}
<div class="pages">
    {x2;endif}
    <div class="pages-tabs">
        <div class="pages-header">
            <div class="text-center">
                发现
            </div>
        </div>
        <div class="pages-content">
            <div class="pages-swiper">
                <div class="swiper-container">
                    <div class="swiper-wrapper">
                        {x2;tree:$carousels['mobile']['data'],content,cid}
                        <div class="swiper-slide">
                            <a href="index.php?content-mobile-content&contentid={x2;v:content['contentid']}" title="{x2;v:content['contenttitle']}" class="ajax">
                                <img src="{x2;v:content['contentthumb']}" style="width:100%">
                            </a>
                        </div>
                        {x2;endif}
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
            <div class="pages-box margin mg1 nomgLRT">
                <div class="page-ele contin">
                    <a href="index.php?content-mobile-category&catid=2" class="ajax" data-page="yes" data-title="考试公告"><h4 class="bigtitle">考试公告</h4></a>
                </div>
                {x2;tree:$contents[2]['data'],content,cid}
                <div class="page-ele contin">
                    <a data-page="yes" data-title="考试公告" href="index.php?content-mobile-content&contentid={x2;v:content['contentid']}" class="ajax">
                        <div>
                            <img src="{x2;v:content['contentthumb']}" style="width: 100%;"/>
                        </div>
                        <div class="title">
                            {x2;v:content['contenttitle']}
                        </div>
                        <div class="intro">
                            {x2;v:content['contentintro']}
                        </div>
                        <div class="time">
                            {x2;v:content['contenttime']}
                        </div>
                    </a>
                </div>
                {x2;endif}
                <div class="page-ele contin">
                    <a data-page="yes" data-title="考试公告" href="index.php?content-mobile-category&catid=2" class="ajax">
                        <p class="text-center padding pd3">更多</p>
                    </a>
                </div>
            </div>
            <div class="pages-box margin mg1 nomgLRT">
                <div class="page-ele contin">
                    <a href="index.php?content-mobile-category&catid=3" class="ajax" data-page="yes" data-title="报考指南"><h4 class="bigtitle">报考指南</h4></a>
                </div>
                {x2;tree:$contents[3]['data'],content,cid}
                <div class="page-ele contin">
                    <a data-page="yes" data-title="报考指南" href="index.php?content-mobile-content&contentid={x2;v:content['contentid']}" class="ajax">
                        <div>
                            <img src="{x2;v:content['contentthumb']}" style="width: 100%;"/>
                        </div>
                        <div class="title">
                            {x2;v:content['contenttitle']}
                        </div>
                        <div class="intro">
                            {x2;v:content['contentintro']}
                        </div>
                        <div class="time">
                            {x2;v:content['contenttime']}
                        </div>
                    </a>
                </div>
                {x2;endif}
                <div class="page-ele contin">
                    <a data-page="yes" data-title="报考指南" href="index.php?content-mobile-category&catid=3" class="ajax">
                        <p class="text-center padding pd3">更多</p>
                    </a>
                </div>
            </div>
        </div>
        <div class="pages-footer padding pd2 nopdLR">
            <a class="col-xs-3 active menu">
                <div class="text-center">
                    <span class="fa fa-compass"></span>
                    <br />发现
                </div>
            </a>
            <a class="col-xs-3 menu">
                <div class="text-center">
                    <span class="fa fa-play-circle"></span>
                    <br />课程
                </div>
            </a>
            <a class="col-xs-3 menu">
                <div class="text-center">
                    <span class="fa fa-file-signature"></span>
                    <br />题库
                </div>
            </a>
            <a class="col-xs-3 menu">
                <div class="text-center">
                    <span class="fa fa-user"></span>
                    <br />我的
                </div>
            </a>
        </div>
    </div>
    <div class="pages-tabs">
        <div class="pages-header">
            <div class="text-center">
                课程
            </div>
        </div>
        <div class="pages-content">
            <div class="pages-box">
                <div class="page-ele">
                    <h4 class="bigtitle">课程分类</h4>
                </div>
                {x2;tree:$lessoncats,cat,cid}
                <div class="page-ele contin">
                    <a href="index.php?lesson-mobile-category&catid={x2;v:cat['catid']}" class="ajax" data-page="yes" data-title="{x2;v:cat['catname']}">
                        <div class="col-xs-4 padding pd2">
                            <img src="{x2;v:cat['catthumb']}" class="img-round" style="width: 100%;"/>
                        </div>
                        <div class="col-xs-8">
                            <div class="title">{x2;v:cat['catname']}</div>
                            <div class="intro">
                                {x2;realhtml:v:cat['catintro']}
                            </div>
                        </div>
                    </a>
                </div>
                {x2;endtree}
            </div>
            <div class="pages-box margin mg1">
                <div class="page-ele">
                    <h4 class="bigtitle">最新课程</h4>
                </div>
                {x2;tree:$lessons['data'],lesson,lid}
                <div class="page-ele contin">
                    <a href="index.php?lesson-mobile-lesson&lessonid={x2;v:lesson['lessonid']}" class="ajax" data-page="yes" data-title="{x2;substring:v:lesson['lessonname'],24}">
                        <div class="col-xs-4 padding pd2">
                            <img src="{x2;v:lesson['lessonthumb']}" class="img-round" style="width: 86%;"/>
                        </div>
                        <div class="col-xs-8">
                            <div class="title">{x2;v:lesson['lessonname']}</div>
                            <div class="intro">
                                {x2;realhtml:v:lesson['lessonintro']}
                            </div>
                        </div>
                    </a>
                </div>
                {x2;endtree}
            </div>

        </div>
        <div class="pages-footer padding pd2 nopdLR">
            <a class="col-xs-3 menu">
                <div class="text-center">
                    <span class="fa fa-compass"></span>
                    <br />发现
                </div>
            </a>
            <a class="col-xs-3 menu active">
                <div class="text-center">
                    <span class="fa fa-play-circle"></span>
                    <br />课程
                </div>
            </a>
            <a class="col-xs-3 menu">
                <div class="text-center">
                    <span class="fa fa-file-signature"></span>
                    <br />题库
                </div>
            </a>
            <a class="col-xs-3 menu">
                <div class="text-center">
                    <span class="fa fa-user"></span>
                    <br />我的
                </div>
            </a>
        </div>
	</div>        
    <div class="pages-tabs">
        <div class="pages-header">
            <div class="text-center">
                题库
            </div>
        </div>
        <div class="pages-content">
            {x2;tree:$trainings,training,tid}
            <div class="pages-box margin mg1 nomgLRT">
                <div class="page-ele">
                    <h4 class="bigtitle">{x2;v:training['trname']}</h4>
                </div>
                {x2;tree:$subjects[v:training['trid']],subject,sid}
                {x2;tree:$basics[v:subject['subjectid']]['data'],basic,bid}
                <div class="page-ele contin">
                    <a data-page="yes" data-title="{x2;v:basic['basic']}" href="index.php?exam-mobile-ajax-setcurrentbasic&subjectid={x2;v:subject['subjectid']}&basicid={x2;v:basic['basicid']}" class="ajax">
                        <div class="col-xs-4 padding pd2">
                            <img src="{x2;v:basic['basicthumb']}" class="img-round" style="width: 100%;"/>
                        </div>
                        <div class="col-xs-8">
                            <div class="title">{x2;v:basic['basic']}</div>
                            <div class="intro">
                                {x2;v:basic['basicdescribe']}
                            </div>
                        </div>
                    </a>
                </div>
                {x2;endtree}
                {x2;endtree}
            </div>
            {x2;endtree}
        </div>
        <div class="pages-footer">
            <a class="col-xs-3 menu">
                <div class="text-center">
                    <span class="fa fa-compass"></span>
                    <br />发现
                </div>
            </a>
            <a class="col-xs-3 menu">
                <div class="text-center">
                    <span class="fa fa-play-circle"></span>
                    <br />课程
                </div>
            </a>
            <a class="col-xs-3 menu active">
                <div class="text-center">
                    <span class="fa fa-file-signature"></span>
                    <br />题库
                </div>
            </a>
            <a class="col-xs-3 menu">
                <div class="text-center">
                    <span class="fa fa-user"></span>
                    <br />我的
                </div>
            </a>
        </div>
    </div>
    <div class="pages-tabs">
        <div class="pages-header">
            <div class="text-center">
                我的
            </div>
        </div>
        <div class="pages-content">
            <div class="pages-box nopadding">
                {x2;if:$_user['userid']}
                <div class="page-ele">
                    <div class="col-xs-1">&nbsp;</div>
                    <div class="col-xs-3 padding pd2">
                        <img class="img-circle" src="public/static/images/icon.jpg" style="width: 100%;"/>
                    </div>
                    <div class="col-xs-7 padding pd2">
                        <div class="title">{x2;if:$_user['userrealname']}{x2;$_user['userrealname']}{x2;else}未设置{x2;endif}</div>
                        <div class="intro">
                            账号：{x2;$_user['username']}
                        </div>
                    </div>
                    <div class="col-xs-1">&nbsp;</div>
                </div>
                {x2;else}
                <div class="page-ele margin clear">
                    <div class="col-xs-4">&nbsp;</div>
                    <div class="col-xs-4 text-center padding pd2">
                        <a class="btn btn-primary btn-block ajax" href="index.php?user-mobile-login">登录/注册</a>
                    </div>
                    <div class="col-xs-4">&nbsp;</div>
                </div>
                {x2;endif}
                <div class="page-ele margin mg1 nomgLR">
                    <ul class="listmenu">
                        {x2;if:$_user['usergroupcode'] == 'user'}
                        <li><a href="index.php?user-mobile-verify" class="ajax"><i class="fa fa-camera"></i> 实名认证 <i class="fa fa-angle-right pull-right"></i></a></li>
                        {x2;endif}
                        <li><a href="index.php?user-mobile-coupon" class="ajax"><i class="fa fa-bolt"></i> 激活码激活 <i class="fa fa-angle-right pull-right"></i></a></li>
                        <li><a href="index.php?lesson-mobile" class="ajax" data-page="yes" data-title="我的课程"><i class="fa fa-play-circle"></i> 我的课程 <i class="fa fa-angle-right pull-right"></i></a></li>
                        <li><a href="index.php?exam-mobile" class="ajax" data-page="yes" data-title="我的题库"><i class="fa fa-file-signature"></i> 我的考场 <i class="fa fa-angle-right pull-right"></i></a></li>
                        <li><a href="index.php?user-mobile-msg" class="ajax"><i class="fa fa-envelope"></i> 站内信{x2;if:$_user['userid']} <span class="badge autoloaditem" autoload="index.php?user-mobile-msg-getnewmsgnumber">0</span>{x2;endif} <i class="fa fa-angle-right pull-right"></i></a></li>
                        <li><a href="index.php?user-mobile-orders" class="ajax"><i class="fa fa-credit-card"></i> 我的订单 <i class="fa fa-angle-right pull-right"></i></a></li>
                        <li><a href="index.php?user-mobile-user-info" class="ajax"><i class="fa fa-user"></i> 个人信息 <i class="fa fa-angle-right pull-right"></i></a></li>
                        <li><a href="index.php?finance-mobile" class="ajax"><i class="fa fa-comments-dollar"></i> 代理平台 <i class="fa fa-angle-right pull-right"></i></a></li>
                        <li><a href="index.php?user-mobile-user-password" class="ajax"><i class="fa fa-cog"></i> 修改密码 <i class="fa fa-angle-right pull-right"></i></a></li>
                    </ul>
                </div>
                {x2;if:$_user['userid']}
                <div class="page-ele clear padding pd2">
                    <a class="btn btn-primary btn-block ajax" href="index.php?user-mobile-login-logout">退出登录</a>
                </div>
                {x2;endif}
            </div>
        </div>
        <div class="pages-footer padding pd2 nopdLR">
            <a class="col-xs-3 menu">
                <div class="text-center">
                    <span class="fa fa-compass"></span>
                    <br />发现
                </div>
            </a>
            <a class="col-xs-3 menu">
                <div class="text-center">
                    <span class="fa fa-play-circle"></span>
                    <br />课程
                </div>
            </a>
            <a class="col-xs-3 menu">
                <div class="text-center">
                    <span class="fa fa-file-signature"></span>
                    <br />题库
                </div>
            </a>
            <a class="col-xs-3 menu active">
                <div class="text-center">
                    <span class="fa fa-user"></span>
                    <br />我的
                </div>
            </a>
        </div>
    </div>
    <script>
        $(function(){
            var mySwiper = new Swiper('.swiper-container',{
                "pagination":".swiper-pagination",
                "loop":false,
                "autoplay": 0,
                "observer":true,
                "observeParents":true
            });
            localStorage.removeItem("prepages");
        });
    </script>
    {x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}