{strip}
    <div class="well" style="background: #fff; border: 1px solid #ddd; padding: 15px;">
        <h5 class="m-b-15"><strong>{vtranslate('Variable Mapping', 'Whatsapp')}</strong></h5>
        
        {if $PLACEHOLDERS.HEADER}
            <h6><strong>Header Variables</strong></h6>
            {foreach from=$PLACEHOLDERS.HEADER item=VAR}
                <div class="row form-group">
                    <label class="col-sm-3 control-label">{$VAR}</label>
                    <div class="col-sm-9">
                        <select class="select2 wa-wf-map" data-comp="HEADER" data-var="{$VAR}" style="width:100%;">
                            <option value="">{vtranslate('LBL_SELECT_OPTION', 'Vtiger')}</option>
                            {foreach from=$MODULE_FIELDS item=FIELD}
                                <option value="{$FIELD.name}">{$FIELD.label} ({$FIELD.name})</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            {/foreach}
        {/if}

        {if $PLACEHOLDERS.BODY}
            <h6><strong>Body Variables</strong></h6>
            {foreach from=$PLACEHOLDERS.BODY item=VAR}
                <div class="row form-group">
                    <label class="col-sm-3 control-label">{$VAR}</label>
                    <div class="col-sm-9">
                        <select class="select2 wa-wf-map" data-comp="BODY" data-var="{$VAR}" style="width:100%;">
                            <option value="">{vtranslate('LBL_SELECT_OPTION', 'Vtiger')}</option>
                            {foreach from=$MODULE_FIELDS item=FIELD}
                                <option value="{$FIELD.name}">{$FIELD.label} ({$FIELD.name})</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            {/foreach}
        {/if}

        {if $PLACEHOLDERS.BUTTONS}
            <h6><strong>Button Variables</strong></h6>
            {foreach from=$PLACEHOLDERS.BUTTONS key=INDEX item=VARS}
                {foreach from=$VARS item=VAR}
                    <div class="row form-group">
                        <label class="col-sm-3 control-label">Button {$INDEX}: {$VAR}</label>
                        <div class="col-sm-9">
                            <select class="select2 wa-wf-map" data-comp="BUTTONS_{$INDEX}" data-var="{$VAR}" style="width:100%;">
                                <option value="">{vtranslate('LBL_SELECT_OPTION', 'Vtiger')}</option>
                                {foreach from=$MODULE_FIELDS item=FIELD}
                                    <option value="{$FIELD.name}">{$FIELD.label} ({$FIELD.name})</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                {/foreach}
            {/foreach}
        {/if}

        <hr>
        <h5><strong>{vtranslate('Message Preview', 'Whatsapp')}</strong></h5>
        <div class="preview-box well well-sm" style="background:#fdfdfd; min-height:60px;">
            {$PREVIEW_HTML}
        </div>
    </div>
{/strip}
