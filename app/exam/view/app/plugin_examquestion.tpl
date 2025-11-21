{x2;if:$subject['subjectmodel']}
{x2;if:$questypes[$question['questiontype']]['questsort']}
<div class="questionchoice">
    <textarea name="question[{x2;$question['questionid']}]" class="pepeditor" rel="{x2;$question['questionid']}" etype="simple"></textarea>
</div>
{x2;else}
{x2;if:in_array($questypes[$question['questiontype']]['questchoice'],array(2,3))}
<div class="clear question">
    {x2;$question['questionselect']}
</div>
<hr/>
<div class="clear question">
    {x2;tree:$setting['selectortype'],selector,sid}
    <label class="selectbox checkbox">
        <input type="checkbox" name="question[{x2;$question['questionid']}][{x2;v:selector}]" value="{x2;v:selector}">
        <span class="selector glyphicon glyphicon-ok"></span>
        <span class="selector-addon">{x2;v:selector}</span>
    </label>
    {x2;if:v:sid >= $question['questionselectnumber']}
    {x2;eval:break}
    {x2;endif}
    {x2;endif}
</div>
{x2;elseif:$questypes[$question['questiontype']]['questchoice'] == 1}
<div class="clear question">
    {x2;$question['questionselect']}
</div>
<hr/>
<div class="clear question">
    {x2;tree:$setting['selectortype'],selector,sid}
    <label class="selectbox radio">
        <input type="radio" name="question[{x2;$question['questionid']}]" value="{x2;v:selector}">
        <span class="selector glyphicon glyphicon-ok"></span>
        <span class="selector-addon">{x2;v:selector}</span>
    </label>
    {x2;if:v:sid >= $question['questionselectnumber']}
    {x2;eval:break}
    {x2;endif}
    {x2;endif}
</div>
{x2;elseif:$questypes[$question['questiontype']]['questchoice'] == 4}
<div class="clear question">
    <div class="questionchoice">
        <label class="selectbox radio float">
            <input type="radio" name="question[{x2;$question['questionid']}]" value="A"><span class="selector">A</span>
        </label>
        <p>对</p>
    </div>
    <hr/>
    <div class="questionchoice">
        <label class="selectbox radio float">
            <input type="radio" name="question[{x2;$question['questionid']}]" value="B"><span class="selector">B</span>
        </label>
        <p>错</p>
    </div>
</div>
{x2;else}
{x2;endif}
{x2;endif}
{x2;else}
<div class="clear question">
    {x2;if:$questypes[$question['questiontype']]['questsort']}
    <div class="questionchoice">
        <textarea name="question[{x2;$question['questionid']}]" class="pepeditor" rel="{x2;$question['questionid']}" etype="simple"></textarea>
    </div>
    {x2;else}
    {x2;if:$questypes[$question['questiontype']]['questchoice'] == 4}
    <div class="questionchoice">
        <label class="selectbox radio float">
            <input type="radio" name="question[{x2;$question['questionid']}]" value="A"><span class="selector">A</span>
        </label>
        <p>对</p>
    </div>
    <hr/>
    <div class="questionchoice">
        <label class="selectbox radio float">
            <input type="radio" name="question[{x2;$question['questionid']}]" value="B"><span class="selector">B</span>
        </label>
        <p>错</p>
    </div>
    {x2;else}
    {x2;eval:$question['questionselect'] = \\PHPEMSPRO\\strings::parseSelector($question['questionselect'])}
    {x2;tree:$question['questionselect'],selector,sid}
    {x2;if:$questypes[$question['questiontype']]['questchoice'] == 1}
    {x2;if:v:key}<hr/>{x2;endif}
    <div class="questionchoice">
        <label class="selectbox radio float">
            <input type="radio" name="question[{x2;$question['questionid']}]" value="{x2;v:selector[0]}"><span class="selector">{x2;v:selector[0]}</span>
        </label>
        {x2;v:selector[1]}
    </div>
    {x2;elseif:$questypes[$question['questiontype']]['questchoice'] == 2 || $questypes[$question['questiontype']]['questchoice'] == 3}
    {x2;if:v:key}<hr/>{x2;endif}
    <div class="questionchoice">
        <label class="selectbox checkbox float">
            <input type="checkbox" name="question[{x2;$question['questionid']}][{x2;v:selector[0]}]" value="{x2;v:selector[0]}"><span class="selector">{x2;v:selector[0]}</span>
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