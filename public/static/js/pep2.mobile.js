jQuery.cookie = function(key, value, options) {
    if (arguments.length > 1 && String(value) !== "[object Object]") {
        options = jQuery.extend({},
        options);
        if (value === null || value === undefined) {
            options.expires = -1;
        }
        if (typeof options.expires === 'number') {
            var days = options.expires,
            t = options.expires = new Date();
            t.setDate(t.getDate() + days);
        }
        value = String(value);
        return (document.cookie = [encodeURIComponent(key), '=', options.raw ? value: encodeURIComponent(value), options.expires ? '; expires=' + options.expires.toUTCString() : '', options.path ? '; path=' + options.path: '', options.domain ? '; domain=' + options.domain: '', options.secure ? '; secure': ''].join(''));
    }
    options = value || {};
    var result,
    decode = options.raw ?
    function(s) {
        return s;
    }: decodeURIComponent;
    return (result = new RegExp('(?:^|; )' + encodeURIComponent(key) + '=([^;]*)').exec(document.cookie)) ? decode(result[1]) : null;
};
var pep = {
    'version':'1.0',
    'copyright':'phpems pro',
    'width':$(window).width(),
    'height':$(window).height(),
    'clock':{},
    'allowpre':true,
    'prePages':[],
	'players':[],
    'prePage':null,
    'currentAjax':false,
    'ajaxSending':false,
	'isdecrypt':false,
    'decrypt':function(data){
        if(!pep.isdecrypt)return data;
		for (var i = 0; i < data.length; i++) {
            data[i] -= 16;
        }
        return data;
    },
    exitFullScreen:function(){
        try{
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.webkitCancelFullScreen) {
                document.webkitCancelFullScreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
        }catch (e) {
            //
        } finally {
            //
        }
    },
    'clearEvent':function(){
        for(x in pep.clock)
        {
            clearInterval(pep.clock[x]);
        }
        for(x in pep.players)
        {
            pep.players[x] = null;
        }
        pep.mask.remove();
    },
    'initpage':function(page,tabindex,initscroll){
        pep.clearEvent();
        if(tabindex >= 0)
        {
            var cp = page.find('.pages-tabs').hide().eq(tabindex).show();
            page.attr('data-index',tabindex);
            var cnt = cp.find('.pages-content:first');
        }
        else
        var cnt = page.find('.pages-content:first');
        if(initscroll)page.attr('data-scrolltop',0);
        if(page.attr('data-scrolltop') > 0)
        {
            cnt.scrollTop(page.attr('data-scrolltop'));
        }
        page.find('.fineuploader').each(inituploader);
        page.find(".autoloaditem").each(function(){
            var _this = this;
            if($(_this).attr('autoload') && $(_this).attr('autoload') != '')
            {
                var url = $(_this).attr("autoload").replace(/{value}/,$(_this).val());
                if($(_this).attr('valuefrom') && ($(_this).attr('valuefrom') != "")){
                    var t = $(_this).attr('valuefrom').split("|");
                    for(i=0;i<t.length;i++)
                        url = url.replace(eval("/{"+t[i]+"}/gi"),$('#'+t[i]).val());
                }
                $(_this).load(url);
            }
        });

        if(cnt.attr('data-scroll') == 'yes')
        {
            var loading = false;
            cnt.scroll(function(){
                var _this = $(this),
                    viewH = _this.height(),
                    contentH = _this.get(0).scrollHeight,
                    scrollTop = _this.scrollTop();
                if(pep.isTrueVar(_this.attr('data-pageurl')))
                {
                    if(scrollTop/(contentH -viewH) >= 0.99 && !loading){
                        loading = true;
                        var page = parseInt(_this.attr('data-pagenumber'));
                        if(page != page || page <= 2)page = 2;
                        $.get(_this.attr('data-pageurl')+'&page='+page+'&userhash='+Math.random(),function(data){
                            data = $.trim(data);
                            if(data != '')
                            {
                                $(data).appendTo(_this.find('.pages-box:first'));
                                _this.attr('data-pagenumber',page+1);
                                loading = false;
                            }
                        });
                    }
                }
            });
        }
    },
    'isTrueVar':function(v){
        if(!v)return false;
        if("undefined" != typeof v)
        {
            if('string' == (typeof v).toLowerCase() && v == '')
            {
                return false;
            }
            return true;
        }
        else return false;
    },
    'setPrepage':function(item){
        pep.prePages = $.parseJSON(localStorage.getItem('prepages'));
        if(!pep.isTrueVar(pep.prePages))
        {
            pep.prePages = {};
        }
        pep.prePages[item.id] = item;
        localStorage.setItem('prepages',$.toJSON(pep.prePages));
        return true;
    },
    'getPrepage':function(id){
        pep.prePages = $.parseJSON(localStorage.getItem('prepages'));
        if(!pep.isTrueVar(pep.prePages))return false;
        var p = pep.prePages[id];
        localStorage.setItem('prepages',$.toJSON(pep.prePages));
        return p;
    },
    'goPage':function(e){
        //跳转到一个目标页面
        if(e && e.preventDefault )e.preventDefault();
        else window.event.returnValue = false;
        if(pep.ajaxSending)return false;

        var index = parseInt($('.pages:first').attr('data-index'));
        if(index != index || index < 0 )index = 0;
        $('.pages:first').attr('data-scrolltop',$('.pages:first').find('.pages-tabs').eq(index).find('.pages-content:first').scrollTop());

        var o = $(this);
        if(!o.attr("data-url") || o.attr("data-url") == '')o.attr("data-url",o.attr("href"));
        if(o.attr("data-url").substring(0,7) == 'http://' || o.attr("data-url").substring(0,8) == 'https://')
        {
            window.location = o.attr("data-url");
            return;
        }
        var par = {'url':o.attr("data-url"),'target':o.attr("data-target"),'page':o.attr("data-page"),'title':o.attr("data-title"),'action-before':o.attr("action-before")};
        var page = false;
        if(o.attr("data-page") == 'yes')
        {
            page = pep.newPage(par.title);
        }
        submitAjax(par,page);
    },
    'goPrePage':function(){
        //跳转到上一个页面
        if(pep.allowpre == false)
        {
            pep.mask.show('tips',{'message':'操作过于频繁！'});
            window.history.pushState('','','');
            return false;
        }
        if(pep.countdownloop != null)
        {
            clearInterval(pep.countdownloop);
            pep.countdownloop = null;
        }
        var p = {'id':hex_md5(window.location.pathname + window.location.search),'url':window.location.pathname + window.location.search,'tabindex':$('.pages:first').attr('data-index'),time:0,tabindex:null};
        var mp = pep.getPrepage(p.id);
        if(mp)
        {
            if(mp.hasOwnProperty('time'))p.time = pep.isTrueVar(mp.time)?mp.time:0;
            if(mp.hasOwnProperty('tabindex'))p.tabindex = pep.isTrueVar(mp.tabindex)?mp.tabindex:null;
        }
        if(pep.isTrueVar(p.id) && pep.isTrueVar($('body').data(p.id)) && (new Date().getTime() - p.time < 900000))
        {
            pep.initpage($('body').data(p.id),p.tabindex?p.tabindex:0);
            $('body').data(p.id).appendTo($('body'));
            $('.pages:first').addClass('moving pt-page-moveToRight');
            $('.pages:last').addClass('moving pt-page-moveFromLeft');
        }
        else
        {
            var par = {'url':p.url,'tabindex':p.tabindex};
            var page = pep.newPage('',true);
            submitAjax(par,page,true);
        }
    },
    'newPage':function(title,ispre){
        if(!pep.isTrueVar(title))title = '正在加载';
        var page = $('<div class="pages">'+
            '<div class="pages-tabs">'+
                '<div class="pages-header">'+
                    '<a class="col-xs-2"><div class="text-center">'+
                        '<i class="glyphicon glyphicon-menu-left"></i>'+
                    '</div></a>'+
                    '<a class="col-xs-8 active">'+
                        '<div class="text-center">'+title+'</div>'+
                    '</a>'+
                    '<a class="col-xs-2">'+
                        '<div class="text-center">'+
                            '<i class="glyphicon glyphicon-option-horizontal"></i>'+
                        '</div>'+
                    '</a>'+
                '</div>'+
                '<div class="pages-content text-center" style="padding-top: 1rem;"></div>'+
            '</div>'+
        '</div>');
        var m = $('<div class="mask"><div class="text-center" style="margin-top: 1rem;"><img src="public/static/images/loader.gif" style="width: 1rem;"/></div></div>');
        m.appendTo($('body'));
        page.appendTo($('body'));
        if(ispre)
        {
            $('.pages:first').addClass('moving pt-page-moveToRight');
            $('.pages:last').addClass('moving pt-page-moveFromLeft');
        }
        else
        {
            var pre = {'id':hex_md5(window.location.pathname + window.location.search),'time':new Date().getTime(),'url':window.location.pathname + window.location.search,'tabindex':$('.pages:first').attr('data-index')};
            if($('.pages:first').find('.pages-content:first').attr('data-nocache') != 'yes')
            $('body').data(pre.id,$('.pages:first'));
            pep.setPrepage(pre);
            $('.pages:first').addClass('moving pt-page-moveToLeft');
            $('.pages:last').addClass('moving pt-page-moveFromRight');
        }
        return page;
    },
    'mask':(function(){
        var timer = null;
        return {
            'show':function(type,target,func){
                pep.mask.remove();
                var m = $('<div class="mask"></div>');
                var msg = $(target).attr('message');
                if(!msg || msg == '')msg = '非法操作！';
                switch(type) {
                    case 'confirm':
                        var cnt = $('<div class="maskbox"></div>'+
                        '<div class="confirmTips">'+
                            '<div class="tipbox">'+msg+'</div>'+
                            '<div class="tiptitle">'+
                                '<div class="col-xs-6 confirm"> 确定 </div>'+
                                '<div class="col-xs-6 cancel"> 取消 </div>'+
                            '</div>'+
                        '</div>');
                        cnt.appendTo(m);
                        m.appendTo($('body'));
                        cnt.find('.cancel').on('click',pep.mask.remove);
                        cnt.find('.confirm').on('click',function(){
                            pep.mask.remove();
                            func();
                        });
                        break;
						
					case 'randcode':
						var cnt = $('<div class="maskbox"></div>'+
                        '<div class="dangerTips">'+
                            '<div class="tipbox" style="clear:both;overflow:hidden;"><img src="index.php?core-api-index-randcode" class="randCode col-xs-6"/><div class="col-xs-6"><input type="text" class="form-control" style="line-height:0.5rem;height:0.5rem" id="randformcode" needle="needle" msg="您必须填写验证码" placeholder="请填写验证码"></div></div>'+
                            '<div class="tiptitle">'+
                                '<div class="col-xs-6 confirm"> 确定 </div>'+
                                '<div class="col-xs-6 cancel"> 取消 </div>'+
                            '</div>'+
                        '</div>');
                        cnt.appendTo(m);
                        m.appendTo($('body'));
						cnt.find('.cancel').on('click',pep.mask.remove);
                        cnt.find('.confirm').on('click',function(){
                            func();
							pep.mask.remove();                            
                        });
						break;
                    case 'warning':
                        var cnt = $('<div class="maskbox"></div>'+
                        '<div class="dangerTips">'+
                            '<div class="tipbox">'+msg+'</div>'+
                            '<div class="tiptitle">'+
                                '确定'+
                            '</div>'+
                        '</div>');
                        cnt.appendTo(m);
                        m.appendTo($('body'));
                        cnt.find('.tiptitle').on('click',function(){
                            pep.mask.remove();
                            if(pep.isTrueVar($(target).attr('forwardUrl')))
                            {
                                if("undefined" != typeof target.page)
                                submitAjax({'url':$(target).attr('forwardUrl')},target.page);
                                else
                                submitAjax({'url':$(target).attr('forwardUrl')});
                            }
                        });
                        break;
                    default:
                        var cnt = $('<div class="tips">'+msg+'</div>');
                        cnt.appendTo(m);
                        m.appendTo($('body'));
                        cnt.css('left',(pep.width - cnt.outerWidth(true))/2);
                        timer = setTimeout(function(){
                            pep.mask.remove();
                        },1500);
                        break;
                }

            },
            'remove':function(){
                if(timer != null){
                    clearTimeout(timer);
                    timer = null;
                }
                $('.mask').remove();
            }
        };
    })()
};
function submitAjax(parms,page,ispre){
    if(!parms.query)parms.query = "";
    parms.query += "&userhash="+Math.random();
    pep.currentAjax = $.ajax({"url":parms.url,
        "type":'post',
        "data":parms.query,
        "beforeSend":function(){
            if(parms['action-before'])eval(parms['action-before'])();
            pep.ajaxSending = true;
        },
        'error':function(){
            pep.mask.show('tips',{'message':'网络异常，请检查网络设置！'});
            setTimeout(function(){
                pep.ajaxSending = false;
            },500);
        },
        'timeout':20000,
        "success":function(data){
            setTimeout(function(){
                pep.ajaxSending = false;
            },500);
            var tmp = null;
            try{
                tmp = $.parseJSON(data);
            }
            catch(e)
            {}
            finally{
                if(tmp){
                    data = tmp;
					pep.mask.remove();
					if(data.callback){
					    eval(data.callback);
                    }
                    if(parseInt(data.statusCode) == 200){
                        if(!data.time)data.time = 500;
                        if(data.message)
                        {
                            pep.mask.show('tips',data);
                        }
                        else
                        {
                            data.time = 1;
                        }
                        setTimeout(function(){
                            if(data.callbackType == 'forward')
                            {
                                if(data.forwardUrl && data.forwardUrl != '')
                                {
                                    pep.mask.remove();
                                    if(data.forwardUrl == 'reload')
                                    {
                                        var href = window.location.href;
                                        if(href && href != '')
                                        {
                                            submitAjax({'url': href,'target':$('.pages:first')[0]});
                                        }
                                    }
                                    else if(data.forwardUrl == 'back')
                                    {
                                        setTimeout(function(){history.back(-1);},500);
                                    }
                                    else
                                    {
                                        submitAjax({'url': data.forwardUrl},page);
                                    }
                                }
                            }
                        },data.time);
                    }else if(parseInt(data.statusCode) == 201){
                        if(data.message)
                        {
                            pep.mask.show('tips',data);
                        }
                        if(data.callbackType == 'forward'){
                            if(data.forwardUrl && data.forwardUrl != '')
                            {
                                if(data.forwardUrl == 'reload')
                                {
                                    window.location.reload();
                                }
                                else if(data.forwardUrl == 'back')
                                {
                                    history.back(-1);
                                }
                                else
                                {
                                    window.location.replace(data.forwardUrl);
                                }
                            }
                        }
                        else{
                            window.location.replace(data.forwardUrl);
                        }
                    }else if(parseInt(data.statusCode) == 300){
                        data.page = page;
                        pep.mask.show('warning',data);
                    }else if(parseInt(data.statusCode) == 301){
                        setTimeout(function(){
                            pep.mask.show('warning',data);
                        },500);
                    }
                    else{
                        pep.mask.show('tips',data);
                    }
                    return;
                }
                else
                {
                    if(data)
                    {
                        if(parms.target)
                        {
                            if((typeof parms.target).toLowerCase() == 'string')
                            $('#'+parms.target).html(data);
                            else
                            {
                                $(parms.target).html(data);
                            }
                        }
                        else
                        {
                            if(!page)page = pep.newPage(parms.title);
                            var inner = $('<div class="pages">'+data+'</div>');
                            setTimeout(function(){
                                //page.html(data);
                                page.replaceWith(inner);
                                pep.initpage(inner,parms.tabindex?parms.tabindex:0);
                                $('.mask').remove();
                            },300);
                            if(ispre != true)
                            history.pushState({'id':parms.url},parms.title,parms.url);
                        }
                    }
                    return page;
                }
            }
        }
    });
}
function xvars(x){
    var _this = this;
    String.prototype.replaceAll  = function(s1,s2){
        return this.replace(new RegExp(s1,"gm"),s2);
    }

    var ginkgo = function(x){
        return core(/(.)*$/gi,x);
    }

    var price = function(x){
        return core(/\d+\.*\d*$/gi,x);
    }

    var datatable = function(x){
        return core(/(\w)+/gi,x);
    }

    var keyword = function(x){
        x.value = x.value.replaceAll('，',',');
        return core(/^[\s|\S]+$/gi,x);
    }

    var english = function(x){
        return core(/^[a-z]+$/gi,x);
    }

    var userid = function(x){
        return core(/^[0-9]+$/gi,x);
    }

    var exp = function(x){
        return core(eval(x.getAttribute('exp')),x);
    }

    var qq = function(x){
        return core(/^\d{5,12}$/gi,x);
    }

    var date = function(x){
        return core(/^\d{4}-\d{1,2}-\d{1,2}$/gi,x);
    }

    var datetime = function(x){
        return core(/^\d{4}-\d{1,2}-\d{1,2}\s\d+:\d+:\d+$/gi,x);
    }

    var telphone = function(x){
        return core(/^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/gi,x);
    }

    var cellphone = function(x){
        return core(/^1[3-9]\d{9}$/gi,x);
    }

    var url = function(x){
        return core(/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/gi,x);
    }

    var userName = function(x){
        return core(/^[\u0391-\uFFE5|\w]{2,40}$/gi,x);
    }

    var title = function(x){
        return _this.core(/^[\u0391-\uFFE5|\w|\s|-]+$/gi,x);
    }

    var password = function(x){
        return core(/^[\s|\S]{6,}$/gi,x);
    }

    var zipcode = function(x){
        return core(/^[1-9]\d{5}$/gi,x);
    }

    var email = function(x){
        return core(/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/gi,x);
    }

    var randcode = function(x){
        return core(/^\d{4}$/gi,x);
    }

    var template = function(x){
        return core(/^\w+$/gi,x);
    }

    var category = function(x){
        return core(/^,[\d|,]+$/gi,x);
    }

    var relation = function(x){
        return core(/^[\d|,|-]+$/gi,x);
    }

    var number = function(x){
        return core(/^\d+$/gi,x);
    }

    var nature = function(x){
        return core(/^[1-9]{1}[0-9]?$/gi,x);
    }
    var core = function(exp,x)
    {
        if(x.attr('type') && x.attr('type').toUpperCase() == 'FILE')
        {
            return {result:true};
        }
        var maxsize = parseInt(x.attr('max'));
        var minsize = parseInt(x.attr('min'));
        var needle = x.attr('needle');
        if( x.attr('type')!='password' && x.val())x.val(x.val().replace(/^\s+/i,'').replace(/\s+$/i,''));
        if(x.get(0).tagName.toUpperCase() == "SELECT"){
            if(needle && x.val() == ""){
                return {result:false,message:x.attr('msg')};
            }
        }

        if(x.attr('maxvalue'))
        {
            var maxv = parseInt(x.attr('maxvalue'));
            if(parseInt(x.val()) > maxv)return {result:false,message:"最大值不能超过"+x.attr('maxvalue')};
        }

        if(x.attr('minvalue'))
        {
            var minv = parseInt(x.attr('minvalue'));
            if(parseInt(x.val()) < minv)return {result:false,message:"最小值不能低于"+x.attr('minvalue')};
        }

        if(x.attr('type')=='checkbox'){
            if(needle && !x.attr('checked')){
                return {result:false,message:x.attr('msg')};
            }
        }
        else{
            if(!needle && x.val() == '')return {result:true};
        }
        if(needle && (x.val() == '' || !x.val()))
        {
            return {result:false,message:x.attr('msg')};
        }
        if(x.attr('equ') && x.attr('equ')!='')
        {
            if(x.val() != $('#'+x.attr('equ')).val())
            {
                return {result:false,message:x.attr('msg')};
            }
        }
        if(maxsize > 0 && x.val().length > maxsize)return {result:false,message:x.attr('msg')};
        if(minsize > 0 && x.val().length < minsize)return {result:false,message:x.attr('msg')};
        try{
            if(x.val().match(exp))return {result:true};
            else return {result:false,message:x.attr('msg')};
        }
        catch(e){
            return false;
        }
    }

    var checkvars = function(x)
    {
        if(x.attr('ajax') == 'get' || x.attr('ajax') == 'post'){
            var d = eval("({'"+x.attr('name')+"':'"+x.val()+"'})");
            var url = x.attr('url');
            var data = $.ajax({
                'url':url,
                async: false,
                'data':d
            }).responseText;
            if(data != '1')return {result:false,message:data};
        }
        try{
            if(x.attr('datatype') && x.attr('datatype') != "")
            {
                var method = eval(x.attr('datatype'));
                return method(x);
            }
            else
                return ginkgo(x);
        }
        catch(e){
            return ginkgo(x);
        }
    }
    return checkvars(x);
}
function formsubmit(){
    var _this = this;
    var status = false;
    var query;
    var target = $(_this).attr('data-target');
    if(!target || target == '')target = null;
    query = $(":input",_this).serialize();
    $(":input",_this).not('.ckeditor').each(function(){
        var _this = this;
        var data = xvars($(this));
        if(!data.result && !status){
            $(_this).parents(".control-group").addClass("error");
            pep.mask.show('tips',data);
            status = true;
        }
    });
    if(status)return false;
    if(!$(_this).attr('action') || $(_this).attr('action') == '')return false;
    if($(_this).attr('disablebutton') == 'on'){
        $("input.formdisable",_this).attr('disabled','true');
        $("input.formdisable",_this).attr('value','正在提交……');
    };
    var url = $(_this).attr('action');
	if($(_this).attr('method').toUpperCase() == 'GET')
	{
		if(url.indexOf('?') < 0)
		{
			url += '?';
		}
		url += '&'+query;
		submitAjax({"url":url,"async":false,"method":'GET',"target":target,'action-before':$(_this).attr('action-before')});
	}
	else
	{
		submitAjax({"url":$(_this).attr('action'),"async":false,"query":query,"target":target,'action-before':$(_this).attr('action-before')});	
	}
    return false;
}

var countdown = function(userOptions)
{
    this.userOptions = userOptions;
    this.time = 0;
    this.h = 0;
    this.m = 0;
    this.s = 0;
    this.t = 0;
    this.interval = false;
    var _this = this;
    this.init = function(lefttime)
    {
        if(!lefttime)lefttime = _this.userOptions.lefttime;
        _this.time = _this.userOptions.time*60 - lefttime;
        _this.s = _this.time%60;
        _this.m = parseInt(_this.time%3600/60);
        _this.h = parseInt(_this.time/3600);
    }

    this.refresh = function (lefttime) {
        _this.init(lefttime);
    }

    this.setval = function()
    {
        if(_this.s >= 10)
            _this.userOptions.sbox.html(_this.s);
        else
            _this.userOptions.sbox.html('0'+_this.s.toString());
        if(_this.m >= 10)
            _this.userOptions.mbox.html(_this.m);
        else
            _this.userOptions.mbox.html('0'+ _this.m);
        if(_this.h >= 10)
            _this.userOptions.hbox.html(_this.h);
        else
            _this.userOptions.hbox.html('0'+ _this.h);
    }

    this.step = function()
    {
        if(_this.s > 0)
        {
            _this.s--;
        }
        else
        {
            if(_this.m > 0)
            {
                _this.m--;
                _this.s = 60;
                _this.s--;
            }
            else
            {
                if(_this.h > 0)
                {
                    _this.h--;
                    _this.m = 60;
                    _this.m--;
                    _this.s = 60;
                    _this.s--;
                }
                else
                {
                    clearInterval(pep.clock.countdown);
                    _this.userOptions.finish();
                    return ;
                }
            }
        }
        _this.setval();
    }
    this.init();
    pep.clock.countdown = setInterval(_this.step, 1000);
};

function inituploader()
{
    var _this = this;
    var ismul = false;
    var petemplate = 'pe-template';
    var petype = 'thumb';
    var ftype = ['jpeg', 'jpg', 'gif', 'png'];
    $(_this).html('');
    if($(_this).attr('attr-list') == 'true')ismul = true;
    if($(_this).attr('attr-template') &&  $(_this).attr('attr-template') != '')petemplate = $(_this).attr('attr-template');
    if($(_this).attr('attr-ftype') &&  $(_this).attr('attr-ftype') != '')ftype = $(_this).attr('attr-ftype').split(',');
    switch($(_this).attr('attr-type'))
    {
        case 'thumb':
        case 'list':
        case 'files':
            petype = $(_this).attr('attr-type');
            break;

        default:
            petype = 'thumb';
    }
    return new qq.FineUploader({
        'element': _this,
        'multiple': ismul,
        'template': petemplate,
        'request': {
            'endpoint': 'index.php?document-api-uploader-fineuploader&imgwidth=420',
            'method': 'POST'
        },
        'thumbnails': {
            'placeholders': {
                'waitingPath': 'public/static/images/loader.gif',
                'notAvailablePath': 'public/static/images/noimage.gif'
            }
        },
        'validation': {
            'allowedExtensions': ftype
        },
        'deleteFile': {
            enabled: false
        },
        'callbacks': {
            'onSubmit':  function(id,  fileName)  {
                if(petype != 'list')
                {
                    $(_this).find('.qq-upload-list-selector').html('');
                    $(_this).find('.qq-upload-list-selector').eq(1).remove();
                }
            },
            'onProgress': function(id, fileName, loaded, total) {
                if (loaded < total)
                {
                    progress = Math.round(loaded / total * 100) + '%';
                    $(_this).find('.process').html(progress);
                    $(_this).find('[qq-file-id='+id+'] .qq-thumbnail-selector').css('opacity',progress/200).css('filter','grayscale(100%)');
                }
                else
                {
                    $(_this).find('.process').html('');
                    $(_this).find('[qq-file-id='+id+'] .qq-thumbnail-selector').css('opacity',0.5).css('filter','grayscale(100%)');
                }
	        },
		    'onComplete': function(id,fileName,responseJSON) {
		    	if(responseJSON.status == 'fail')
                {
                    alert(responseJSON.message);
                    return;
                }
                $(_this).find('[qq-file-id='+id+'] .qq-thumbnail-selector').css('opacity',1).css('filter','none').attr('src',responseJSON.thumb);
                $(_this).find('[qq-file-id='+id+'] .qq-edit-filename-selector').val(responseJSON.thumb);
                if(petype == 'list')
                {
                    var tpl = $(_this).find('.listimg').first().html().replace(/\*name\*/g,$(_this).attr('attr-name'));
                    tpl = tpl.replace(/\*value\*/g,responseJSON.thumb);
                    $('#'+$(_this).attr('attr-box')).append(tpl);
                }
            }
        }
    });
}

function combox(){
    var _this = this;
    if($(_this).attr("target") && ($(_this).attr("target") != "")){
        var url = $(_this).attr("refUrl").replace(/{value}/,$(_this).val());
        if($(_this).attr('valuefrom') && ($(_this).attr('valuefrom') != "")){
            var t = $(_this).attr('valuefrom').split("|");
            for(i=0;i<t.length;i++)
            {
                url = url.replace(eval("/{"+t[i]+"}/gi"),$('#'+t[i]).val());
            }
        }
        submitAjax({'url':url,'target':$(_this).attr("target")});
        if($(_this).attr("callback") && $(_this).attr("callback") != "")
        {
            eval($(_this).attr("callback"))($(_this));
        }
    }
}
function cleardata()
{
    $('body').removeData();
}
$(function(){
    $("body").delegate("a.ajax","click",pep.goPage);
    $("body").delegate("form","submit",formsubmit);
    $('body').delegate('.pages-footer .navibutton','click',function(){
        var _this = $(this);
        var index = _this.index();
        pep.initpage(_this.parents('.pages:first'),index,true);
    });
    $("body").delegate(".pages","animationstart webkitAnimationStart oAnimationStart",function(){
        if($(this).hasClass('moving'))
        {
            pep.allowpre = false;
        }
    });
    $("body").delegate(".pages","animationend webkitAnimationEnd oAnimationEnd",function(){
        $(".pages").removeClass('pt-page-moveFromRight pt-page-moveToRight pt-page-moveFromLeft pt-page-moveToLeft moving');
        if($(".pages").length > 1){
            $('.pages:first').remove();
        }
        pep.allowpre = true;
    });
    pep.initpage($('.pages:first'),0);
    window.addEventListener("hashchange", function(e) {
        return false;
    }, false);
    window.addEventListener("popstate", function(e) {
        if(pep.isTrueVar($('.pages:first').find('.pages-content:first').attr('data-callback')))
        {
            $('body').removeData();
            eval($('.pages:first').find('.pages-content:first').attr('data-callback'))();
        }
        else if($('.pages:first').find('.pages-content:first').attr('data-refresh') == 'yes')
        {
            $('body').removeData();
            pep.goPrePage();
        }
        else
        pep.goPrePage();
    }, false);
})