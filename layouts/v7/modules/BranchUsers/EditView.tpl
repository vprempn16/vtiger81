{*+**********************************************************************************
* BranchUsers EditView template.
************************************************************************************ *}

<style type="text/css">
	.branchusers-common-page {
		min-height: calc(100vh - 190px);
		padding-bottom: 24px;
	}
	.branchusers-common-page:after {
		content: "";
		display: table;
		clear: both;
	}
</style>
<div class="main-container clearfix">
<div class="editViewPageDiv viewContent detailViewContainer branchusers-common-page">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 content-area">
	<form class="form-horizontal recordEditView" id="EditView" name="EditView" method="POST" action="index.php">
		<div class="editViewHeader">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12">
					<h4 class="editHeader">
						{if $RECORD_ID}
							{vtranslate('LBL_EDIT', 'Vtiger')} {vtranslate('LBL_USER', 'Vtiger')}
						{else}
							{vtranslate('LBL_ADD', 'Vtiger')} {vtranslate('LBL_USER', 'Vtiger')}
						{/if}
					</h4>
				</div>
			</div>
		</div>
		<hr />
		<div class="editViewBody">
			<div class="editViewContents">
		<input type="hidden" name="module" value="{$MODULE}" />
		<input type="hidden" name="action" value="Save" />
		{if $RECORD_ID}<input type="hidden" name="record" value="{$RECORD_ID}" />{/if}
		<input type="hidden" name="parent_user_id" id="parent_user_id" value="{$SELECTED_PARENT_USER_ID}" />

		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<label class="control-label col-sm-4">{vtranslate('User Name', 'Users')}</label>
					<div class="col-sm-8">
						<input class="form-control" type="text" name="user_name" value="{$USER.user_name}" {if $RECORD_ID}readonly{/if} required />
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4">{vtranslate('First Name', 'Users')}</label>
					<div class="col-sm-8">
						<input class="form-control" type="text" name="first_name" value="{$USER.first_name}" />
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4">{vtranslate('Last Name', 'Users')}</label>
					<div class="col-sm-8">
						<input class="form-control" type="text" name="last_name" value="{$USER.last_name}" required />
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4">Primary Email</label>
					<div class="col-sm-8">
						<input class="form-control" type="email" name="email1" value="{$USER.email1}" />
					</div>
				</div>
			</div>

			<div class="col-lg-6">
				<div class="form-group">
					<label class="control-label col-sm-4">{vtranslate('Status', 'Users')}</label>
					<div class="col-sm-8">
						<select class="form-control" name="status">
							<option value="Active" {if $USER.status eq 'Active'}selected{/if}>{vtranslate('Active', 'Users')}</option>
							<option value="Inactive" {if $USER.status eq 'Inactive'}selected{/if}>{vtranslate('Inactive', 'Users')}</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4">{vtranslate('Role', 'Users')}</label>
					<div class="col-sm-8">
						<select class="form-control" name="roleid" id="branchusers_roleid" required>
							<option value="">{vtranslate('LBL_SELECT_OPTION', 'Vtiger')}</option>
							{foreach item=ROLE from=$AVAILABLE_ROLES}
								{if $ROLE.roleid neq $CURRENT_USER_ROLE_ID}
									<option value="{$ROLE.roleid}" {if $SELECTED_ROLE_ID eq $ROLE.roleid}selected{/if}>
										{$ROLE.rolename}
									</option>
								{/if}
							{/foreach}
						</select>
					</div>
				</div>
				<div class="form-group" id="parentUserContainer" style="display:none;">
					<label class="control-label col-sm-4">Select Parent User</label>
					<div class="col-sm-8">
						<div class="input-group">
							<input class="form-control" type="text" id="parent_user_label" value="{$SELECTED_PARENT_USER_LABEL}" readonly />
							<span class="input-group-btn">
								<button type="button" class="btn btn-default" id="chooseParentUserBtn">Choose</button>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<label class="control-label col-sm-4">
						{if $RECORD_ID}
							{vtranslate('LBL_NEW_PASSWORD', 'Users')}
						{else}
							{vtranslate('Password', 'Users')}
						{/if}
					</label>
					<div class="col-sm-8">
						<input class="form-control" type="password" name="new_password" {if !$RECORD_ID}required{/if} />
					</div>
				</div>
			</div>
		</div>
			</div>
		</div>

		<div class='modal-overlay-footer clearfix'>
			<div class="row clearfix">
				<div class='textAlignCenter col-lg-12 col-md-12 col-sm-12'>
					<button type="submit" class="btn btn-success saveButton">
						{vtranslate('LBL_SAVE', 'Vtiger')}
					</button>
					&nbsp;&nbsp;
					<a class="cancelLink" href="index.php?module={$MODULE}&view=List" onclick="window.onbeforeunload = null;">
						{vtranslate('LBL_CANCEL', 'Vtiger')}
					</a>
				</div>
			</div>
		</div>
	</form>
	</div>
</div>
</div>

<div class="modal fade" id="roleUsersModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="roleUsersModalTitle">Select User</h4>
			</div>
			<div class="modal-body">
				<div id="roleUsersTreeContainer" style="max-height: 420px; overflow-y: auto;">
					<p class="text-muted">No users found.</p>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">{vtranslate('LBL_CANCEL', 'Vtiger')}</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	(function() {
		var roleSelect = document.getElementById('branchusers_roleid');
		var parentContainer = document.getElementById('parentUserContainer');
		var chooseBtn = document.getElementById('chooseParentUserBtn');
		var parentUserIdField = document.getElementById('parent_user_id');
		var parentUserLabel = document.getElementById('parent_user_label');
		var modalTitle = document.getElementById('roleUsersModalTitle');
		var treeContainer = document.getElementById('roleUsersTreeContainer');
		var firstChildRoleId = '{$FIRST_CHILD_ROLE_ID|escape:'javascript'}';
		var currentUserId = '{$CURRENT_USER_ID|escape:'javascript'}';
		var currentUserLabel = '{$CURRENT_USER_LABEL|escape:'javascript'}';
		var recordId = '{$RECORD_ID|escape:'javascript'}';

		function toggleParentChooser() {
			if (!roleSelect || !parentContainer) return;
			parentContainer.style.display = roleSelect.value ? 'block' : 'none';
		}

		function applyFirstChildRoleParentRule() {
			if (!roleSelect || !chooseBtn || !parentUserIdField || !parentUserLabel) return false;
			if (firstChildRoleId && roleSelect.value === firstChildRoleId) {
				parentUserIdField.value = currentUserId || '';
				parentUserLabel.value = currentUserLabel || '';
				chooseBtn.setAttribute('disabled', 'disabled');
				return true;
			}
			chooseBtn.removeAttribute('disabled');
			return false;
		}

		function renderRoleUsers(roleLabel, users) {
			modalTitle.textContent = roleLabel ? ('Select User - ' + roleLabel) : 'Select User';
			if (!users || users.length === 0) {
				treeContainer.innerHTML = '<p class="text-muted">No users found for selected role.</p>';
				return;
			}
			var html = '';
			html += '<div class="listViewPageDiv">';
			html += '  <div class="col-sm-12 col-xs-12">';
			html += '    <div class="clearfix treeView">';
			html += '      <ul>';
			html += '        <li data-roleid="' + (roleSelect ? roleSelect.value : '') + '">';
			html += '          <div class="toolbar-handle">';
			html += '            <a href="javascript:;" class="btn app-MARKETING droppable">' + (roleLabel || 'Selected Role') + '</a>';
			html += '          </div>';
			html += '          <ul>';
			for (var i = 0; i < users.length; i++) {
				var u = users[i];
				var fullNameEscaped = (u.full_name || '').replace(/"/g, '&quot;');
				html += '            <li class="role-user-row" data-userid="' + u.id + '" data-label="' + fullNameEscaped + '">';
				html += '              <div class="toolbar-handle">';
				html += '                <a href="javascript:;" class="btn btn-default roleEle">' + u.full_name + ' <span class="text-muted">(' + u.user_name + ')</span></a>';
								html += '              </div>';
				html += '          </li>';
			}
			html += '          </ul>';
			html += '        </li>';
			html += '      </ul>';
			html += '    </div>';
			html += '  </div>';
			html += '</div>';
			treeContainer.innerHTML = html;
		}

		function selectParentUser(id, label) {
			parentUserIdField.value = id || '';
			parentUserLabel.value = label || '';
			jQuery('#roleUsersModal').modal('hide');
		}

		function fetchRoleUsers() {
			if (chooseBtn && chooseBtn.hasAttribute('disabled')) {
				return;
			}
			if (!roleSelect.value) {
				if (typeof app !== 'undefined' && app.helper && app.helper.showErrorNotification) {
					var notifyParams = new Object();
					notifyParams.message = 'Please select Role first';
					app.helper.showErrorNotification(notifyParams);
				}
				return;
			}
			var url = 'index.php?module={$MODULE}&action=GetRoleUsers&roleid=' + encodeURIComponent(roleSelect.value) + '&ajax=1';
			if (recordId) {
				url += '&exclude_user_id=' + encodeURIComponent(recordId);
			}
			jQuery.getJSON(url).done(function(res) {
				if (res && res.success && res.result) {
					renderRoleUsers(res.result.roleLabel, res.result.users);
					jQuery('#roleUsersModal').modal('show');
				}
			});
		}

		document.addEventListener('click', function(e) {
			var chooseEl = e.target ? e.target.closest('.choose-role-user') : null;
			if (chooseEl) {
				e.preventDefault();
				selectParentUser(chooseEl.getAttribute('data-id'), chooseEl.getAttribute('data-label'));
				return;
			}
			var rowEl = e.target ? e.target.closest('.role-user-row') : null;
			if (rowEl) {
				e.preventDefault();
				selectParentUser(rowEl.getAttribute('data-userid'), rowEl.getAttribute('data-label'));
			}
		});

		if (roleSelect) {
			roleSelect.addEventListener('change', function() {
				parentUserIdField.value = '';
				parentUserLabel.value = '';
				applyFirstChildRoleParentRule();
				toggleParentChooser();
			});
		}
		if (chooseBtn) {
			chooseBtn.addEventListener('click', fetchRoleUsers);
		}
		applyFirstChildRoleParentRule();
		toggleParentChooser();
	})();
</script>

