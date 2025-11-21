function questionPaper(config){
    var favorObj = {};
    var mySwiper;
    var slides = [];
    var answers = {};
    var config = config;
    function initSilde(swiper){
        var dom = $(swiper.el.querySelector('.swiper-slide-active'));
        var initStatus = false;
        var form = dom.find('form:first');
        var index = dom.data('index');
        dom.find('.selectbox.radio :radio').off('change').on('change',function(e){
            var parent = $(this).parents('.pages-box:first');
            var _this = $(this);
            if(_this.prop('checked'))
            {
                parent.find('.selector').removeClass('right').removeClass('wrong');
                parent.find('.page-ele').removeClass('hide');
                parent.find('.myanswer').html(_this.parent().find('input:first').val());
                if(_this.val() == parent.attr('data-answer'))
                {
                    _this.parent().find('.selector').addClass('right');
                }
                else
                {
                    _this.parent().find('.selector').addClass('wrong');
                }
                $('.questionindex').eq(index).find('.selector').addClass('selected');
                answers[parent.attr('data-questionid')].useranswer = _this.parent().find('input:first').val();
                if(_this.val() == answers[parent.attr('data-questionid')]?.answer)$('.questionindex').eq(index).find('.selector').addClass('right');
                else $('.questionindex').eq(index).find('.selector').addClass('wrong');
                if(initStatus && config?.recordprogress)
                {
                    setTimeout(function(){
                        form.submit();
                    },50);
                }
            }
        });
        dom.find('.selectbox.checkbox .selector').off('click').on('click',function(){
            var parent = $(this).parents('.pages-box:first');
            var _this = $(this);
            parent.find('.selector').removeClass('right').removeClass('wrong');
            parent.find('.finishbtn').parent().removeClass('hide');
        });
        if(config.subjectmodel)
        {
            dom.find('.selectbox.radio .selector').parent().siblings().off('click').on('click',function(){
                $(this).parent().find('.selector').trigger('click');
            });
            dom.find('.selectbox.checkbox .selector').parent().siblings().off('click').on('click',function(){
                $(this).parent().find('.selector').trigger('click');
            });
        }
        dom.find('.finishbtn').off('click').on('click',function(){
            var parent = $(this).parents('.pages-box:first');
            var _this = $(this);
            parent.find('.selector').removeClass('right').removeClass('wrong');
            if(parent.find('input:checked').length < 1)
            {
                pep.mask.show('tips',{'message':_this.attr('rel')+'请选择一个答案'});
                return false;
            }
            $('.questionindex').eq(index).find('.selector').addClass('selected');
            parent.find('.selector').removeClass('right').removeClass('wrong');
            $(this).parent().addClass('hide');
            var answer = '';
            parent.find('input:checked').each(function(){
                answer += $(this).val();
                if(parent.attr('data-answer').indexOf($(this).val()) >= 0)
                {
                    $(this).parent().find('.selector').addClass('right');
                }
                else
                {
                    $(this).parent().find('.selector').addClass('wrong');
                }
            });
            parent.find('.page-ele').removeClass('hide');
            parent.find('.myanswer').html(answer);
            answers[parent.attr('data-questionid')].useranswer = answer;
            if(answer == answers[parent.attr('data-questionid')]?.answer)$('.questionindex').eq(index).find('.selector').addClass('right');
            else $('.questionindex').eq(index).find('.selector').addClass('wrong');
            if(initStatus && config?.recordprogress)
            {
                setTimeout(function(){
                    form.submit();
                },50);
            }
        });
        if(favorObj?.[dom.attr('data-questionid')])
        {
            $('.pages-footer a').eq(0).addClass('active');
        }
        else
        {
            $('.pages-footer a').eq(0).removeClass('active');
        }
        if (pep.isTrueVar(answers[dom.attr('data-questionid')]?.useranswer)) {
            if (answers[dom.attr('data-questionid')]?.useranswer?.length == 1) {
                dom.find('input[value="' + answers[dom.attr('data-questionid')].useranswer + '"]').parent().find('.selector').trigger('click');
            } else {
                var tmp = answers[dom.attr('data-questionid')].useranswer.split('');
                for (x in tmp) {
                    dom.find('input[value="' + tmp[x] + '"]').attr("checked","true");
                }
            }
            dom.find('.finishbtn').trigger('click');
        }
        if(config?.recordprogress)submitAjax({url:'index.php?exam-mobile-point-savelastquestion&pointid='+config.pointid+'&lastquestion='+index});
        initStatus = true;
    }
    $('.swiper-slide').each(function(index){
        var _this = $(this);
        answers[_this.attr('data-questionid')] = {
            answer:_this.attr('data-answer'),
            useranswer:_this.attr('data-useranswer')
        };
        if(_this.attr('data-useranswer') && _this.attr('data-useranswer') != '')
        {
            $('.questionindex').eq(index).find('.selector').addClass('selected');
            if(_this.attr('data-answer') == _this.attr('data-useranswer'))
            {
                $('.questionindex').eq(index).find('.selector').addClass('right');
            }
            else
            {
                $('.questionindex').eq(index).find('.selector').addClass('wrong');
            }
        }
        if(_this.attr('data-favor') == '1')
        {
            favorObj[_this.attr('data-questionid')] = true;
        }
        slides.push(_this.removeClass('hide').prop("outerHTML"));
        _this.remove();
    })
    $('.pages-footer:first a').eq(2).on('click',function(){
        $('.pages-content:first').toggleClass('hide');
        $('.pages-content:last').toggleClass('hide');
        $(this).toggleClass('active');
    });

    $('.questionindex').on('click',function(){
        $('.pages-content:first').toggleClass('hide');
        $('.pages-content:last').toggleClass('hide');
        $('.pages-footer:first a').eq(2).toggleClass('active');
        var i = parseInt($(this).attr('data-index'));
        mySwiper.slideTo(i-1);
    });
    mySwiper = new Swiper('.swiper', {
        virtual:{
            slides,
            cache: true,
            renderSlide: function(slide, index){
                return slide;
            }
        },
        "loop": false,
        "on": {
            init:function(swiper){
                initSilde(swiper);
            },
            transitionEnd: (swiper) => {
                initSilde(swiper);
            }
        }
    });
    $('.pages-footer a').eq(0).on('click',function(){
        var questionid = $(mySwiper.el.querySelector('.swiper-slide-active')).attr('data-questionid');
        if($(this).hasClass('active'))
        {
            submitAjax({url:'index.php?exam-mobile-ajax-cancelfavor&questionid='+questionid});
            $(this).removeClass('active');
            favorObj[questionid] = null;
        }
        else
        {
            submitAjax({url:'index.php?exam-mobile-ajax-favorquestion&questionid='+questionid});
            $(this).addClass('active');
            favorObj[questionid] = true;
        }
    });
    $('.pages-footer a').eq(1).on('click',function(){
        submitAjax({url:'index.php?exam-mobile-point-note&questionid='+$(mySwiper.el.querySelector('.swiper-slide-active')).attr('data-questionid')});
    });
    $('.pages-footer a').eq(3).on('click',function(){
        submitAjax({url:'index.php?exam-mobile-point-errors&questionid='+$(mySwiper.el.querySelector('.swiper-slide-active')).attr('data-questionid')});
    });
    $('.pages-footer a').eq(4).on('click',function(){
        submitAjax({url:'index.php?exam-mobile-point-faq&questionid='+$(mySwiper.el.querySelector('.swiper-slide-active')).attr('data-questionid')});
    });
    if(config.lastquestion >= 0)mySwiper.slideTo(config.lastquestion);
}

function examPaper(config,type = 'exam')
{
    var mySwiper;
    var slides = [];
    var answers = {};
    var config = config;
    var signs = {};
    var mystorage;
    var clock;
    var type = type == 'exam'?'exam':'exampaper';

    mystorage = new storage(config.token)
    if(Object.keys(config.useranswer).length > 0)
    {
        answers = config.useranswer;
        mystorage.synchData(answers);
    }
    else
    {
        answers = mystorage.question;
    }

    for(var x in answers)
    {
        $('.questionindex[data-questionid="' + x + '"]').find('.selector').addClass('selected');
    }

    function initSilde(swiper)
    {
        var dom = $(swiper.el.querySelector('.swiper-slide-active'));
        var questionid = dom.attr('data-questionid');
        dom.find(':radio').each(function () {
            if ($(this).val() == answers[questionid]) {
                $(this).attr('checked', true);
            }
        });
        dom.find(':checkbox').each(function () {
            if (answers[questionid]?.indexOf($(this).val()) >= 0) {
                $(this).attr('checked', true);
            }
        });
        dom.find(':text').each(function () {
            $(this).val(answers[questionid]);
        });
        dom.find('textarea').each(function () {
            $(this).val(answers[questionid]);
        });

        dom.find('.selectbox.radio :radio').off('change').on('change',function(){
            var parent = $(this).parents('.pages-box:first');
            var _this = $(this);
            $('.questionindex').eq(parent.attr('data-index') - 1).find('.selector').addClass('selected');
            if(_this.prop("checked")){
                answers[questionid] = _this.val();
            }else{
                answers[questionid] = null;
            }
            mystorage.addItem(questionid,answers[questionid]);
        });
        dom.find('.selectbox.checkbox :checkbox').off('change').on('change',function(){
            var parent = $(this).parents('.pages-box:first');
            var _this = $(this);
            $('.questionindex').eq(parent.attr('data-index') - 1).find('.selector').addClass('selected');
            var tmp = answers[questionid]?answers[questionid].split(''):[];
            if(_this.prop("checked")){
                tmp.push(_this.val());
            }else{
                tmp = tmp.filter(function(value){
                    return value != _this.val();
                });
            }
            answers[questionid] = tmp.sort().join('');
            mystorage.addItem(questionid,answers[questionid]);
        });
        dom.find('.selectbox textarea').off('change').on("change",function(){
            var parent = $(this).parents('.pages-box:first');
            var _this = $(this);
            answers[questionid] = _this.val();
            mystorage.addItem(questionid,answers[questionid]);
            $('.questionindex').eq(parent.index() - 1).find('.selector').addClass('selected');
        });
        if(config.subjectmodel)
        {
            dom.find('.selectbox.radio .selector').parent().siblings().off('click').on('click',function(){
                $(this).parent().find('.selector').trigger('click');
            });
            dom.find('.selectbox.checkbox .selector').parent().siblings().off('click').on('click',function(){
                $(this).parent().find('.selector').trigger('click');
            });
        }
        if(signs?.[questionid])
        {
            $('.pages-footer a').eq(3).addClass('active');
        }
        else
        {
            $('.pages-footer a').eq(3).removeClass('active');
        }
    }

    $('.swiper-slide').each(function(index){
        var _this = $(this);
        slides.push(_this.removeClass('hide').prop("outerHTML"));
        _this.remove();
    })

    mySwiper = new Swiper('.swiper', {
        virtual:{
            slides,
            cache: true,
            renderSlide: function(slide, index){
                return slide;
            }
        },
        "loop": false,
        "on": {
            init:function(swiper){
                initSilde(swiper);
            },
            transitionEnd: (swiper) => {
                initSilde(swiper);
            }
        }
    });

    function clearEvent()
    {
        clearInterval(pep.countdownloop);
        clearInterval(pep.countdown);
        clearInterval(pep.saveanswer);
    }

    $('.questionindex').on('click',function(){
        $('.pages-content:first').toggleClass('hide');
        $('.pages-content:last').toggleClass('hide');
        $('.pages-footer:first a').eq(2).toggleClass('active');
        mySwiper.slideTo($(this).attr('data-index') - 1,0);
    });
    $('.pages-footer:first a').eq(1).on('click',function(){
        pep.mask.show('confirm',{'message':'确定交卷吗？'},function(){
            clearEvent();
            $('#answers').val(JSON.stringify(answers));
            $('.pages-content:first').find('form').submit();
        });
    });
    $('.pages-footer:first a').eq(2).on('click',function(){
        $('.pages-content:first').toggleClass('hide');
        $('.pages-content:last').toggleClass('hide');
        $(this).toggleClass('active');
    });
    $('.pages-footer:first a').eq(3).on('click',function(){
        var dom = $(mySwiper.el.querySelector('.swiper-slide-active'));
        var _this = $(this);
        var questionid = dom.attr('data-questionid');
        $('.questionindex').eq(dom.attr('data-index')).find('.selector').toggleClass('sign');
        _this.toggleClass('active');
        signs[questionid] = !signs[questionid];
    });

    $(".savesession").on("click",function(){
        $('#answers').val(JSON.stringify(answers));
        var url = 'index.php?exam-mobile-exampaper-savesession';
        var params = {
            url:url,
            query:$('#exampaperForm :input').serialize()
        };
        submitAjax(params);
    });

    $.get('index.php?exam-mobile-'+ type +'-lefttime&token='+ config.token +'&rand'+Math.random(),function(data){
        var setting = {
            time:config.time,
            hbox:$("#exampaper-timer_h"),
            mbox:$("#exampaper-timer_m"),
            sbox:$("#exampaper-timer_s"),
            finish:function(){
                clearEvent();
                $('#answers').val(JSON.stringify(answers));
                $('.pages-content:first').find('form').submit();
            }
        }
        setting.lefttime = parseInt(data);
        clock = new countdown(setting);
    });
    pep.countdown = setInterval(function(){
        $.get('index.php?exam-mobile-'+ type +'-lefttime&token='+ config.token +'&rand'+Math.random(),function(data){
            clock(data);
        });
    },179000);

    pep.saveanswer = setInterval(function(){
        $('#answers').val(JSON.stringify(answers));
        var params = {
            url:'index.php?exam-mobile-'+ type +'-saveanswer',
            query:$('#exampaperForm :input').serialize()
        };
        submitAjax(params);
    },180000);
    pep.injectEvent(function(){
        clearInterval(pep.countdownloop);
        clearInterval(pep.countdown);
        clearInterval(pep.saveanswer);
    })
}