{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}

{strip}
	<link rel="stylesheet" href="layouts/v7/modules/Settings/SRColor/resources/color.css" />
	<script type="text/javascript" src="layouts/v7/modules/Settings/SRColor/js/pickr.es5.min.js" ></script>
	<script type="text/javascript" src="layouts/v7/modules/Settings/SRColor/js/pickr.min.js" ></script>

	<div class="editViewPageDiv editViewContainer" id="EditViewOutgoing" style="padding-top:0px;">
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div>
				<h3 style="margin-top: 0px;">{vtranslate('Color Configuration', $QUALIFIED_MODULE)}</h3>
			</div>
			{assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
			<form id="colorSettingsConfig" action="index.php" data-detail-url="{$LISTVIEWURL}" method="POST">
				<input type="hidden" name="default" value="false" />
				<input type="hidden" name="id" value="{$RECORDID}"/>
				<input type="hidden" name="parent" value="Settings"/>
				<input type="hidden" name="module" value="SRColor"/>
				<input type="hidden" name="action" value="SaveFormAjax"/>
				<input type="hidden" name="picklistvalues" value='{Zend_JSON::encode($PICKLIST_VAL)}'/>
				<input type="hidden" name="listviewurl" value='{$LISTVIEWURL}'/>
				<div class="blockData">
					<br>
					<div class="block">
						<div>
							<h4>{vtranslate('Edit Color Configuration', $QUALIFIED_MODULE)}</h4>
						</div>
						<hr>
						<div class="col-md-12">
						<table class="table editview-table no-border">
							<tbody>
								<tr>
									<td class="{$WIDTHTYPE} fieldLabel"><label>{vtranslate('Name', $QUALIFIED_MODULE)}</label></td>
									<td class="{$WIDTHTYPE} fieldValue">
										<div class=" col-lg-6 col-md-6 col-sm-12">
											<input type="text" name="name" value="{$RECORD['name']}" class="inputElement"  data-rule-email="true" data-rule-illegal="true" />
										</div>
									</td>
								</tr>
								<tr>
									<td class="{$WIDTHTYPE} fieldLabel"><label>{vtranslate('Status', $QUALIFIED_MODULE)}</label></td>
									<td class="{$WIDTHTYPE} fieldValue">
										<div class=" col-lg-6 col-md-6 col-sm-12">
											<input type="checkbox" name="status" {if $RECORD['status']} checked {/if} data-rule-email="true" data-rule-illegal="true" />
										</div>
									</td>
								</tr>
								<tr>
									<td class="{$WIDTHTYPE} fieldLabel"><label>{vtranslate('Select Module', $QUALIFIED_MODULE)}</label></td>
									<td class="{$WIDTHTYPE} fieldValue">
										<div class=" col-lg-6 col-md-6 col-sm-12">
											<select class="select2 inputelement col-lg-12 col-md-12 col-lg-12 selectCls" data-category="module"  id="selected_module" name="selected_module">
												<option value="">{vtranslate('Select option','vtiger')}</option>
												{foreach from=$ENTITY_MODULES key=NAME item=MODULE }
													<option {if $MODULE eq $SELECTED_MODULE } selected {/if} value="{$MODULE}" >{vtranslate($MODULE,$QUALIFIED_MODULE)}  </option>
												{/foreach}
											</select>
										</div>
									</td>
								</tr>
								<tr>
									<td class="{$WIDTHTYPE} fieldLabel"><label>{vtranslate('Select Field', $QUALIFIED_MODULE)}</label></td>
									<td class="{$WIDTHTYPE} fieldValue">
										<div class=" col-lg-6 col-md-6 col-sm-12">
											<select class="select2 inputelement col-lg-12 col-md-12 col-lg-12 selectCls" data-category="field" id="field" name="field">
												<option value>{vtranslate('Select option','vtiger')}</option>
												{foreach from=$PICKLIST_FIELD key=NAME item=LABEL }
                                                                                                        <option  {if in_array($NAME, $SELECTED_FIELDS)} disabled {/if}  {if $NAME eq $SELECTED_FIELD } selected {/if}   value="{$NAME}" >{$LABEL} </option>
                                                                                                {/foreach}
											</select>
										</div>
									</td>
								</tr>

							</tbody>
						</table>
						</div>
						<div class="col-md-7">
						<table class="table picklist-table editview-table no-border">
							<tbody>
								{if $PICKLIST_VAL != ''}	
								{foreach from=$PICKLIST_VAL key="key" item="VALUES"}
								<tr>
        								<td class="fieldLabel" style="width:53%;">
               	 								<label>{$PICKLIST_VAL[$VALUES]}</label>
        								</td>
    					    				<td class="fieldValue">
                								<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="color-picker" data-color-element="{$PICKLIST_VAL[$VALUES]}" value="{$META_VALUE[$VALUES]}"></div>
											<input type="hidden" name="{$PICKLIST_VAL[$VALUES]}" id="{$PICKLIST_VAL[$VALUES]}" value="{$META_VALUE[$VALUES]}">
              								        	<!--<input class="inputElement color-picker" data-colorname="{$PICKLIST_VAL[$VALUES]}" type="color" name="{$PICKLIST_VAL[$VALUES]}" value="{$META_VALUE[$VALUES]}"> -->
               	 								</div>
        								</td>
								</tr>
								{/foreach}
								{/if}
							</tbody>
						</table>
						</div>
					</div>
					<br>	
					<div class='modal-overlay-footer clearfix'>
						<div class="row clearfix">
							<div class='textAlignCenter col-lg-12 col-md-12 col-sm-12 '>
								<button type='submit' class='btn btn-success color_saveButton' >{vtranslate('LBL_SAVE', $MODULE)}</button>&nbsp;&nbsp;
								<a class='color_cancelLink' data-dismiss="modal" href="#">{vtranslate('LBL_CANCEL', $MODULE)}</a>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<script></script>
{/strip}
