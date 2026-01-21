{*+**********************************************************************************                                                                                                         * The contents of this file are subject to the vtiger CRM Public License Version 1.1                                                                                                          * ("License"); You may not use this file except in compliance with the License                                                                                                                * The Original Code is: vtiger CRM Open Source                                                                                                                                                * The Initial Developer of the Original Code is vtiger.                                                                                                                                       * Portions created by vtiger are Copyright (C) vtiger.                                                                                                                                        * All Rights Reserved.                                                                                                                                                                        ************************************************************************************}
{strip}                                                                                                                                                                                               <div class="editViewPageDiv editViewContainer" id="EditViewOutgoing" style="padding-top:0px;">
		 <div class="col-lg-12 col-md-12 col-sm-12">                                             
                 	<div>
				<h3 style="margin-top: 0px;">{vtranslate('VT Atom Comments Mention', $QUALIFIED_MODULE)}</h3> 
                        </div>
			<form id="VtAtomCommentConfig" data-detail-url="{$LSITVIEWURL}" method="POST">
				<input type="hidden" name="id" value="{$recordId}"/>
				<div class="blockData">
					<br>
					<div class="block">
						<div>
							<h4>{vtranslate('VtAtom Comments Mention', $QUALIFIED_MODULE)}</h4>
						</div>
						<hr>
						<table class="table editview-table no-border">
							 <tbody>
								<tr>
                                                                 	<td class="{$WIDTHTYPE} fieldLabel"><label>{vtranslate('Comment mentions', $QUALIFIED_MODULE)}</label></td>
                                        	                        <td class="{$WIDTHTYPE} fieldValue">
                                                	                        <div class=" col-lg-6 col-md-6 col-sm-12">
                                                        	                        <input type="checkbox" class="vtatom-commentcheck" name="comment_mentions" data-linklabel="VtAtomCommentMentions" data-type="comment_mentions" {if $RECORD['comment_mentions'] eq 'on'}checked{/if} >
                                                                	        </div>
                                                                	</td>
                                                                </tr>
							 </tbody>
						</table>
					<br>
					<div style="padding: 5px;" class="block cmt-sendmail-block {if $RECORD['comment_mentions'] eq 'off'}hide{/if}">
                                                <table class="table editview-table no-border">
                                                         <tbody>
                                                                <tr>
                                                                        <td class="{$WIDTHTYPE} fieldLabel"><label>{vtranslate('Send Mail ', $QUALIFIED_MODULE)}</label></td>
                                                                        <td class="{$WIDTHTYPE} fieldValue">
                                                                                <div class=" col-lg-6 col-md-6 col-sm-12">
                                                                                        <input type="checkbox" class="vtatom-commentcheck" name="send_commentmail" data-type="send_commentmail" {if $RECORD['send_commentmail'] eq 'on'}checked{/if} >
                                                                                </div>
                                                                        </td>
                                                                </tr>
                                                         </tbody>
                                                </table>
                                        </div>
					<br>
					</div>
				</div>
			</form>
		</div>
	</div>
