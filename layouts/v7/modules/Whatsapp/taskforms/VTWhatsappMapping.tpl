{strip}
    <div class="col-sm-12" style="padding: 0;">
        {if $PLACEHOLDERS.HEADER}
            <h5 class="m-b-10"><strong>Header</strong></h5>
            {foreach from=$PLACEHOLDERS.HEADER item=P}
                <div class="row form-group">
                    <div class="col-sm-6 col-xs-6">
                        <div class="row">
                            <label class="col-sm-5 col-xs-5">{$P.var} &rarr; {$P.example}</label>
                            <div class="col-sm-7 col-xs-7">
                                <select class="select2 wa-wf-map" data-comp="HEADER" data-var="{$P.var}" style="width:100%;">
                                    <option value="">{vtranslate('LBL_SELECT_OPTION', 'Vtiger')}</option>
                                    {foreach from=$MODULE_FIELDS item=FIELD}
                                        <option value="{$FIELD.name}">{$FIELD.label} ({$FIELD.name})</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            {/foreach}
        {/if}

        {if $PLACEHOLDERS.BODY}
            <h5 class="m-b-10"><strong>Body</strong></h5>
            {foreach from=$PLACEHOLDERS.BODY item=P}
                <div class="row form-group">
                    <div class="col-sm-6 col-xs-6">
                        <div class="row">
                            <label class="col-sm-5 col-xs-5">{$P.var} &rarr; {$P.example}</label>
                            <div class="col-sm-7 col-xs-7">
                                <select class="select2 wa-wf-map" data-comp="BODY" data-var="{$P.var}" style="width:100%;">
                                    <option value="">{vtranslate('LBL_SELECT_OPTION', 'Vtiger')}</option>
                                    {foreach from=$MODULE_FIELDS item=FIELD}
                                        <option value="{$FIELD.name}">{$FIELD.label} ({$FIELD.name})</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            {/foreach}
        {/if}

        {if $PLACEHOLDERS.BUTTONS}
            <h5 class="m-b-10"><strong>Buttons</strong></h5>
            {foreach from=$PLACEHOLDERS.BUTTONS key=INDEX item=BUTTON}
                <div class="m-b-15">
                    <div class="m-b-5"><strong>{$INDEX} &rarr; {$BUTTON.label}</strong></div>
                    {foreach from=$BUTTON.vars item=P}
                        <div class="row form-group">
                            <div class="col-sm-6 col-xs-6">
                                <div class="row">
                                    <label class="col-sm-5 col-xs-5">Button {$INDEX} URL Parameter</label>
                                    <div class="col-sm-7 col-xs-7">
                                        <select class="select2 wa-wf-map" data-comp="BUTTONS_{$INDEX}" data-var="{$P.var}" style="width:100%;">
                                            <option value="">{vtranslate('LBL_SELECT_OPTION', 'Vtiger')}</option>
                                            {foreach from=$MODULE_FIELDS item=FIELD}
                                                <option value="{$FIELD.name}">{$FIELD.label} ({$FIELD.name})</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/foreach}
                </div>
            {/foreach}
        {/if}

        <hr>
        <h5><strong>Template Preview</strong></h5>
        <div class="preview-box well" style="background:#fdfdfd; min-height:60px; border: 1px solid #eee; padding:15px; border-radius:4px; margin-bottom:20px;">
            {$PREVIEW_HTML}
        </div>
    </div>
{/strip}
