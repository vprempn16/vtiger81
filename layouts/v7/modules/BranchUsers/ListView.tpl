{*+**********************************************************************************
* BranchUsers ListView using Users common table-style design.
************************************************************************************ *}

{strip}
<div class="main-container clearfix">
<div class="listViewPageDiv viewContent" id="listViewContent">
	<div class="col-sm-12 col-xs-12 content-area">
	<div class="module-action-bar clearfix">
		<div class="module-action-content clearfix row">
			<div class="col-lg-4 col-md-4 col-sm-4">
				<h3 class="module-title pull-left">&nbsp;{vtranslate($MODULE, $MODULE)}&nbsp;</h3>
				<div>
					<p class="current-filter-name pull-left"><span class="fa fa-chevron-right" aria-hidden="true"></span>&nbsp;{vtranslate('List', 'List')}&nbsp;</p>
				</div>
			</div>
			<div class="col-lg-4 col-md-4 col-sm-4"><div id="messageBar" class="hide"></div></div>
			<div class="col-lg-4 col-md-4 col-sm-4 text-right">
				<a class="btn addButton btn-default module-buttons" href="index.php?module={$MODULE}&view=Edit">
					<div class="fa fa-plus"></div>&nbsp;&nbsp;{vtranslate('LBL_ADD_RECORD', 'Vtiger')}
				</a>
			</div>
		</div>
	</div>

	<div class="col-sm-12 col-xs-12">
		<div id="listview-actions" class="listview-actions-container">
			<div class="row">
				<div class="col-md-6">
					<form method="get" action="index.php" class="form-inline">
						<input type="hidden" name="module" value="{$MODULE}" />
						<input type="hidden" name="view" value="List" />
						<input type="hidden" name="orderby" value="{$ORDERBY}" />
						<input type="hidden" name="sortorder" value="{$SORTORDER}" />
						<input type="hidden" name="pageLimit" value="{$PAGELIMIT}" />
						<div class="form-group">
							<input type="text" name="search_key" value="{$SEARCH_KEY|escape}" class="form-control" placeholder="{vtranslate('LBL_SEARCH', 'Vtiger')}" />
						</div>
						<button type="submit" class="btn btn-default">{vtranslate('LBL_SEARCH', 'Vtiger')}</button>
					</form>
				</div>
				<div class="col-md-3"></div>
				<div class="col-md-3 text-right" style="line-height:34px;">
					{vtranslate('LBL_TOTAL', 'Vtiger')}: {$TOTAL_USERS}
				</div>
			</div>
		</div>

		<div id="table-content" class="table-container">
			<table id="listview-table" class="table listview-table">
				<thead>
					<tr class="listViewContentHeader">
						<th>{vtranslate('LBL_ACTIONS', 'Vtiger')}</th>
						<th><a class="listViewContentHeaderValues" href="index.php?module={$MODULE}&view=List&search_key={$SEARCH_KEY|escape:'url'}&page=1&pageLimit={$PAGELIMIT}&orderby=user_name&sortorder={if $ORDERBY eq 'user_name' and $SORTORDER eq 'ASC'}DESC{else}ASC{/if}">{vtranslate('User Name', 'Users')}</a></th>
						<th><a class="listViewContentHeaderValues" href="index.php?module={$MODULE}&view=List&search_key={$SEARCH_KEY|escape:'url'}&page=1&pageLimit={$PAGELIMIT}&orderby=first_name&sortorder={if $ORDERBY eq 'first_name' and $SORTORDER eq 'ASC'}DESC{else}ASC{/if}">{vtranslate('First Name', 'Users')}</a></th>
						<th><a class="listViewContentHeaderValues" href="index.php?module={$MODULE}&view=List&search_key={$SEARCH_KEY|escape:'url'}&page=1&pageLimit={$PAGELIMIT}&orderby=last_name&sortorder={if $ORDERBY eq 'last_name' and $SORTORDER eq 'ASC'}DESC{else}ASC{/if}">{vtranslate('Last Name', 'Users')}</a></th>
						<th><a class="listViewContentHeaderValues" href="index.php?module={$MODULE}&view=List&search_key={$SEARCH_KEY|escape:'url'}&page=1&pageLimit={$PAGELIMIT}&orderby=email1&sortorder={if $ORDERBY eq 'email1' and $SORTORDER eq 'ASC'}DESC{else}ASC{/if}">{vtranslate('Email', 'Users')}</a></th>
						<th><a class="listViewContentHeaderValues" href="index.php?module={$MODULE}&view=List&search_key={$SEARCH_KEY|escape:'url'}&page=1&pageLimit={$PAGELIMIT}&orderby=role_name&sortorder={if $ORDERBY eq 'role_name' and $SORTORDER eq 'ASC'}DESC{else}ASC{/if}">{vtranslate('Role', 'Users')}</a></th>
						<th>{vtranslate('Parent User', 'BranchUsers')}</th>
						<th><a class="listViewContentHeaderValues" href="index.php?module={$MODULE}&view=List&search_key={$SEARCH_KEY|escape:'url'}&page=1&pageLimit={$PAGELIMIT}&orderby=status&sortorder={if $ORDERBY eq 'status' and $SORTORDER eq 'ASC'}DESC{else}ASC{/if}">{vtranslate('Status', 'Users')}</a></th>
					</tr>
				</thead>
				<tbody class="overflow-y">
					{if empty($USERS)}
						<tr class="emptyRecordsDiv">
							<td colspan="8">
								<div class="emptyRecordsContent">{vtranslate('LBL_NO_RECORDS_FOUND', 'Vtiger')}</div>
							</td>
						</tr>
					{else}
						{foreach item=ROW from=$USERS name=users}
							<tr class="listViewEntries" data-id="{$ROW.id}">
								<td class="listViewRecordActions">
									<div class="table-actions">
										<span class="more dropdown action">
											<span href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
												<i title="{vtranslate('LBL_MORE_OPTIONS', 'Vtiger')}" class="fa fa-ellipsis-v icon"></i>
											</span>
											<ul class="dropdown-menu" style="top: auto; bottom: 45%;">
												{if $IS_ADMIN || $ROW.id eq $CURRENT_USER_ID || $ROW.parent_user_id eq $CURRENT_USER_ID || $ROW.creator_id eq $CURRENT_USER_ID || $CAN_EDIT_SAME_LEVEL_USERS}
													<li>
														<a href="index.php?module={$MODULE}&view=Edit&record={$ROW.id}">
															<i class="fa fa-pencil"></i>&nbsp;{vtranslate('LBL_EDIT', 'Vtiger')}
														</a>
													</li>
												{/if}
												{if $IS_ADMIN}
													<li>
														<a href="index.php?module=Users&view=DeleteAjax&record={$ROW.id}">
															<i class="fa fa-trash"></i>&nbsp;{vtranslate('LBL_DELETE', 'Vtiger')}
														</a>
													</li>
												{/if}
											</ul>
										</span>
									</div>
								</td>
								<td class="listViewEntryValue">
									<span class="fieldValue">
										<span class="value">
											{if $IS_ADMIN || $ROW.id eq $CURRENT_USER_ID || $ROW.parent_user_id eq $CURRENT_USER_ID || $ROW.creator_id eq $CURRENT_USER_ID || $CAN_EDIT_SAME_LEVEL_USERS}
												<a href="index.php?module={$MODULE}&view=Edit&record={$ROW.id}">{$ROW.user_name}</a>
											{else}
												{$ROW.user_name}
											{/if}
										</span>
									</span>
								</td>
								<td class="listViewEntryValue"><span class="fieldValue"><span class="value">{$ROW.first_name}</span></span></td>
								<td class="listViewEntryValue"><span class="fieldValue"><span class="value">{$ROW.last_name}</span></span></td>
								<td class="listViewEntryValue"><span class="fieldValue"><span class="value">{$ROW.email1}</span></span></td>
								<td class="listViewEntryValue"><span class="fieldValue"><span class="value">{$ROW.role_name}</span></span></td>
								<td class="listViewEntryValue">
									<span class="fieldValue">
										<span class="value">
											{if $ROW.parent_user_id > 0}
												{getUserFullName($ROW.parent_user_id)}
											{else}
												-
											{/if}
										</span>
									</span>
								</td>
								<td class="listViewEntryValue"><span class="fieldValue"><span class="value">{$ROW.status}</span></span></td>
							</tr>
						{/foreach}
					{/if}
				</tbody>
			</table>
		</div>

		<div class="row" style="margin-top:10px;">
			<div class="col-md-12 text-right">
				{assign var=PREV_PAGE value=$PAGE-1}
				{assign var=NEXT_PAGE value=$PAGE+1}
				<a class="btn btn-default btn-sm {if $PAGE le 1}disabled{/if}" href="index.php?module={$MODULE}&view=List&search_key={$SEARCH_KEY|escape:'url'}&orderby={$ORDERBY}&sortorder={$SORTORDER}&pageLimit={$PAGELIMIT}&page={if $PAGE le 1}1{else}{$PREV_PAGE}{/if}">
					<i class="fa fa-chevron-left"></i>
				</a>
				<span style="margin:0 8px;">{$PAGE} / {$TOTAL_PAGES}</span>
				<a class="btn btn-default btn-sm {if $PAGE ge $TOTAL_PAGES}disabled{/if}" href="index.php?module={$MODULE}&view=List&search_key={$SEARCH_KEY|escape:'url'}&orderby={$ORDERBY}&sortorder={$SORTORDER}&pageLimit={$PAGELIMIT}&page={if $PAGE ge $TOTAL_PAGES}{$TOTAL_PAGES}{else}{$NEXT_PAGE}{/if}">
					<i class="fa fa-chevron-right"></i>
				</a>
			</div>
		</div>
	</div>
</div>
</div>
</div>
{/strip}

