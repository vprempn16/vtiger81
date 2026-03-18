{* Renders after user selects a template *}
<div class="row form-group">
  <input type="hidden" value='{$TEMPLATE_ID}' name="templateid">
    <div class="col-sm-12 col-xs-12">
	<!--     Recipient -->
    <div class="row m-b-5" style="margin-bottom:10px;">
      <div class="col-sm-3 col-xs-3">
        Recepient<span class="redColor">*</span>
      </div>
      <div class="col-sm-9 col-xs-9">
        <select name="recepients" class="select2" style="width:100%;">
          <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
          {foreach from=$RECEPIENT_FIELD item=F}
            <option value="{$F.name}" {if $RECEPIENT eq $F.name} selected {/if}>{$F.label} ({$F.name})</option>
          {/foreach}
        </select>
      </div>
    </div>

    <!-- Header placeholders -->
    {if $HEADER_PLACEHOLDERS|@count > 0}
      <h5><b>Header</b></h5>
      {foreach from=$HEADER_PLACEHOLDERS item=P}
        <div class="row m-b-5" style="margin-bottom:10px;">
          <div class="col-sm-3 col-xs-3">
            {$P.index} -> {$P.placeholder}
            <input type="hidden" name="task[wa_placeholders][header][{$P.index}]" value="{$P.placeholder}">
          </div>
          <div class="col-sm-9 col-xs-9">
            <select name="task[wa_mapping][header][{$P.index}]" class="select2" style="width:100%;">
              <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
              {foreach from=$MODULE_FIELDS item=F}
                <option value="{$F.name}" {if $SELECTED_FIELD.header[$P.index] eq $F.name} selected {/if}>{$F.label} ({$F.name})</option>
              {/foreach}
            </select>
          </div>
        </div>
      {/foreach}
    {/if}

    <!-- Body placeholders -->
    {if $BODY_PLACEHOLDERS|@count > 0}
      <h5><b>Body</b></h5>
      {foreach from=$BODY_PLACEHOLDERS item=P}
        <div class="row m-b-5" style="margin-bottom:10px;">
          <div class="col-sm-3 col-xs-3">
            {$P.index} → {$P.placeholder}
            <input type="hidden" name="task[wa_placeholders][body][{$P.index}]" value="{$P.placeholder}">
          </div>
          <div class="col-sm-9 col-xs-9">
            <select name="task[wa_mapping][body][{$P.index}]" class="select2" style="width:100%;">
              <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
              {foreach from=$MODULE_FIELDS item=F}
                <option value="{$F.name}" {if $SELECTED_FIELD.body[$P.index] eq $F.name} selected {/if}>{$F.label} ({$F.name})</option>
              {/foreach}
            </select>
          </div>
        </div>
      {/foreach}
    {/if}

	{if $BUTTON_PLACEHOLDERS|@count > 0}
  <h5><b>Buttons</b></h5>

  {foreach from=$BUTTON_PLACEHOLDERS item=B}
    <div class="row m-b-5" style="margin-bottom:10px;">
      
      <div class="col-sm-3 col-xs-3">
        Button {$B.index + 1} → {{$B.param}}
        <br>
        <small>{$B.text}</small>
        <input type="hidden"
               name="task[wa_placeholders][button][{$B.index}]"
               value="{$B.param}">
      </div>

      <div class="col-sm-9 col-xs-9">
        <select name="task[wa_mapping][button][{$B.index}]"
                class="select2"
                style="width:100%;">
          <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
          {foreach from=$MODULE_FIELDS item=F}
            <option value="{$F.name}"
              {if $SELECTED_FIELD.button[$B.index] eq $F.name} selected {/if}>
              {$F.label} ({$F.name})
            </option>
          {/foreach}
        </select>
      </div>

    </div>
  {/foreach}
{/if}
    {if $HEADER_PLACEHOLDERS|@count == 0 && $BODY_PLACEHOLDERS|@count == 0}
      <div class="alert alert-info">
        {vtranslate('LBL_NO_PLACEHOLDERS_FOUND','Vtiger')}
      </div>
    {/if}


    </div>
</div>
<hr>
<div class="row form-group">
    <div class="col-sm-12 col-xs-12">
        <h4>Template Preview</h4>
        <div class="well" style="background:#f9f9f9; padding:10px; border-radius:5px;">
            {foreach from=$TEMPLATE_COMPONENTS item=COMP}
                {if $COMP.type eq 'HEADER'}
                    <div><strong>{$COMP.text}</strong></div>
                {/if}

                {if $COMP.type eq 'BODY'}
                    <div style="margin-top:5px; white-space:pre-line;">{$COMP.text}</div>
                {/if}

                {if $COMP.type eq 'FOOTER'}
                    <div style="margin-top:5px; font-size:12px; color:#666;">{$COMP.text}</div>
                {/if}

                {if $COMP.type eq 'BUTTONS'}
                    <div style="margin-top:10px;">
                        {foreach from=$COMP.buttons item=BTN}
                            <button type="button" class="btn btn-sm btn-default" disabled>{$BTN.text}</button>
                        {/foreach}
                    </div>
                {/if}
            {/foreach}
        </div>
    </div>
</div>
