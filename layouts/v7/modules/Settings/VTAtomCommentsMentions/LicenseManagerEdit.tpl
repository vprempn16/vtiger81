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
				<h3 style="margin-top: 0px;">{vtranslate('VTAtom Comments Mention License Manager', $QUALIFIED_MODULE)}</h3>&nbsp;
			</div>
			{assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
			<form id="LicenseManagerConfig" data-detail-url="{$LSITVIEWURL}" method="POST">
				<input type="hidden" name="default" value="false" />
				<input type="hidden" name="parent" value="Settings"/>
                <input type="hidden" name="module" value="VTAtomCommentsMentions"/>
                <input type="hidden" name="action" value="SaveLicense"/>
				<div class="blockData">
					<br>
					<div class="hide errorMessage">
						<div class="alert alert-danger">
						</div>
					</div>
					<div class="block">
						<div>
							<h4>{vtranslate('VTAtom Comments Mention License Manager', $QUALIFIED_MODULE)}</h4>
						</div>
						<hr>
                        {if $LICENSE_KEY neq ''}
						<div class="col-5 pull-right">
                            {if $IS_KEYACTIVE eq true}
                            <button class="btn btn-default" type="button" value='deactivate' id="direct_api">Deactivate</button>
                            {else}
							<button class="btn btn-default" type="button" value='activate'  id="direct_api">Activate</button>
                            {/if}
						</div>
                         {/if}
						<table class="table editview-table no-border">
							<tbody>
								<tr>
									<td class="{$WIDTHTYPE} fieldLabel"><label>{vtranslate('License Key', $QUALIFIED_MODULE)}</label></td>
									<td class="{$WIDTHTYPE} fieldValue" style="width:70%;" >
										<div class=" col-lg-6 col-md-6 col-sm-12">
											<input type="text" {if $API_KEY neq ''} placeholder="{$APIKEY}" {/if} name="cmtmention_license_key" value="" class="inputElement" name="from_email_field" data-rule-email="true" data-rule-illegal="true" />
                                            {if $IS_KEYVALID eq false && $LICENSE_KEY neq ''} <br><span style="color:red;"> Key is not valid</span>  {/if}
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<br>
                    {if  $LICENSE_KEY eq ''}
                        <span style='color:red;'>License Key is empty.Please enter valid License key</span>
                    {/if}
                    {if $IS_KEYVALID eq false && $LICENSE_KEY neq '' }
                    <span style='color:red;'>License Key is not valid.Please enter valid License key</span>
					{/if}
                    {if !$IS_KEYACTIVE && $LICENSE_KEY neq '' } 
                    <span style='color:red;'> Please activate the License key</span>
                    {/if}
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
