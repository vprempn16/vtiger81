{strip}
<div class="container-fluid main-scroll paddingTop15" id="layoutEditorContainer">
    <div>
            <h3 style="margin-top: 0px;">{vtranslate('Service Competency Settings', $QUALIFIED_MODULE)}</h3>&nbsp; 
    </div>
<div class="contents tabbable ui-sortable">
    <ul class="nav nav-tabs layoutTabs massEditTabs marginBottom10px">
        <li class="rolePriceTab active"><a data-toggle="tab" href="#rolePriceLayout" data-url="" data-mode="showRolePricing" aria-expanded="false"><strong>Role Pricing</strong></a></li>
        <li class="workingDaysTab"><a data-toggle="tab" href="#userWorkingdays" data-url="" data-mode="showWorkingdays" aria-expanded="false"><strong>User Working Days</strong></a></li>
    </ul>
    <div class="tab-content layoutContent themeTableColor overflowVisible">
    <div id="rolePriceLayout" class="tab-pane active">
        <div class="editViewPageDiv editViewContainer" id="EditViewOutgoing" style="padding-top:0px;">
            <div class="col-lg-12 col-md-12 col-sm-12">
            <div>
            </div>
            {assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
            <form id="RolePricing" data-detail-url="{$LSITVIEWURL}" method="POST">
                <input type="hidden" name="default" value="false" />
                <input type="hidden" name="parent" value="Settings"/>
                <input type="hidden" name="module" value="ServiceCompetency"/>
                <input type="hidden" name="action" value="SaveAjax"/>
                <div class="blockData">
                    <br>
                    <div class="hide errorMessage">
                        <div class="alert alert-danger">
                        </div>
                    </div>
                    <div class="block">
                        <div>
                            <h4>{vtranslate('Role Pricing', $QUALIFIED_MODULE)}</h4>
                        </div>
                        <hr>
                        <table class="table editview-table no-border">
                            <tbody>
                                {foreach from=$ROLES item=ROLE key=VAL}
                                <tr>
                                    <td class="{$WIDTHTYPE} fieldLabel"><label>{vtranslate($VAL, $QUALIFIED_MODULE)}</label></td>
                                    <td class="{$WIDTHTYPE} fieldValue" style="width:70%;" >
                                        <div class=" col-lg-6 col-md-6 col-sm-12">
                                            <input type="text" name="{$ROLE}" {if $RECORDS[$ROLE] neq '' } value="{$RECORDS[$ROLE]}" {/if} class="inputElement roleprice" name="from_email_field" data-rule-email="true" data-rule-illegal="true" />
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
                                <button type='submit' class='btn btn-success atm_saveButton' >{vtranslate('LBL_SAVE', $MODULE)}</button>&nbsp;&nbsp;
                                <a class='atm_cancelLink' data-dismiss="modal" href="#">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            </div>
        </div> <!--editViewPageDiv end -->
    </div><!--rolePriceLayout end --> 
    <div id="userWorkingdays" class="tab-pane">
    </div>
</div>
</div>
</div>
{/strip}

