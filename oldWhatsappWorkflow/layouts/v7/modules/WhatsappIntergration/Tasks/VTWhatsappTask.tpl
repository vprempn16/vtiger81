
<div id="VtTaskContainer">
	<div class="row form-group">
		<div class="col-sm-6 col-xs-6">
		<div class="row">
			<div class="col-sm-3 col-xs-3">Select Template<span class="redColor">*</span>
			</div>
			<div class="col-sm-9 col-xs-9">
				<select name="task_template" class="select2" data-validation-engine="validate[required]"  id='task_template' style="width:100%;">
                		    <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
                    			{foreach from=$WHATSAPP_TEMPLATES item=T}
                        			<option value="{$T.id}" {if $WHATSAPP_SELECTED_TEMPLATE_ID eq $T.id} selected  {/if}>
                       		     {$T.name}
                     		   </option>
                    		{/foreach}
                		</select>
			</div>
		</div>
		<br>
		<input type='hidden' name='contents' value='{$CONTENTS}'>
		<input type='hidden' name='recepients' value='{$RECEPIENTS}'>
		<div id="WA_MappingContainer" class="WA_MappingContainer"></div>
		</div>
	</div>
</div>
