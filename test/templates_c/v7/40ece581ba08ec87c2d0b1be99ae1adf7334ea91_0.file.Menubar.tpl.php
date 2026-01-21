<?php
/* Smarty version 4.3.2, created on 2025-09-06 11:20:17
  from '/var/www/html/vtiger81/layouts/v7/modules/Vtiger/partials/Menubar.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_68bc18f1d038e6_76865578',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '40ece581ba08ec87c2d0b1be99ae1adf7334ea91' => 
    array (
      0 => '/var/www/html/vtiger81/layouts/v7/modules/Vtiger/partials/Menubar.tpl',
      1 => 1693558649,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68bc18f1d038e6_76865578 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['MENU_STRUCTURE']->value) {
$_smarty_tpl->_assignInScope('topMenus', $_smarty_tpl->tpl_vars['MENU_STRUCTURE']->value->getTop());
$_smarty_tpl->_assignInScope('moreMenus', $_smarty_tpl->tpl_vars['MENU_STRUCTURE']->value->getMore());?>

<div id="modules-menu" class="modules-menu">
	<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['SELECTED_CATEGORY_MENU_LIST']->value, 'moduleModel', false, 'moduleName');
$_smarty_tpl->tpl_vars['moduleModel']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['moduleName']->value => $_smarty_tpl->tpl_vars['moduleModel']->value) {
$_smarty_tpl->tpl_vars['moduleModel']->do_else = false;
?>
		<?php $_smarty_tpl->_assignInScope('translatedModuleLabel', vtranslate($_smarty_tpl->tpl_vars['moduleModel']->value->get('label'),$_smarty_tpl->tpl_vars['moduleName']->value));?>
		<ul title="<?php echo $_smarty_tpl->tpl_vars['translatedModuleLabel']->value;?>
" class="module-qtip">
			<li <?php if ($_smarty_tpl->tpl_vars['MODULE']->value == $_smarty_tpl->tpl_vars['moduleName']->value) {?>class="active"<?php } else { ?>class=""<?php }?>>
				<a href="<?php echo $_smarty_tpl->tpl_vars['moduleModel']->value->getDefaultUrl();?>
&app=<?php echo $_smarty_tpl->tpl_vars['SELECTED_MENU_CATEGORY']->value;?>
">
					<?php echo $_smarty_tpl->tpl_vars['moduleModel']->value->getModuleIcon();?>

					<span><?php echo $_smarty_tpl->tpl_vars['translatedModuleLabel']->value;?>
</span>
				</a>
			</li>
		</ul>
	<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
</div>
<?php }
}
}
