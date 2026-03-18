{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{* modules/Vtiger/views/Popup.php *}

{strip}
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE={vtranslate($MODULE,$MODULE)}}
        <div class="modal-body">
            <div id="popupPageContainer" class="contentsDiv col-sm-12">
                <br>
                <div class="blockData">
                    <div class="block">
                        <hr>
                        <table class="table editview-table no-border">
                            <tbody>
                                <tr>
                                    <td class=" fieldLabel"> <label>Select Product </label></td>
                                    <td class=" fieldValue"> 
                                        <div class=" col-lg-6 col-md-6 col-sm-12">
                                            <select class="select2 inputElement col-lg-12 col-md-12 col-lg-12" name="products">
                                                    <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
                                                    {foreach from=$PRODUCTS item=LABEL key=ID }
                                                        <option value="{$ID}">{vtranslate($LABEL,'Vtiger')}</option>
                                                    {/foreach}
                                            </select>
                                        </div> 
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div id="variantTableContainer">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{/strip}

