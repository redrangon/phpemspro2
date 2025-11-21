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
                        试题搜索
                        <a class="btn btn-default pull-right" href="javascript:history.back();">
                            <i class="glyphicon glyphicon-chevron-left"></i> 返回
                        </a>
                    </div>
                    <div class="panel-body">
                        <div class="panel-heading">
                            {x2;realhtml:$questionrows['qrquestion']}
                        </div>
                        {x2;include:plugin_examquestion}
                    </div>
                </div>
                {x2;tree:$questionrows['data'],question,qid}
                {x2;eval: $question = v:question}
                <div class="panel panel-default pagebox border">
                    <div class="panel-heading">
                        第{x2;v:qid}题
                    </div>
                    <div class="panel-body">
                        <div class="panel-heading">
                            {x2;realhtml:$question['question']}
                        </div>
                        <div class="panel-body">
                            {x2;include:plugin_examquestion}
                        </div>
                    </div>
                    <div class="panel-heading">
                        参考答案：<span class="text-success">{x2;$question['questionanswer']}</span>
                    </div>
                    <div class="panel-heading">
                        参考解析
                    </div>
                    <div class="panel-body">
                        {x2;if:$question['questionintro']}
                        {x2;$question['questionintro']}
                        {x2;else}
                        <p>暂无解析</p>
                        {x2;endif}
                    </div>
                </div>
                {x2;endtree}
            </div>
        </div>
    </div>
</div>
{x2;include:footer}
</body>
</html>