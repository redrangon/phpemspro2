{x2;include:header}
<body>
{x2;include:nav}
<div class="container-fluid pages-content">
    <div class="row-fluid panels">
        <div class="pep panels">
            <div class="col-xs-2">
                {x2;include:menu}
            </div>
            <div class="col-xs-10 nopadding">
                <div class="panel panel-default pagebox border">
                    <div class="panel-heading bold">
                        学习进度
                        <a href="javascript:history.back();" class="btn btn-primary pull-right">返回</a>
                    </div>
                    <div class="panel-body">
                        <table class="table table-hover table-bordered small">
                            <tbody>
                            <tr class="info">
                                <th>名称</th>
                                <th width="160">开始学习时间</th>
                                <th width="160">第一次学完时间</th>
                                <th width="160">上次学到时间</th>
                                <th width="90">完成情况</th>
                                <th width="80">操作</th>
                            </tr>
                            {x2;tree:$videos,video,vid}
                            <tr{x2;if:$records[v:video['videoid']]['recstatus']} class="text-success" {x2;endif}>
                                <td>
                                    {x2;v:video['videoname']}
                                </td>
                                <td>
                                    {x2;$records[v:video['videoid']]['recstarttime']}
                                </td>
                                <td>
                                    {x2;$records[v:video['videoid']]['recfinishtime']}
                                </td>
                                <td>
                                    {x2;v:video['rate']}
                                </td>
                                <td>
                                    {x2;if:$records[v:video['videoid']]['recstatus']}完成{x2;else}未完成{x2;endif}
                                </td>
                                <td>
                                    <a href="index.php?lesson-app-lesson&lessonid={x2;v:video['videolesson']}&videoid={x2;v:video['videoid']}">{x2;if:$records[v:video['videoid']]['recstarttime']}继续学习{x2;else}开始学习{x2;endif}</a>
                                </td>
                            </tr>
                            {x2;endtree}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{x2;include:footer}
</body>
</html>