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
				<div class="text-center">正式考试</div>
			</a>
			<a class="col-xs-2 ajax" href="index.php?exam-mobile-exam-history">
				<div class="text-center">
					<i class="fa fa-check-double"></i>
				</div>
			</a>
		</div>
		<div class="pages-content nofooter">
			{x2;if:$status['status']}
			<div class="pages-box">
				<div class="page-ele margin mg1 nomgLR">
					<h3 class="bigtitle">考试须知</h3>
					<div class="news padding pd2">
						<p>1．考前三十分钟，考生需持符合报考规定的并与准考证显示信息一致的有效证件，进入规定的考场。</p>
						<p>2．考试期间考生可携带物品：签字笔、铅笔、橡皮、二十四色彩笔（包含黑色、蓝色、棕色、绿色、灰色、橙色、粉色、紫色、红色、黄色）铅笔盒、塑料瓶装水、药品、纸巾和准考证。</p>
						<p>3．考试期间需放在指定区域的禁止携带物品：处于关闭状态下的手机、相机或任何其他电子产品、字典、笔记本、修正液/修正带等、纸张、书包、手提包、行李箱。</p>
						<p>4．考场内不得相互借用文具。严禁在考场内饮食。</p>
						<p>5．考生入场后，按号入座，将本人《准考证》放在课桌上，以便核验。</p>
						<p>6．统一开考信号发出后才能开始答题。</p>
					</div>
				</div>
                {x2;if:$intime}
				{x2;if:$maxnumber}
				{x2;if:$papers}
				{x2;if:$examsession}
				<div class="page-ele contin">
					<h4 class="bigtitle">意外续考</h4>
				</div>
				<div class="page-ele contin">
					<div class="col-xs-12">
						<div class="cards">
							<h5 class="title">{x2;substring:$examsession['esdata']['name'],63}</h5>
							<p>
								总分：{x2;$examsession['esdata']['setting']['papersetting']['score']}分  时间：{x2;$examsession['esdata']['setting']['papersetting']['papertime']}分钟 及格分：{x2;$examsession['esdata']['setting']['papersetting']['passscore']}
							</p>
							<p>
								开启时间：{x2;date:$examsession['esdata']['starttime'],'Y-m-d H:i:s'}
							</p>
							<div class="clear">
								<div class="col-xs-6">
									<a href="index.php?exam-mobile-exam-recover" class="ajax btn btn-primary btn-block">继续考试</a>
								</div>
								<div class="col-xs-6">
									<a href="index.php?exam-mobile-exam-cancel" class="confirm btn btn-danger btn-block" msg="确定要放弃吗？">放弃续考</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				{x2;else}
				{x2;if:$basic['basicexam']['selectrule']}
				<div class="page-ele clear padding pd2">
					<a class="btn btn-primary openvideo btn-block" data-paperid="">开始考试</a>
				</div>
				{x2;else}
				<div class="page-ele margin mg1 nomgLR">
					<ul class="listmenu">
					{x2;tree:$papers,paper,pid}
						<li class="small display dflex">
							<a data-paperid="{x2;v:paper['paperid']}" class="openvideo">
								<div class="col-xs-9 cflex">
									<div class="title">{x2;substring:v:paper['papername'],63}</div>
									<div class="intro">
										总分：{x2;v:paper['papersetting']['score']}分 时间：{x2;v:paper['papersetting']['papertime']}分钟
									</div>
								</div>
								<div class="col-xs-3 text-center cflex bg">
									<span class="ccmiddle" style="width: 100%;">{x2;if:$examsessions[v:paper['paperid']]}继续作题{x2;else}开始考试{x2;endif}</span>
								</div>
							</a>
						</li>
					{x2;endtree}
					</ul>
				</div>
				{x2;endif}
				{x2;endif}
				{x2;else}
				<div class="page-ele margin mg1 nomgLR">
					<ul class="listmenu">
						<li>目前没有试卷</li>
					</ul>
				</div>
                {x2;endif}
				{x2;else}
				<div class="page-ele margin mg1 nomgLR">
					<ul class="listmenu">
						<li>您的考试次数已经使用完毕！</li>
					</ul>
				</div>
				{x2;endif}
				{x2;else}
				<div class="page-ele margin mg1 nomgLR">
					<p class="text-danger news text-center padding pd3">
						考试将在{x2;date:$basic['basicexam']['opentime']['start'],'Y-m-d H:i:s'}开启，请稍候！
					</p>
				</div>
                {x2;endif}
			</div>
			{x2;else}
			<div class="pages-box">
				<div class="page-ele margin mg1 nomgLR">
					<div class="text-danger news text-center padding pd3">
						本考场正在进行正式考试，您未开通本考场，不能参加考试！
					</div>
				</div>
			</div>
			{x2;endif}
		</div>
	</div>
	{x2;if:$isios}
	<div class="pages-tabs">
		<div class="pages-header">
			<a class="col-xs-2" href="javascript:history.back();"><div class="text-center">
					<i class="fa fa-angle-left"></i>
				</div></a>
			<a class="col-xs-8 active">
				<div class="text-center">正式考试</div>
			</a>
			<a class="col-xs-2 ajax" href="index.php?exam-mobile-exam-history">
				<div class="text-center">
					<i class="fa fa-check-double"></i>
				</div>
			</a>
		</div>
		<div class="pages-content nofooter">
			<div class="list-box bg">
				<form action="index.php?exam-mobile-exam-selectquestions" method="post" id="verifyform">
					<ul class="page-ele clear">
						<li class="text-center padding pd2">
							<img src="" style="width:2.4rem;height:auto;min-height:1rem;" id="faceimg"/>
							<input name="photo" id="photo" type="hidden" value=""/>
							<input name="submit" type="hidden" value="1"/>
							<input name="paperid" type="hidden" value="" id="videopaperid"/>
						</li>
						<li class="padding pd2">
							<button class="btn btn-primary btn-block" type="button" id="tack">重新拍摄</button>
						</li>
						<li class="padding pd2 nopdTB">
							<button class="btn btn-default btn-block" id="cancelvideo" type="button">取消</button>
						</li>
					</ul>
				</form>
				<input type="file" id="faceimage" accept="image/*" capture="camera" style="display:none;" data-name="cam">
			</div>
		</div>
	</div>
	<script>
		$(function(){
			var faceimage  = $('#faceimage');
			var faceimg  = $('#faceimg');
			var img = new Image();
			var opencamera = function(){
				faceimage.val('');
				faceimage.trigger('click');
			}
			faceimage.on('change',function(){
				if(typeof FileReader == 'undefined')
				{
					alert('不支持本机型');
					return;
				}
				var reader = new FileReader();
				reader.readAsDataURL(faceimage[0].files[0]);
				reader.onload = function(){
					img.src = this.result;
				}
				img.onload = function(){
					var width = 480;
					var height = parseInt(width * this.height / this.width);
					var canvas = document.createElement('canvas');
					var context = canvas.getContext('2d');
					canvas.width = width;
					canvas.height = height;
					// 清除画布
					context.clearRect(0, 0, width, height);
					// 图片压缩
					context.drawImage(img, 0, 0, width, height);
					var result = canvas.toDataURL("image/png");
					$('#photo').val(result);
					faceimg.attr('src',result);
					pep.mask.show('loading');
					$('#verifyform').submit();
				}
			});
			$('#tack').on('click', function(){
				opencamera();
			})
			$('.openvideo').on('click',function(){
				var _this = $(this);
				//$('.pages-tabs').toggle();
				$('#videopaperid').val(_this.data('paperid'));
				opencamera();
			});
			$('#cancelvideo').on('click',function(){
				//$('.pages-tabs').toggle();
			});
		});
	</script>
	{x2;else}
	<div class="pages-tabs">
		<div class="pages-header">
			<a class="col-xs-2" href="javascript:history.back();"><div class="text-center">
					<i class="fa fa-angle-left"></i>
				</div></a>
			<a class="col-xs-8 active">
				<div class="text-center">正式考试</div>
			</a>
			<a class="col-xs-2 ajax" href="index.php?exam-mobile-exam-history">
				<div class="text-center">
					<i class="fa fa-check-double"></i>
				</div>
			</a>
		</div>
		<div class="pages-content nofooter">
			<div class="pages-box">
				<form action="index.php?exam-mobile-exam-selectquestions" method="post" id="verifyform">
					<ul class="page-ele clear">
						<li class="text-center padding pd2">
							<video class="img" id="video" width="480" height="384"></video>
							<canvas id="canvas" width="480" height="384" style="display: none;"></canvas>
							<input name="photo" id="photo"  type="hidden" value=""/>
							<input name="submit" type="hidden" value="1"/>
							<input name="paperid" type="hidden" value="" id="videopaperid"/>
						</li>
						<li class="padding pd2">
							<button class="btn btn-primary btn-block" type="button" id="tack">拍摄并验证</button>
						</li>
						<li class="padding pd2 nopdTB">
							<button class="btn btn-default btn-block" id="cancelvideo" type="button">取消</button>
						</li>
					</ul>
				</form>
			</div>
		</div>
	</div>
	<script>
		$(function(){
			var video = document.getElementById('video'),
					canvas = document.getElementById('canvas'),
					snap = document.getElementById('tack'),
					vendorUrl = window.URL || window.webkitURL;

			//媒体对象
			function getUserMedia(obj,success,error){
				if(navigator.getUserMedia){
					getUserMedia = function(obj,success,error){
						navigator.getUserMedia(obj,function(stream){
							success(stream);
						},error);
					}
				}else if(navigator.webkitGetUserMedia){
					getUserMedia=function(obj,success,error){
						navigator.webkitGetUserMedia(obj,function(stream){
							var _URL=window.URL || window.webkitURL;
							success(_URL.createObjectURL(stream));
						},error);
					}
				}else if(navigator.mozGetUserMedia){
					getUserMedia = function(obj,success,error){
						navigator.mozGetUserMedia(obj,function(stream){
							success(window.URL.createObjectURL(stream));
						},error);
					}
				}else{
					return false;
				}
				return getUserMedia(obj,success,error);
			}
			var cams = getUserMedia({
				video: {facingModel:'user'}, //使用摄像头对象
				audio: false  //不适用音频
			}, function(stream){
				console.log(stream.getTracks());
				if ("srcObject" in video) {
					video.srcObject = stream
				} else {
					video.src = window.URL && window.URL.createObjectURL(stream) || stream
				}
				//video.play();
			}, function(error) {
				console.log(error);
			});
			snap.addEventListener('click', function(){
				pep.mask.show('loading');
				canvas.getContext('2d').drawImage(video, 0, 0,300,240);
				$('#photo').val(canvas.toDataURL("image/png"));
				cams = null;
				$('#verifyform').submit();
			})
			$('.openvideo').on('click',function(){
				var _this = $(this);
				$('.pages-tabs').toggle();
				$('#videopaperid').val(_this.data('paperid'));
				video.play();
			});
			$('#cancelvideo').on('click',function(){
				video.pause();
				$('.pages-tabs').toggle();
			});
		});
	</script>
	{x2;endif}
	{x2;if:!$intime}
	<script>
		setInterval(function(){
			submitAjax({url:'index.php?exam-mobile-exam-isintime'});
		},10000)
	</script>
	{x2;endif}
    {x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}