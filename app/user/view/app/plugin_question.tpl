{x2;if:$parent['qrquestion']}
<div class="clear question">
    {x2;realhtml:$parent['qrquestion']}
</div>
{x2;endif}
<div class="clear question">
    {x2;realhtml:$question['question']}
</div>
{x2;if:$subject['subjectmodel']}
{x2;if:!$questypes[$question['questiontype']]['questsort']}
<div class="clear question">
    {x2;$question['questionselect']}
</div>
{x2;endif}
{x2;else}
<div class="clear question">
    {x2;if:$questypes[$question['questiontype']]['questsort']}
    {x2;else}
    {x2;if:$questypes[$question['questiontype']]['questchoice'] == 4}
    <div style="clear: both">
        <label class="selectbox radio float">
            <input type="radio" name="question[{x2;$question['questionid']}]" value="A" disabled><span class="selector">A</span>
        </label>
        <p>对</p>
    </div>
    <hr/>
    <div style="clear: both">
        <label class="selectbox radio float">
            <input type="radio" name="question[{x2;$question['questionid']}]" value="B" disabled><span class="selector">B</span>
        </label>
        <p>错</p>
    </div>
    {x2;else}
    {x2;eval:$question['questionselect'] = \\PHPEMSPRO\\strings::parseSelector($question['questionselect'])}
    {x2;tree:$question['questionselect'],selector,sid}
    {x2;if:$questypes[$question['questiontype']]['questchoice'] == 1}
    {x2;if:v:key}<hr/>{x2;endif}
    <div style="clear: both">
        <label class="selectbox radio float">
            <input type="radio" name="question[{x2;$question['questionid']}]" value="{x2;v:selector[0]}" disabled><span class="selector">{x2;v:selector[0]}</span>
        </label>
        {x2;v:selector[1]}
    </div>
    {x2;elseif:$questypes[$question['questiontype']]['questchoice'] == 2 || $questypes[$question['questiontype']]['questchoice'] == 3}
    {x2;if:v:key}<hr/>{x2;endif}
    <div style="clear: both">
        <label class="selectbox checkbox float">
            <input type="checkbox" name="question[{x2;$question['questionid']}][]" value="{x2;v:selector[0]}" disabled><span class="selector">{x2;v:selector[0]}</span>
        </label>
        {x2;v:selector[1]}
    </div>
    {x2;endif}
    {x2;if:v:sid >= $question['questionselectnumber']}
    {x2;eval:break}
    {x2;endif}
    {x2;endtree}
    {x2;endif}
    {x2;endif}
</div>
{x2;endif}