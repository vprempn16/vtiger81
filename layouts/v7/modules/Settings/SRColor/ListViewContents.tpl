{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*
********************************************************************************/
-->*}
{strip}
   <div class="col-sm-12 col-xs-12 ">
        <div id="listview-actions" class="listview-actions-container">
	    <div class = "row">
		<div class="col-md-6 pull-left"><h3>Color Configuration</h3></div>
                <div class="col-md-6 pull-right">
		   <a class="btn btn-default pull-right editButton" href="{$ADD_URL}">{vtranslate('Create', $QUALIFIED_MODULE)}</a>
                </div>
            </div>
            <div class="list-content row">
                <div class="col-sm-12 col-xs-12 ">
                 <div id="table-content" class="table-container" style="padding-top:0px !important;">
                    <table id="listview-table"  class="table listview-table">
                       <thead>
                          <tr class="listViewContentHeader">
				<th nowrap>SI.No</th>
                            {foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
                                 <th nowrap>
					{$LISTVIEW_HEADER}
                                 </th>
                             {/foreach}
                          </tr>
                       </thead>
                       <tbody class="overflow-y">
                          {foreach item=RECORD key=row_count from=$RECORDS}
                             <tr class="listViewEntries row-{$RECORD['id']}" data-id="{$RECORD['id']}" > 
				    <td>{$row_count}</td>
                                 {foreach item=Values key=column from=$RECORD}
				     {if $column neq 'id' && $column neq 'editUrl'}
                                     	<td class="listViewEntryValue {$WIDTHTYPE}"  width="{$WIDTH}%" nowrap style='cursor:text;'>{vtranslate($Values,$QUALIFIED_MODULE)}</td>
				     {else if $column eq 'editUrl'}
                                     	<td class="listViewEntryValue {$WIDTHTYPE}"  width="{$WIDTH}%" nowrap style='cursor:text;'><a href="{$Values}"><i class="fa fa-pencil" title="Edit"></i> </a> &nbsp;| &nbsp; <a href="#" class="atm_row_del" data-ser-id="{$RECORD['id']}"><i class="fa fa-trash" title="delete"></i> </a> </td>
				     {/if}		
                                 {/foreach}
                             </tr>
                          {/foreach}

 			<!-- Show Empty Table Alert -->
                        {if $RECORD|@count == 0}
                            <tr>
                                <td colspan="6">
                                    <div class="alert alert-info">
                                        There is no configuration created yet. Please click <a href="{$ADD_URL}"><b> here to create<b> </a>
                                    </div>
                                </td>
                            </tr>
                        {/if}

                       </tbody>
                    </table>

                 </div>
                 <div id="scroller_wrapper" class="bottom-fixed-scroll">
                    <div id="scroller" class="scroller-div"></div>
                </div>
                </div>
              </div>
        </div>
    </div>
{/strip}
