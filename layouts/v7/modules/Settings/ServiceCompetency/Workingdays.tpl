{strip}
<div class="editViewPageDiv editViewContainer" id="EditViewOutgoing" style="padding-top:0px;">
            <div class="col-lg-12 col-md-12 col-sm-12">
            <div>
            </div>
            <form id="Workingdays" data-detail-url="{$LSITVIEWURL}" method="POST">
                <input type="hidden" name="default" value="false" />
                <input type="hidden" name="parent" value="Settings"/>
                <input type="hidden" name="module" value="ServiceCompetency"/>
                <input type="hidden" name="action" value="SaveAjax"/>
                <input type="hidden" name="mode" value="working_days"/>
                <div class="blockData">
                    <br>
                    <div class="hide errorMessage">
                        <div class="alert alert-danger">
                        </div>
                    </div>
                    <div class="block">
                        <div>
                            <h4>{vtranslate('User Working Days', $QUALIFIED_MODULE)}</h4>
                        </div>
                        <hr>
                        <table class="table editview-table no-border">
                            <tbody>
                                {foreach from=$RECORDS item=NAME key=ID}
                                <tr>
                                    <td class="{$WIDTHTYPE} fieldLabel"><input type="hidden" value="{$ID}" name="userid"><label>{vtranslate($NAME, $QUALIFIED_MODULE)}</label></td>
                                    <td class="{$WIDTHTYPE} fieldValue" style="width:70%;" >
                                        <div class=" col-lg-3 col-md-3 col-sm-6">
                                            <input type="text" name="{$ID}" {if $WORKING_DAYS[$ID]['working_days'] neq '' } value="{$WORKING_DAYS[$ID]['working_days'] }" {/if} class="inputElement working_days" name="from_email_field" data-rule-email="true" data-rule-illegal="true" />
                                        </div>
					<div class=" col-lg-3 col-md-3 col-sm-6">
                                            <input type="text" name="{$ID}" {if $WORKING_DAYS[$ID]['working_hours'] neq '' } value="{$WORKING_DAYS[$ID]['working_hours'] } " {/if} class="inputElement working_days" name="from_email_field" data-rule-email="true" data-rule-illegal="true" />
                                        </div>	
                                    </td>
                                </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    </div>
                     <br>
                    <div class='modal-overlay-footer clearfix'>
                        <div class="row clearfix">
                            <div class='textAlignCenter col-lg-12 col-md-12 col-sm-12 '>
                                <button type='button' class='btn btn-success wd_saveButton' >{vtranslate('LBL_SAVE', $MODULE)}</button>&nbsp;&nbsp;
                                <a class='atm_cancelLink' data-dismiss="modal" href="#">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            </div>
        </div> <!--editViewPageDiv end -->
    {/strip}
