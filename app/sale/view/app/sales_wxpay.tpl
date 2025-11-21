{x2;include:header}
<body>
<style>
	.pep{
		width: 100%;
	}
	.pep .regbox{
		width: 88%;
		margin-top: 10px;
	}
</style>
<div class="container-fluid">
	<div class="row-fluid panels">
		<div class="pep panels">
			<div class="col-xs-12">
				<div class="panel panel-default pagebox margin">
					<div class="panel-heading">
						微信支付
					</div>
					<div class="panel-body pagebox padding">
						<p class="text-center">
							<img src="index.php?sale-app-sales-qrcode&data={x2;$data}" style="width: 360px;"/>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$(function(){
		setInterval(function(){
			submitAjax({'url':'index.php?sale-app-sales-getstatus&ordersn={x2;$order['ordersn']}'});
		},10000);
	})
</script>
</body>
</html>