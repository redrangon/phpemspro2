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
					<div class="text-center">{x2;$point['pointname']}</div>
				</a>
				<a class="col-xs-2">
					<div class="text-center">
						<i class="fa fa-option-horizontal"></i>
					</div>
				</a>
			</div>
			<div class="pages-content">
				<form action="index.php?exam-mobile-point-save" method="post">
					<input type="hidden" name="pointid" value="{x2;$point['pointid']}"/>
					<input type="hidden" name="lastquestion" value="{x2;$lastquestion}" id="lastquestion"/>						
					<div class="swiper">
						<div class="swiper-wrapper">
							{x2;eval: v:totalnumber = count($questions) }
							{x2;eval:v:index = 0}
							{x2;tree:$questions,question,qid}
							<div class="pages-box swiper-slide" data-index="{x2;v:index}" data-questionid="{x2;v:question['questionid']}"{x2;if:$useranswer[v:question['questionid']]} data-useranswer="{x2;$useranswer[v:question['questionid']]}"{x2;endif} {x2;if:$questypes[v:question['questiontype']]['questsort']}data-sort="1" data-answer="A"{x2;else}data-sort="0" data-answer="{x2;v:question['questionanswer']}"{x2;endif}>
								{x2;eval:v:index++}
								<div class="page-ele margin mg1 nomgLR">
									<h5 class="bigtitle col-xs-12">{x2;v:qid}/{x2;v:totalnumber} {x2;$questypes[v:question['questiontype']]['questype']}</h5>
									{x2;if:v:question['questionparent']}
									<div class="clear question">
										{x2;$parent[v:question['questionparent']]['qrquestion']}
									</div>
									{x2;endif}
									<div class="clear question">
										{x2;v:question['question']}
									</div>
									{x2;eval:$question = v:question}
									{x2;include:plugin_question}
								</div>
								{x2;if:$questypes[v:question['questiontype']]['questsort']}
								<div class="page-ele hide margin mg1 nomgLR">
									<div class="clear question" >
										<div class="col-xs-6 text-center">
											习题编号<br/>
											{x2;v:question['questionid']}
										</div>
										<div class="col-xs-6 text-center">
											难度<br/>
											{x2;if:v:question['questionlevel'] == 1}易{x2;elseif:v:question['questionlevel'] == 2}中{x2;else}难{x2;endif}
										</div>
									</div>
								</div>
								{x2;else}
								<div class="page-ele hide margin mg1 nomgLR">
									<div class="clear question" >
										<div class="col-xs-3 text-center">
											我的答案<br/>
											<span class="myanswer">-</span>
										</div>
										<div class="col-xs-3 text-center">
											正确答案<br/>
											{x2;v:question['questionanswer']}
										</div>
										<div class="col-xs-3 text-center">
											习题编号<br/>
											{x2;v:question['questionid']}
										</div>
										<div class="col-xs-3 text-center">
											难度<br/>
											{x2;if:v:question['questionlevel'] == 1}易{x2;elseif:v:question['questionlevel'] == 2}中{x2;else}难{x2;endif}
										</div>
									</div>
								</div>
								{x2;endif}
								{x2;if:$questypes[v:question['questiontype']]['questsort']}
								<div class="page-ele hide margin mg1 nomgLR">
									<h5 class="bigtitle col-xs-12">参考答案</h5>
									<div class="clear question">
										{x2;if:v:question['questionanswer']}
										{x2;v:question['questionanswer']}
										{x2;else}
										本题暂无参考答案
										{x2;endif}
									</div>
								</div>
								{x2;endif}
								<div class="page-ele hide margin mg1 nomgLR">
									<h5 class="bigtitle col-xs-12">解析</h5>
									<div class="clear question">
										{x2;if:v:question['questionintro']}
										{x2;v:question['questionintro']}
										{x2;else}
										本题暂无解析
										{x2;endif}
									</div>
								</div>
							</div>
							{x2;endtree}
						</div>
					</div>
				</form>
			</div>
			<div class="pages-content hide">
				<div class="pages-box">
					<div class="page-ele clear">
						<div class="clear question">
                            {x2;tree:$questions,question,qid}
							<label class="selectbox col-xs-2 questionindex" data-index="{x2;v:qid}">
								<span class="selector">{x2;v:qid}</span>
							</label>
                            {x2;endtree}
						</div>
					</div>
				</div>
			</div>
			<div class="pages-footer">
				<a class="col-xs-10-2">
					<div class="text-center">
						<span class="fa fa-star"></span><br />收藏
					</div>
				</a>
				<a class="col-xs-10-2">
					<div class="text-center">
						<span class="fa fa-pencil"></span><br />笔记
					</div>
				</a>
				<a class="col-xs-10-2">
					<div class="text-center">
						<span class="fa fa-bookmark"></span><br />题卡
					</div>
				</a>
				<a class="col-xs-10-2">
					<div class="text-center">
						<span class="fa fa-cloud-upload"></span><br />纠错
					</div>
				</a>
				<a class="col-xs-10-2">
					<div class="text-center">
						<span class="fa fa-certificate"></span><br />答疑
					</div>
				</a>
			</div>
		</div>
		<script>
			var savedata = function(){
				$('.pages-content:first').find('form').submit();
			}
			$(function() {
                var form = $('.pages-content:first').find('form:first');
				var favorObj = JSON.parse('{x2;eval: echo json_encode($favors,256)}');
				var mySwiper;
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
				$('.selectbox.radio .selector').on('click',function(e){
					var parent = $(this).parents('.pages-box:first');
					var _this = $(this);
                    _this.removeClass('right').removeClass('wrong');
					parent.find('.page-ele').removeClass('hide');
					parent.find('.myanswer').html(_this.parent().find('input:first').val());
					if(_this.parent().find('input:first').val() == parent.attr('data-answer'))
					{
						_this.addClass('right');
					}
					else
					{
                        _this.addClass('wrong');
					}
					$('.questionindex').eq(parent.index()).find('.selector').addClass('selected');
					if(form.data('initdata') == 'yes')
					{
						setTimeout(function(){
							form.submit();
						},50);
					}
				});
                $('.selectbox.checkbox .selector').on('click',function(){
                    var parent = $(this).parents('.pages-box:first');
                    var _this = $(this);
                    parent.find('.selector').removeClass('right').removeClass('wrong');
                    parent.find('.finishbtn').parent().removeClass('hide');
                });
				{x2;if:!$subject['subjectmodel']}
				$('.selectbox.radio .selector').parent().siblings().on('click',function(){
					$(this).parent().find('.selector').trigger('click');
				});
				$('.selectbox.checkbox .selector').parent().siblings().on('click',function(){
					$(this).parent().find('.selector').trigger('click');
				});
				{x2;endif}
                $('.finishbtn').on('click',function(){
                    var parent = $(this).parents('.pages-box:first');
                    var _this = $(this);
                    if(parent.find('input:checked').length < 1)
                    {
                        pep.mask.show('tips',{'message':_this.attr('rel')+'请选择一个答案'});
                        return false;
                    }
                    $('.questionindex').eq(parent.index()).find('.selector').addClass('selected');
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
                    if(form.data('initdata') == 'yes')
					{
						setTimeout(function(){
							form.submit();
						},50);
					}
				});
                $('.pages-footer a').eq(0).on('click',function(){
                    var questionid = $(mySwiper.virtual.slides[mySwiper.activeIndex]).data('questionid');
                    if($(this).hasClass('active'))
                    {
                        submitAjax({'url':'index.php?exam-mobile-ajax-cancelfavor&questionid='+questionid});
                        $(this).removeClass('active');
                        favorObj[questionid] = null;
                    }
                    else
                    {
                        submitAjax({'url':'index.php?exam-mobile-ajax-favorquestion&questionid='+questionid});
                        $(this).addClass('active');
                        favorObj[questionid] = {};
                    }
                });
                $('.pages-footer a').eq(1).on('click',function(){
                    var index = pep.isTrueVar($('#lastquestion').val())?$('#lastquestion').val():0;
                    submitAjax({'url':'index.php?exam-mobile-point-note&questionid='+$('.pages-box').eq(index).attr('data-questionid')});
                });
                $('.pages-footer a').eq(3).on('click',function(){
                    var index = pep.isTrueVar($('#lastquestion').val())?$('#lastquestion').val():0;
                    submitAjax({'url':'index.php?exam-mobile-point-errors&questionid='+$('.pages-box').eq(index).attr('data-questionid')});
                });
				$('.pages-footer a').eq(4).on('click',function(){
					var index = pep.isTrueVar($('#lastquestion').val())?$('#lastquestion').val():0;
					submitAjax({url:'index.php?exam-mobile-point-faq&questionid='+$('.pages-box').eq(index).attr('data-questionid')});
				});
				$('.pages-box').each(function () {
					var _this = $(this);
					if (pep.isTrueVar(_this.attr('data-useranswer'))) {
						if (_this.attr('data-useranswer').length == 1) {
							_this.find('input[value="' + _this.attr('data-useranswer') + '"]').parent().find('.selector').trigger('click');
						} else {
							var tmp = _this.attr('data-useranswer').split('');
							for (x in tmp) {
								_this.find('input[value="' + tmp[x] + '"]').attr("checked","true");
							}
							_this.find('.finishbtn').trigger('click');
						}
					}
				});
				form.data("initdata","yes");
                // 收集现有 slide HTML 到数组，然后清空 DOM（使用 Swiper virtual）
                var slidesArray = [];
                $('.swiper .swiper-wrapper .swiper-slide').each(function(){
                    slidesArray.push($(this).prop('outerHTML'));
                });
                $('.swiper .swiper-wrapper').empty();

                // 使用事件委托，适配 virtual 模式下 slide 的重建/销毁
                $(document).off('click', '.selectbox.radio .selector').on('click', '.selectbox.radio .selector', function(e){
                    var parent = $(this).closest('.pages-box');
                    var _this = $(this);
                    _this.removeClass('right').removeClass('wrong');
                    parent.find('.page-ele').removeClass('hide');
                    parent.find('.myanswer').html(_this.closest('label').find('input:first').val());
                    if(_this.closest('label').find('input:first').val() == parent.attr('data-answer')) {
                        _this.addClass('right');
                    } else {
                        _this.addClass('wrong');
                    }
                    $('.questionindex').eq(parent.index()).find('.selector').addClass('selected');
                    if(form.data('initdata') == 'yes') {
                        setTimeout(function(){ form.submit(); },50);
                    }
                });
                $(document).off('click', '.selectbox.checkbox .selector').on('click', '.selectbox.checkbox .selector', function(){
                    var parent = $(this).closest('.pages-box');
                    parent.find('.selector').removeClass('right').removeClass('wrong');
                    parent.find('.finishbtn').closest('div').removeClass('hide');
                });
                $(document).off('click', '.finishbtn').on('click', '.finishbtn', function(){
                    var parent = $(this).closest('.pages-box');
                    var _this = $(this);
                    if(parent.find('input:checked').length < 1) {
                        pep.mask.show('tips',{'message':_this.attr('rel')+'请选择一个答案'});
                        return false;
                    }
                    $('.questionindex').eq(parent.index()).find('.selector').addClass('selected');
                    parent.find('.selector').removeClass('right').removeClass('wrong');
                    $(this).closest('div').addClass('hide');
                    var answer = '';
                    parent.find('input:checked').each(function(){
                        answer += $(this).val();
                        if(parent.attr('data-answer').indexOf($(this).val()) >= 0) {
                            $(this).closest('label').find('.selector').addClass('right');
                        } else {
                            $(this).closest('label').find('.selector').addClass('wrong');
                        }
                    });
                    parent.find('.page-ele').removeClass('hide');
                    parent.find('.myanswer').html(answer);
                    if(form.data('initdata') == 'yes') {
                        setTimeout(function(){ form.submit(); },50);
                    }
                });

                // 初始化 virtual swiper（cache:false 减少内存占用）
                mySwiper = new Swiper('.swiper', {
                    loop: false,
                    virtual: {
                        slides: slidesArray,
                        cache: false,
                        renderSlide: function(slide, index){
                            return slide; // slide 已经是 HTML 字符串
                        }
                    },
                    on: {
                        init: function(swiper){
                            var page = $(swiper.virtual.slides[swiper.activeIndex]);
                            if(page.attr('data-favor') == 1) {
                                $('.pages-footer a').eq(0).addClass('active');
                            } else {
                                $('.pages-footer a').eq(0).removeClass('active');
                            }
                        },
                        slideChange: (swiper) => {
                            var page = $(swiper.virtual.slides[swiper.activeIndex]);
                            if(favorObj?.[page.attr('data-questionid')]) {
                                $('.pages-footer a').eq(0).addClass('active');
                            } else {
                                $('.pages-footer a').eq(0).removeClass('active');
                            }
                            $('#lastquestion').val(page.attr('data-index'));
                        }
                    }
                });
                if(pep.isTrueVar($('#lastquestion').val())) {
                    mySwiper.slideTo(parseInt($('#lastquestion').val()));
                }
			});
		</script>
    {x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}