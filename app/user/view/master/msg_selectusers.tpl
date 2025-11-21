<script type="text/javascript">
    function selectall(obj,a,b){
        $(".sbox").prop('checked', $(obj).is(':checked'));
        $(".sbox").each(function(){
            selectquestions(this,a,b);
        });
    }
</script>
<form action="index.php?user-master-msg-selectusers" method="post" direct="modal-body">
	<table class="table">
		<thead>
		<tr>
			<td>
				用户ID：
			</td>
			<td>
				<input name="search[userid]" class="form-control" size="15" type="text" class="number" value="{x2;$search['userid']}"/>
			</td>
			<td>
				用户名：
			</td>
			<td>
				<input class="form-control" name="search[username]" size="15" type="text" value="{x2;$search['username']}"/>
			</td>
			<td></td>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td>
				手机号码：
			</td>
			<td>
				<input class="form-control" name="search[userphone]" size="15" type="text" value="{x2;$search['userphone']}"/>
			</td>
			<td>
				用户组：
			</td>
			<td>
				<select name="search[groupcode]" class="form-control">
					<option value="0">不限</option>
					{x2;tree:$groups,group,gid}
					<option value="{x2;v:group['groupcode']}"{x2;if:$search['groupcode'] == v:group['groupcode']} selected{x2;endif}>{x2;v:group['groupname']}</option>
					{x2;endtree}
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<button class="btn btn-primary" type="submit">提交</button>
			</td>
		</tr>
		</tbody>
	</table>
	<div class="input">
		<input type="hidden" value="1" name="search[argsmodel]" />
	</div>
</form>
<table class="table table-hover table-bordered">
	<thead>
	<tr class="info">
		<th width="30"><input type="checkbox" class="checkall" target="delids"  onclick="javascript:selectall(this,'usersdata');"/></th>
		<th width="48">ID</th>
		<th width="120">用户名</th>
		<th width="120">姓名</th>
		<th width="120">手机号码</th>
		<th width="120">用户组</th>
	</tr>
	</thead>
	<tbody>
	{x2;tree:$users['data'],user,uid}
	<tr>
		<td>
			<input class="sbox" type="checkbox" name="ids[]" value="{x2;v:user['username']}" onclick="javascript:selectquestions(this,'usersdata')">
		</td>
		<td>{x2;v:user['userid']}</td>
		<td>{x2;v:user['username']}</td>
		<td>{x2;v:user['userrealname']}</td>
		<td>{x2;v:user['userphone']}</td>
		<td>{x2;$groups[v:user['usergroupcode']]['groupname']}</td>
	</tr>
	{x2;endtree}
	</tbody>
</table>
<div class="pagination">
	<ul>{x2;$questions['pages']}</ul>
</div>
<script type="text/javascript">
    $(function($) {
        markSelectedQuestions('ids[]','usersdata');
    });
</script>