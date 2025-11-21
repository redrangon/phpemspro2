{x2;include:header}
<style>
.logbtn{
	border-radius:40px;
	line-height:40px;
	padding-left:75px;
	padding-right:75px;
	font-size:18px;
	background:#02756E;
	margin:40px;
	border:0px;
}
.logbtn:hover,.logbtn:active,.logbtn:focus,.logbtn:visited{
	background:#02756E;
}
.pep .regbox{
	box-shadow: 0px 0px 13px 5px rgb(14 25 80 / 20%);
	border-radius: 10px!important;
	margin-top:15vh;
	background:#FFFFFF;
	width:500px;
	padding-top:45px;
}
.regbox .panel-heading,.regbox .panel-body{
	border:0px;
	background:transparent;
	color:#02756E;
}
.regbox .panel-body .controls{
	position:relative;
}
.regbox .panel-body input[type=text],.regbox .panel-body input[type=password]{
	line-height:48px;
	height:48px;
	border:0px;
	border-bottom:1px solid #02756E;
	box-shadow:none;
	text-indent:50px;
	font-size:16px;
}
.texticon{
	position:absolute;
	left:25px;
	top:15px;
}
</style>
<body style="margin-top:0px;">
<div class="container-fluid" style="background:url('public/static/images/bg2.png');">
	<div class="row-fluid">
		<div class="pep" style="height: 100vh;overflow:hidden;">
			<div class="panel panel-default regbox" style="">
				<div class="panel-heading">
					<h2 class="text-center">管理后台登录</h2>
				</div>
				<div class="panel-body">
					<form method="post" action="index.php?core-master-login" class="form-horizontal" style="width:72%;margin:auto;">
						<div class="form-group">
							<div class="controls">
								<i class="glyphicon glyphicon-user texticon"></i>
								<input type="text" class="form-control" size="40" name="args[username]" needle="needle" datatype="userName" msg="请填写用户名" placeholder="请填写用户名"/>
							</div>
						</div>
						<div class="form-group">
							<div class="controls">
								<i class="glyphicon glyphicon-lock texticon"></i>
								<input type="password" class="form-control" size="40" name="args[userpassword]" needle="needle" datatype="password" msg="请填写密码" placeholder="请填写密码"/>
							</div>
						</div>
						<div class="form-group hide">
							<div class="controls">
								<i class="glyphicon glyphicon-lock texticon"></i>
								<input type="text" class="form-control" size="40" name="randcode" datatype="number" msg="请填写验证码" placeholder="请填写验证码"/>
							</div>
						</div>
						<div class="form-group">
							<div class="controls text-center">
								<input type="hidden" name="userlogin" value="1"/>
								<button class="btn btn-primary logbtn" type="submit">登录</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>