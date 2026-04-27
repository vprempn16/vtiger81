<?php
/* Smarty version 4.3.2, created on 2026-04-21 05:23:29
  from '/var/www/html/vtiger81/layouts/v7/modules/BranchUsers/EditView.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_69e709d12d6f14_92988088',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '72009b74af441b694d34e8f3d126f095ace6833a' => 
    array (
      0 => '/var/www/html/vtiger81/layouts/v7/modules/BranchUsers/EditView.tpl',
      1 => 1776418004,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69e709d12d6f14_92988088 (Smarty_Internal_Template $_smarty_tpl) {
?>
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
						<?php if ($_smarty_tpl->tpl_vars['RECORD_ID']->value) {?>
							<?php echo vtranslate('LBL_EDIT','Vtiger');?>
 <?php echo vtranslate('LBL_USER','Vtiger');?>

						<?php } else { ?>
							<?php echo vtranslate('LBL_ADD','Vtiger');?>
 <?php echo vtranslate('LBL_USER','Vtiger');?>

						<?php }?>
					</h4>
				</div>
			</div>
		</div>
		<hr />
		<div class="editViewBody">
			<div class="editViewContents">
		<input type="hidden" name="module" value="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
" />
		<input type="hidden" name="action" value="Save" />
		<?php if ($_smarty_tpl->tpl_vars['RECORD_ID']->value) {?><input type="hidden" name="record" value="<?php echo $_smarty_tpl->tpl_vars['RECORD_ID']->value;?>
" /><?php }?>
		<input type="hidden" name="parent_user_id" id="parent_user_id" value="<?php echo $_smarty_tpl->tpl_vars['SELECTED_PARENT_USER_ID']->value;?>
" />

		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<label class="control-label col-sm-4"><?php echo vtranslate('User Name','Users');?>
</label>
					<div class="col-sm-8">
						<input class="form-control" type="text" name="user_name" value="<?php echo $_smarty_tpl->tpl_vars['USER']->value['user_name'];?>
" <?php if ($_smarty_tpl->tpl_vars['RECORD_ID']->value) {?>readonly<?php }?> required />
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4"><?php echo vtranslate('First Name','Users');?>
</label>
					<div class="col-sm-8">
						<input class="form-control" type="text" name="first_name" value="<?php echo $_smarty_tpl->tpl_vars['USER']->value['first_name'];?>
" />
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4"><?php echo vtranslate('Last Name','Users');?>
</label>
					<div class="col-sm-8">
						<input class="form-control" type="text" name="last_name" value="<?php echo $_smarty_tpl->tpl_vars['USER']->value['last_name'];?>
" required />
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4">Primary Email</label>
					<div class="col-sm-8">
						<input class="form-control" type="email" name="email1" value="<?php echo $_smarty_tpl->tpl_vars['USER']->value['email1'];?>
" />
					</div>
				</div>
			</div>

			<div class="col-lg-6">
				<div class="form-group">
					<label class="control-label col-sm-4"><?php echo vtranslate('Status','Users');?>
</label>
					<div class="col-sm-8">
						<select class="form-control" name="status">
							<option value="Active" <?php if ($_smarty_tpl->tpl_vars['USER']->value['status'] == 'Active') {?>selected<?php }?>><?php echo vtranslate('Active','Users');?>
</option>
							<option value="Inactive" <?php if ($_smarty_tpl->tpl_vars['USER']->value['status'] == 'Inactive') {?>selected<?php }?>><?php echo vtranslate('Inactive','Users');?>
</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4"><?php echo vtranslate('Role','Users');?>
</label>
					<div class="col-sm-8">
						<select class="form-control" name="roleid" id="branchusers_roleid" required>
							<option value=""><?php echo vtranslate('LBL_SELECT_OPTION','Vtiger');?>
</option>
							<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['AVAILABLE_ROLES']->value, 'ROLE');
$_smarty_tpl->tpl_vars['ROLE']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['ROLE']->value) {
$_smarty_tpl->tpl_vars['ROLE']->do_else = false;
?>
								<?php if ($_smarty_tpl->tpl_vars['ROLE']->value['roleid'] != $_smarty_tpl->tpl_vars['CURRENT_USER_ROLE_ID']->value) {?>
									<option value="<?php echo $_smarty_tpl->tpl_vars['ROLE']->value['roleid'];?>
" <?php if ($_smarty_tpl->tpl_vars['SELECTED_ROLE_ID']->value == $_smarty_tpl->tpl_vars['ROLE']->value['roleid']) {?>selected<?php }?>>
										<?php echo $_smarty_tpl->tpl_vars['ROLE']->value['rolename'];?>

									</option>
								<?php }?>
							<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
						</select>
					</div>
				</div>
				<div class="form-group" id="parentUserContainer" style="display:none;">
					<label class="control-label col-sm-4">Select Parent User</label>
					<div class="col-sm-8">
						<div class="input-group">
							<input class="form-control" type="text" id="parent_user_label" value="<?php echo $_smarty_tpl->tpl_vars['SELECTED_PARENT_USER_LABEL']->value;?>
" readonly />
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
						<?php if ($_smarty_tpl->tpl_vars['RECORD_ID']->value) {?>
							<?php echo vtranslate('LBL_NEW_PASSWORD','Users');?>

						<?php } else { ?>
							<?php echo vtranslate('Password','Users');?>

						<?php }?>
					</label>
					<div class="col-sm-8">
						<input class="form-control" type="password" name="new_password" <?php if (!$_smarty_tpl->tpl_vars['RECORD_ID']->value) {?>required<?php }?> />
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
						<?php echo vtranslate('LBL_SAVE','Vtiger');?>

					</button>
					&nbsp;&nbsp;
					<a class="cancelLink" href="index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=List" onclick="window.onbeforeunload = null;">
						<?php echo vtranslate('LBL_CANCEL','Vtiger');?>

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
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo vtranslate('LBL_CANCEL','Vtiger');?>
</button>
			</div>
		</div>
	</div>
</div>

<?php echo '<script'; ?>
 type="text/javascript">
	(function() {
		var roleSelect = document.getElementById('branchusers_roleid');
		var parentContainer = document.getElementById('parentUserContainer');
		var chooseBtn = document.getElementById('chooseParentUserBtn');
		var parentUserIdField = document.getElementById('parent_user_id');
		var parentUserLabel = document.getElementById('parent_user_label');
		var modalTitle = document.getElementById('roleUsersModalTitle');
		var treeContainer = document.getElementById('roleUsersTreeContainer');
		var firstChildRoleId = '<?php echo strtr((string)$_smarty_tpl->tpl_vars['FIRST_CHILD_ROLE_ID']->value, array("\\" => "\\\\", "'" => "\\'", "\"" => "\\\"", "\r" => "\\r", 
                       "\n" => "\\n", "</" => "<\/", "<!--" => "<\!--", "<s" => "<\s", "<S" => "<\S",
                       "`" => "\\`", "\${" => "\\\$\{"));?>
';
		var currentUserId = '<?php echo strtr((string)$_smarty_tpl->tpl_vars['CURRENT_USER_ID']->value, array("\\" => "\\\\", "'" => "\\'", "\"" => "\\\"", "\r" => "\\r", 
                       "\n" => "\\n", "</" => "<\/", "<!--" => "<\!--", "<s" => "<\s", "<S" => "<\S",
                       "`" => "\\`", "\${" => "\\\$\{"));?>
';
		var currentUserLabel = '<?php echo strtr((string)$_smarty_tpl->tpl_vars['CURRENT_USER_LABEL']->value, array("\\" => "\\\\", "'" => "\\'", "\"" => "\\\"", "\r" => "\\r", 
                       "\n" => "\\n", "</" => "<\/", "<!--" => "<\!--", "<s" => "<\s", "<S" => "<\S",
                       "`" => "\\`", "\${" => "\\\$\{"));?>
';
		var recordId = '<?php echo strtr((string)$_smarty_tpl->tpl_vars['RECORD_ID']->value, array("\\" => "\\\\", "'" => "\\'", "\"" => "\\\"", "\r" => "\\r", 
                       "\n" => "\\n", "</" => "<\/", "<!--" => "<\!--", "<s" => "<\s", "<S" => "<\S",
                       "`" => "\\`", "\${" => "\\\$\{"));?>
';

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
			var url = 'index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&action=GetRoleUsers&roleid=' + encodeURIComponent(roleSelect.value) + '&ajax=1';
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
<?php echo '</script'; ?>
>

<?php }
}
