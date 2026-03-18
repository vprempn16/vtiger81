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
				<h3 style="margin-top: 0px;">{vtranslate('WhatsApp Manager', $QUALIFIED_MODULE)}</h3>&nbsp;
			</div>
			{assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
			<form id="whatsappmanager_config" data-detail-url="{$LSITVIEWURL}" method="POST">
				<input type="hidden" name="default" value="false" />
				<input type="hidden" name="parent" value="Settings"/>
                <input type="hidden" name="module" value="WhatsappIntergration"/>
                <input type="hidden" name="action" value="SaveToken"/>
				<div class="blockData">
					<br>
					<div class="hide errorMessage">
						<div class="alert alert-danger">
						</div>
					</div>
					<div class="block">
						<div>
							<h4>{vtranslate('WhatsApp Configuration', $QUALIFIED_MODULE)}</h4>
						</div>
						<hr>
						<table class="table editview-table no-border">
							<tbody>
								<tr>
									<td class="{$WIDTHTYPE} fieldLabel"><label>{vtranslate('App Id', $QUALIFIED_MODULE)}</label></td>
									<td class="{$WIDTHTYPE} fieldValue" style="width:70%;" >
										<div class=" col-lg-6 col-md-6 col-sm-12">
											<input type="text"  name="app_id" value="{$APP_ID}" class="inputElement"  data-rule-email="true" data-rule-illegal="true" />
										</div>
									</td>
								</tr>
								<tr>
									<td class="{$WIDTHTYPE} fieldLabel"><label>{vtranslate('App Secret', $QUALIFIED_MODULE)}</label></td>
									<td class="{$WIDTHTYPE} fieldValue" style="width:70%;" >
										<div class=" col-lg-6 col-md-6 col-sm-12">
											<input type="text"  name="app_secret" value="{$APP_SECRET}" class="inputElement"  data-rule-email="true" data-rule-illegal="true" />
										</div>
									</td>
								</tr>
								<tr>
									<td class="{$WIDTHTYPE} fieldLabel"><label>{vtranslate('Phone Number Id', $QUALIFIED_MODULE)}</label></td>
									<td class="{$WIDTHTYPE} fieldValue" style="width:70%;" >
										<div class=" col-lg-6 col-md-6 col-sm-12">
											<input type="text"  name="phone_number_id" value="{$PHONE_ID}" class="inputElement"  data-rule-email="true" data-rule-illegal="true" />
										</div>
									</td>
								</tr>
								<tr>
									<td class="{$WIDTHTYPE} fieldLabel"><label>{vtranslate('Business Id', $QUALIFIED_MODULE)}</label></td>
									<td class="{$WIDTHTYPE} fieldValue" style="width:70%;" >
										<div class=" col-lg-6 col-md-6 col-sm-12">
											<input type="text"  name="business_id" value="{$BUSI_ID}" class="inputElement"  data-rule-email="true" data-rule-illegal="true" />
										</div>
									</td>
								</tr>
								<tr>
									<td class="{$WIDTHTYPE} fieldLabel"><label>{vtranslate('Access Token', $QUALIFIED_MODULE)}</label></td>
									<td class="{$WIDTHTYPE} fieldValue" style="width:70%;" >
										<div class=" col-lg-6 col-md-6 col-sm-12">
											<input type="text" {if $ACCESS_TOKEN neq ''} placeholder="{$ACCESS_TOKEN}" {/if} name="access_token" value="{$ACCESS_TOKEN}" class="inputElement"  data-rule-email="true" data-rule-illegal="true" />
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
								<button type='submit' class='btn btn-success atm_saveToken' >{vtranslate('LBL_SAVE', $MODULE)}</button>&nbsp;&nbsp;
								<a class='atm_cancelLink' data-dismiss="modal" href="#">{vtranslate('LBL_CANCEL', $MODULE)}</a>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
{/strip}
