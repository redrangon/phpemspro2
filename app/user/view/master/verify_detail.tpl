<div class="form-horizontal">
    {x2;tree:$forms,form,fid}
    <div class="form-group">
        <label for="{x2;v:form['id']}" class="control-label col-xs-3">{x2;v:form['title']}：</label>
        <div class="col-xs-9">
            <div class="help-block">
                {x2;v:form['html']}
            </div>
        </div>
    </div>
    {x2;endtree}
</div>