<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>PHPEMSPRO试卷模板</title>
</head>
<body>
<style>
    html, body {
        margin: 0;
        padding: 0;
        font-family: "Roboto", -apple-system, "San Francisco", "Segoe UI", "Helvetica Neue", sans-serif;
        font-size: 12pt;
        background-color: #eee;
        vertical-align:middle;
        background:#FFFFFF;
    }
    * {
        box-sizing: border-box;
        -moz-box-sizing: border-box;
    }
    .page {
        margin: 1cm auto;
        background: #fff;
        box-shadow: 0 4px 5px rgba(75, 75, 75, 0.2);
        outline: 0;
    }
    div.page-break {
        page-break-after: always;
    }
    h1 {
        page-break-before: always;
    }
    h1, h2, h3, h4, h5, h6 {
        page-break-after: avoid;
    }
    p{
        margin: 0;
        margin-bottom:10pt;
    }
    p+p{
        margin-top: 5pt;
        margin-bottom: 5pt;
    }
    a {
        text-decoration: none;
        color: black;
    }
    table {
        page-break-inside: avoid;
    }
    @page {
        orphans: 4;
        widows: 2;
    }
    @media print {
        html, body {
            background-color: #fff;
        }
        .page {
            width: initial !important;
            min-height: initial !important;
            margin: 0 !important;
            padding: 0 !important;
            border: initial !important;
            border-radius: initial !important;
            background: initial !important;
            box-shadow: initial !important;
            page-break-after: always;
        }
    }
    .page {
        width: 21cm;
        min-height: 29.7cm;
        padding-left: 2cm;
        padding-top: 2cm;
        padding-right: 2cm;
        padding-bottom: 2cm;
    }
    @page {
        size: A4 portrait;
        margin-left: 2cm;
        margin-top: 2cm;
        margin-right: 2cm;
        margin-bottom: 2cm;
    }

    .text-left{
        text-align:left;
    }
    .text-center{
        text-align:center;
    }
    .text-right{
        text-align:right;
    }
    .text-justify{
        text-align:justify;
    }

    .table{
        border:2px solid #000000;
        border-right:1px solid #000000;
        border-bottom:1px solid #000000;
        width:auto;
        margin:auto;
        display:inline-block;
    }
    .table td{
        border-right:1px solid #000000;
        border-bottom:1px solid #000000;
        padding:3pt 8pt;
    }

    .float-left{
        float:left;
    }
    .float-right{
        float:right;
    }
    .float-clear{
        clear:both;
    }
    .answer{
        margin-top:0px!important;
        padding:1pt 5pt;
    }
    .answer p{
        margin: 0px;
        padding:0px;
    }
</style>
<!--
<img alt="" src="files/attach/files/content/20200927/image001.jpg" />
-->
<div class="page" style="margin-left: 35pt;margin-right: 15pt;">
    <p style="text-align:center;font-family:黑体;font-size:15pt;font-weight:400;line-height:24pt;">
        PHPEMSPRO试卷模板
    </p>
    <p style="text-align:center;font-family:黑体;font-size:18pt;font-weight:bold;line-height:28pt;">
        {x2;$paper['papername']}
    </p>
    <p style="text-align:center;font-family:黑体;font-size:12pt;font-weight:400;line-height:24pt;">
        注&nbsp;&nbsp;意&nbsp;&nbsp;事&nbsp;&nbsp;项
    </p>
    <p style="padding:1pt 25pt;font-family:宋体;font-size:10.5pt;line-height:18pt;">
        1、本试卷依据PHPEMSPRO试卷模板标准制作，考试时间：{x2;$paper['papersetting']['papertime']}分钟。<br />
        2、请自行制作试卷密封线并在试卷标封处填写姓名、准考证号和所在单位的名称。<br />
        3、请仔细阅读答题要求，在规定位置填写答案。
    </p>
    <p class="text-center">
        <table class="table" cellspacing="0">
            <tr>
                <td class="text-center"></td>
                {x2;eval:v:qtid = 0}
                {x2;tree:$paper['papersetting']['questypelite'],lite,lid}
                {x2;eval:v:questype = v:key}
                {x2;if:$paper['question']['questions'][v:questype] || $paper['question']['questionrows'][v:questype]}
                {x2;eval:v:qtid++}
                <td class="text-center">&nbsp;&nbsp;{x2;$index[v:qtid]}&nbsp;&nbsp;</td>
                {x2;else}

                {x2;endif}
                {x2;endtree}
                <td class="text-center">&nbsp;&nbsp;总分&nbsp;&nbsp;</td>
            </tr>
            <tr>
                <td class="text-center">得&nbsp;&nbsp;分</td>
                {x2;eval:v:qtid = 0}
                {x2;tree:$paper['papersetting']['questypelite'],lite,lid}
                {x2;eval:v:questype = v:key}
                {x2;if:$paper['question']['questions'][v:questype] || $paper['question']['questionrows'][v:questype]}
                {x2;eval:v:qtid++}
                <td class="text-center">&nbsp;&nbsp;&nbsp;&nbsp;</td>
                {x2;endif}
                {x2;endtree}
                <td class="text-center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            </tr>
        </table>
    </p>
    {x2;eval:v:qtid = 0}
    {x2;eval:v:qindex = 0}
    {x2;tree:$paper['papersetting']['questypelite'],lite,lid}
    {x2;eval:v:questype = v:key}
    {x2;if:$paper['question']['questions'][v:questype] || $paper['question']['questionrows'][v:questype]}
    {x2;eval:v:qtid++}
    <div class="float-clear">
        <p style="height:5pt;padding:0px;margin:0px;font-size:1pt;">&nbsp;</p>
        <p class="text-left">
            <table class="table" cellspacing="0">
                <tr>
                    <td class="text-center">得&nbsp;&nbsp;&nbsp;&nbsp;分</td>
                    <td class="text-center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
                <tr>
                    <td class="text-center">评分人</td>
                    <td class="text-center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
            </table>
        </p>
        <p style="font-family:宋体;font-size:10.5pt;font-weight:bold;line-height:24pt;">
            {x2;$index[v:qtid]}、{x2;$questypes[v:questype]['questype']}({x2;$paper['papersetting']['questype'][v:questype]['describe']})
        </p>
        {x2;if:$paper['question']['questions'][v:questype]}
        {x2;tree:$paper['question']['questions'][v:questype],question,qid}
        {x2;eval:v:qindex++}
        <div class="question" style="font-family:宋体;font-size:10.5pt;line-height:16pt;margin-top: 5pt;margin-bottom: 5pt;">
            {x2;eval:v:question['question'] = \\PHPEMSPRO\\strings::splitp(html_entity_decode(\\PHPEMSPRO\\route::stripSlashes(v:question['question'])))}
            <p>{x2;v:qindex}．{x2;v:question['question']}</p>
        </div>
        {x2;if:!$questypes[v:question['questiontype']]['questsort']}
        {x2;if:$questypes[v:question['questiontype']]['questchoice'] < 4}
        {x2;eval:v:question['questionselect'] = \\PHPEMSPRO\\strings::parseSelector(v:question['questionselect'],'、')}
        {x2;tree:v:question['questionselect'],selector,sid}
        <div class="answer" style="font-family:宋体;font-size:10.5pt;line-height:16pt;">
            {x2;v:selector}
        </div>
        {x2;endtree}
        {x2;endif}
        {x2;endif}
        {x2;endtree}
        {x2;endif}
        {x2;if:$paper['question']['questionrows'][v:questype]}
        {x2;tree:$paper['question']['questionrows'][v:questype],questionrows,qrid}
        <div class="question" style="font-family:宋体;font-size:10.5pt;line-height:16pt;margin-top: 5pt;margin-bottom: 5pt;">
            {x2;realhtml:v:questionrows['qrquestion']}
        </div>
        {x2;tree:v:questionrows['data'],question,qid}
        {x2;eval:v:qindex++}
        <div class="question" style="font-family:宋体;font-size:10.5pt;line-height:16pt;">
            {x2;eval:v:question['question'] = \\PHPEMSPRO\\strings::splitp(html_entity_decode(\\PHPEMSPRO\\route::stripSlashes(v:question['question'])))}
            <p>{x2;v:qindex}．{x2;v:question['question']}</p>
        </div>
        {x2;if:!$questypes[v:question['questiontype']]['questsort']}
        {x2;if:$questypes[v:question['questiontype']]['questchoice'] < 4}
        {x2;eval:v:question['questionselect'] = \\PHPEMSPRO\\strings::parseSelector(v:question['questionselect'],'、')}
        {x2;tree:v:question['questionselect'],selector,sid}
        <div class="answer" style="font-family:宋体;font-size:10.5pt;line-height:16pt;">
            {x2;v:selector}
        </div>
        {x2;endtree}
        {x2;endif}
        {x2;endif}
        {x2;endtree}
        {x2;endtree}
        {x2;endif}
    </div>
    {x2;endif}
    {x2;endtree}
</div>
<div class="page">
    <p style="text-align:center;font-family:黑体;font-size:15pt;font-weight:400;line-height:24pt;">
        答案
    </p>
    {x2;eval:v:qtid = 0}
    {x2;eval:v:qindex = 0}
    {x2;tree:$paper['papersetting']['questypelite'],lite,lid}
    {x2;eval:v:questype = v:key}
    {x2;if:$paper['question']['questions'][v:questype] || $paper['question']['questionrows'][v:questype]}
    {x2;eval:v:qtid++}
    <p style="font-family:宋体;font-size:10.5pt;font-weight:bold;line-height:24pt;">
        {x2;$index[v:qtid]}、{x2;$questypes[v:questype]['questype']}
    </p>
    <div class="question" style="font-family:宋体;font-size:10.5pt;line-height:16pt;">
        {x2;if:$paper['question']['questions'][v:questype]}
        {x2;tree:$paper['question']['questions'][v:questype],question,qid}
        {x2;eval:v:qindex++}
        {x2;v:qindex}．{x2;v:question['questionanswer']}&nbsp;&nbsp;
        {x2;if:v:qindex % 5 == 0}<br />{x2;endif}
        {x2;endtree}
        {x2;endif}
        {x2;if:$paper['question']['questionrows'][v:questype]}
        {x2;tree:$paper['question']['questionrows'][v:questype],questionrows,qrid}
        {x2;tree:v:questionrows['data'],question,qid}
        {x2;eval:v:qindex++}
        {x2;v:qindex}．{x2;v:question['questionanswer']}&nbsp;&nbsp;
        {x2;if:v:qindex % 5 == 0}<br />{x2;endif}
        {x2;endtree}
        {x2;endtree}
        {x2;endif}
    </div>
    {x2;endif}
    {x2;endtree}
</div>
</body>
</html>