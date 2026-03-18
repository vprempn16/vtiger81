{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}

{strip}
	<div class="editViewPageDiv editViewContainer" id="EditViewOutgoing" style="padding-top:0px;">
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div>
				<h3 style="margin-top: 0px;">{vtranslate('Atom Variant Options', $QUALIFIED_MODULE)}</h3>&nbsp;
			</div>
			{assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
			<form id="AtomsVariantConfig" data-detail-url="{$LSITVIEWURL}" method="POST">
				<input type="hidden" name="default" value="false" />
				<input type="hidden" name="parent" value="Settings"/>
                                <input type="hidden" name="module" value="AtomsVariant"/>
                                <input type="hidden" name="action" value="SaveFormAjax"/>
				<div class="blockData">
					<br>
					<div class="hide errorMessage">
						<div class="alert alert-danger">
						</div>
					</div>
					<div class="block">
						<div>
							<h4>{vtranslate('Atom Variant Options', $QUALIFIED_MODULE)}</h4>
						</div>
						<hr>
						<table class="table editview-table no-border">
							<tbody>
								<tr>
									<td class="{$WIDTHTYPE} fieldLabel"><label>{vtranslate('Field', $QUALIFIED_MODULE)}</label></td>
									<td class="{$WIDTHTYPE} fieldValue">
										<div class=" col-lg-6 col-md-6 col-sm-12">
											<select class="select2 inputElement col-lg-12 col-md-12 col-lg-12" multiple name="variant_fields[]">
												<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
                                                {foreach from=$FIELDS item=LABEL key=NAME }
												<option value="{$NAME}" {if in_array($NAME,$SELECTED_FIELDS) }  selected {/if}>{vtranslate($LABEL, $QUALIFIED_MODULE)}</option>
                                                {/foreach}
											</select>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<br>	
					<div class='modal-overlay-footer clearfix'>
						<div class="row clearfix">
							<div class='textAlignCenter col-lg-12 col-md-12 col-sm-12 '>
								<button type='submit' class='btn btn-success atm_saveButton' >{vtranslate('LBL_SAVE', $MODULE)}</button>&nbsp;&nbsp;
								<a class='atm_cancelLink' data-dismiss="modal" href="#">{vtranslate('LBL_CANCEL', $MODULE)}</a>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
{/strip}
