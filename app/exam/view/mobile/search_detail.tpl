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
                <div class="text-center">试题搜索</div>
            </a>
            <a class="col-xs-2">
                <div class="text-center">
                    <i class="fa fa-option-horizontal"></i>
                </div>
            </a>
        </div>
        <div class="pages-content nofooter">
            <div class="pages-box">
                <div class="page-ele margin mg1 nomgLR">
                    <h5 class="bigtitle">{x2;$questypes[$question['questiontype']]['questype']}</h5>
                    {x2;if:$question['questionparent']}
                    <div class="clear question">
                        {x2;$parent[$question['questionparent']]['qrquestion']}
                    </div>
                    {x2;endif}
                    <div class="clear question">
                        {x2;$question['question']}
                    </div>
                    {x2;eval:$question = $question}
                    {x2;include:plugin_examquestion}
                </div>
                <div class="page-ele margin mg1 nomgLR">
                    <h5 class="bigtitle">参考答案</h5>
                    <div class="clear question">
                        {x2;if:$question['questionanswer']}
                        {x2;$question['questionanswer']}
                        {x2;else}
                        本题暂无参考答案
                        {x2;endif}
                    </div>
                </div>
                <div class="page-ele margin mg1 nomgLR">
                    <h5 class="bigtitle col-xs-12">解析</h5>
                    <div class="clear question">
                        {x2;if:$question['questionintro']}
                        {x2;$question['questionintro']}
                        {x2;else}
                        本题暂无解析
                        {x2;endif}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {x2;if:!$_userhash}
</div>
{x2;include:footer}
{x2;endif}