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
				<div class="text-center">{x2;$paper['name']}</div>
			</a>
			<a class="col-xs-2">
				<div class="text-center">
					<i class="fa fa-option-horizontal"></i>
				</div>
			</a>
		</div>
		<div class="pages-content" data-callback="savedata" data-nocache="yes">
			<form class="swiper-container" action="index.php?exam-mobile-exam-save" method="post">
				<input type="hidden" name="token" value="{x2;$token}">
				<input type="hidden" name="sign" value="{x2;$sign}">
				<div class="swiper-wrapper">
					{x2;eval:v:innerquestionindex = 0;}
					{x2;tree:$paper['setting']['papersetting']['questypelite'],lite,lid}
					{x2;eval:v:questype = v:key}
					{x2;eval:v:questypeindex = 0;}
					{x2;if:$paper['question']['questions'][v:questype] || $paper['question']['questionrows'][v:questype]}
					{x2;eval:v:questypeindex++;}
					{x2;if:$paper['question']['questions'][v:questype]}
					{x2;if:$basic['basicexam']['changesequence']}
					{x2;eval: shuffle($paper['question']['questions'][v:questype]);}
					{x2;endif}
					{x2;tree:$paper['question']['questions'][v:questype],question,qid}

					<div class="pages-box swiper-slide" data-questionid="{x2;v:question['questionid']}" data-index="{x2;v:innerquestionindex}">
						<div class="page-ele margin mg1 nomgLR">
							<h5 class="bigtitle col-xs-12">{x2;v:qid}/{x2;$paper['setting']['papersetting']['questype'][v:questype]['number']} {x2;$questypes[v:question['questiontype']]['questype']}</h5>
							<div class="clear question">
								{x2;v:question['question']}
							</div>
							{x2;eval:$question = v:question}
							{x2;include:plugin_examquestion}
						</div>
					</div>
					{x2;eval:v:innerquestionindex ++;}

					{x2;endtree}
					{x2;endif}
					{x2;if:$paper['question']['questionrows'][v:questype]}
					{x2;if:$basic['basicexam']['changesequence']}
					{x2;eval: shuffle($paper['question']['questionrows'][v:questype]);}
					{x2;endif}
					{x2;tree:$paper['question']['questionrows'][v:questype],questionrows,qrid}
					{x2;tree:v:questionrows['data'],question,qid}
					<div class="pages-box swiper-slide" data-questionid="{x2;v:question['questionid']}" data-index="{x2;v:innerquestionindex}">
						<div class="page-ele margin mg1 nomgLR">
							<h5 class="bigtitle">{x2;v:qid}/{x2;$paper['setting']['papersetting']['questype'][v:questype]['number']} {x2;$questypes[v:questionrows['qrtype']]['questype']}（{x2;$questypes[v:question['questiontype']]['questype']}）</h5>
							<div class="clear question">
								{x2;v:questionrows['qrquestion']}
							</div>
							<div class="clear question">
								{x2;v:question['question']}
							</div>
							{x2;eval:$question = v:question}
							{x2;include:plugin_examquestion}
						</div>
					</div>
					{x2;eval:v:innerquestionindex ++;}
					{x2;endtree}
					{x2;endtree}
					{x2;endif}
					{x2;endif}
					{x2;endif}
				</div>
			</form>
		</div>
		<div class="pages-content hide">
			<div class="pages-box">
				<div class="page-ele radius margin">
					<div class="clear question">
						{x2;eval:v:qtid = 0}
						{x2;tree:$paper['setting']['papersetting']['questypelite'],lite,lid}
						{x2;eval:v:qtindex = 0}
						{x2;eval:v:questype = v:key}
						{x2;if:$paper['question']['questions'][v:questype] || $paper['question']['questionrows'][v:questype]}
						<h5 class="bigtitle col-xs-12">{x2;$questypes[v:questype]['questype']}</h5>
						{x2;if:$paper['question']['questions'][v:questype]}
						{x2;tree:$paper['question']['questions'][v:questype],question,qid}
						{x2;eval:v:qtid++}
						{x2;eval:v:qtindex++}
						<label class="selectbox col-xs-2 questionindex" data-index="{x2;v:qtid}" data-questionid="{x2;v:question['questionid']}">
							<span class="selector">{x2;v:qid}</span>
						</label>
						{x2;endtree}
						{x2;endif}
						{x2;if:$paper['question']['questionrows'][v:questype]}
						{x2;tree:$paper['question']['questionrows'][v:questype],questionrows,qrid}
						{x2;tree:v:questionrows['data'],question,qid}
						{x2;eval:v:qtid++}
						{x2;eval:v:qtindex++}
						<label class="selectbox col-xs-2 questionindex" data-index="{x2;v:qtid}" data-questionid="{x2;v:question['questionid']}">
							<span class="selector">{x2;v:qrid}.{x2;v:qid}</span>
						</label>
						{x2;endtree}
						{x2;endtree}
						{x2;endif}
						{x2;endif}
						{x2;endtree}
					</div>
				</div>
			</div>
		</div>
		<div class="pages-footer">
			<a class="col-xs-4">
				<div class="text-center timer">
					<span id="exampaper-timer_h">00</span>:<span id="exampaper-timer_m">00</span>:<span id="exampaper-timer_s">00</span>
				</div>
			</a>
			<a class="col-xs-4">
				<div class="text-center">
					<span class="btn btn-primary btn-block subpaperbtn">交卷</span>
				</div>
			</a>
			<a class="col-xs-2">
				<div class="text-center">
					<span class="fa fa-bookmark"></span><br />题卡
				</div>
			</a>
			<a class="col-xs-2">
				<div class="text-center">
					<span class="fa fa-star"></span><br />标记
				</div>
			</a>
		</div>
	</div>
	<script>
		var savedata = function(){
			pep.mask.show('tips',{message:'请先交卷'});
			history.pushState({id:'index.php?exam-mobile-exam-paper'},'{x2;$paper['name']}','index.php?exam-mobile-exam-paper');
		}
		$(function() {
			var clock = null;
			var mySwiper = new Swiper('.swiper-container', {
				virtual: true,
				'preventClicks':false,
				"loop": false,
				"autoplay": 0,
				"observer": true,
				"observeParents": true,
				'onInit':function(swiper){
					var page = $(swiper.slides[swiper.activeIndex]);
					if(page.attr('data-sign') == 1)
					{
						$('.pages-footer a').eq(3).addClass('active');
					}
					else
					{
						$('.pages-footer a').eq(3).removeClass('active');
					}
					$('.pages-content:first').attr('data-index',swiper.activeIndex);
				},
				"onSlideChangeEnd": function (swiper) {
					var page = $(swiper.slides[swiper.activeIndex]);
					if(page.attr('data-sign') == 1)
					{
						$('.pages-footer a').eq(3).addClass('active');
					}
					else
					{
						$('.pages-footer a').eq(3).removeClass('active');
					}
					$('.pages-content:first').attr('data-index',swiper.activeIndex);
				}
			});
			var mystorage = new storage('{x2;$token}');
			initData = mystorage.question;
			{x2;if:$useranswer}
			initData = JSON.parse('{x2;$useranswer}');
			mystorage.synchData(initData);
			{x2;endif}
			$('.pages-footer:first a').eq(1).on('click',function(){
				pep.mask.show('confirm',{'message':'确定交卷吗？'},function(){
					$('.pages-content:first').find('form').submit();
				});
			});
			$('.pages-footer:first a').eq(2).on('click',function(){
				$('.pages-content:first').toggleClass('hide');
				$('.pages-content:last').toggleClass('hide');
				$(this).toggleClass('active');
			});
			$('.pages-footer:first a').eq(3).on('click',function(){
				var parent = $(mySwiper.slides[$('.pages-content:first').attr('data-index')]);
				var _this = $(this);
				$.pp = parent;
				$('.questionindex').eq($('.pages-content:first').attr('data-index')).find('.selector').toggleClass('sign');
				_this.toggleClass('active');
				if(parent.attr('data-sign') == '1')
				{
					parent.attr('data-sign','0');
				}
				else
				{
					parent.attr('data-sign','1');
				}
			});
			$('.questionindex').on('click',function(){
				$('.pages-content:first').toggleClass('hide');
				$('.pages-content:last').toggleClass('hide');
				$('.pages-footer:first a').eq(2).toggleClass('active');
				mySwiper.slideTo($(this).attr('data-index') - 1,0);
			});
			$('.selectbox.radio .selector').on('click',function(){
				var parent = $(this).parents('.pages-box:first');
				var _this = $(this);
				var input = _this.parent().find("input:first");
				$('.questionindex').eq(parent.index()).find('.selector').addClass('selected');
				if(input.prop("checked")){
					mystorage.addItem(input.attr('name'),{value:input.val(),time:Date.parse(new Date())/1000});
				}else{
					mystorage.addItem(input.attr('name'),{value:null,time:null});
				}
			});
			$('.selectbox.checkbox .selector').on('click',function(){
				var parent = $(this).parents('.pages-box:first');
				var _this = $(this);
				var input = _this.parent().find("input:first");
				$('.questionindex').eq(parent.index()).find('.selector').addClass('selected');
				if(input.prop("checked")){
					mystorage.addItem(input.attr('name'),{value:input,time:Date.parse(new Date())/1000});
				}else{
					mystorage.addItem(input.attr('name'),{value:null,time:null});
				}
			});
			$('.selectbox textarea').on("change",function(){
				var parent = $(this).parents('.pages-box:first');
				var _this = $(this);
				mystorage.addItem(_this.attr('name'),{value:_this.val(),time:Date.parse(new Date())/1000});
				$('.questionindex').eq(parent.index()).find('.selector').addClass('selected');
			});
			{x2;if:!$subject['subjectmodel']}
			$('.selectbox.radio .selector').parent().siblings().on('click',function(){
				$(this).parent().find('.selector').trigger('click');
			});
			$('.selectbox.checkbox .selector').parent().siblings().on('click',function(){
				$(this).parent().find('.selector').trigger('click');
			});
			{x2;endif}
			$.get('index.php?exam-mobile-exam-lefttime&token={x2;$token}&rand'+Math.random(),function(data){
				var setting = {
					time:{x2;$paper['time']},
					hbox:$("#exampaper-timer_h"),
					mbox:$("#exampaper-timer_m"),
					sbox:$("#exampaper-timer_s"),
					finish:function(){
						$('.pages-content:first').find('form').submit();
					}
				}
				setting.lefttime = parseInt(data);
				clock = new countdown(setting);
			});
			if(initData) {
				for (x in initData) {
					if (initData[x].value) {
						$(':radio[name="' + x + '"]').each(function () {
							var parent = $(this).parents('.pages-box:first');
							if ($(this).val() == initData[x].value) {
								$(this).attr('checked', true);
								$('.questionindex').eq(parent.index()).find('.selector').addClass('selected');
							}
						});
						$(':checkbox[name="' + x + '"]').each(function () {
							var parent = $(this).parents('.pages-box:first');
							if ($(this).val() == initData[x].value) {
								$(this).attr('checked', true);
								$('.questionindex').eq(parent.index()).find('.selector').addClass('selected');
							}
						});
						$(':text[name="' + x + '"]').each(function () {
							var parent = $(this).parents('.pages-box:first');
							$(this).val(initData[x].value);
							$('.questionindex').eq(parent.index()).find('.selector').addClass('selected');
						});
						$('textarea[name="' + x + '"]').each(function () {
							var parent = $(this).parents('.pages-box:first');
							$(this).val(initData[x].value);
							$('.questionindex').eq(parent.index()).find('.selector').addClass('selected');
						});
					}
				}
			}
			pep.saveanswer = setInterval(function(){
				var params = {
					url:'index.php?exam-mobile-exam-saveanswer',
					query:$('.swiper-container:first :input').serialize()
				};
				submitAjax(params);
			},180000);
			pep.countdown = setInterval(function(){
				$.get('index.php?exam-mobile-exam-lefttime&token={x2;$token}&rand'+Math.random(),function(data){
					clock(data);
				});
			},179000);
		});
	</script>
	{x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}