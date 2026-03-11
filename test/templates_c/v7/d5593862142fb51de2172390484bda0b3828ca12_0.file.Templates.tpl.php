<?php
/* Smarty version 4.3.2, created on 2026-03-11 14:10:07
  from '/var/www/html/vtiger81/layouts/v7/modules/Settings/Whatsapp/Templates.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_69b177bf496255_92661246',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd5593862142fb51de2172390484bda0b3828ca12' => 
    array (
      0 => '/var/www/html/vtiger81/layouts/v7/modules/Settings/Whatsapp/Templates.tpl',
      1 => 1773149866,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b177bf496255_92661246 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="listViewPageDiv" id="listViewContainer" style="padding: 20px;">
    <div class="listViewTopMenuDiv">
        <div class="listViewActionsContainer clearfix">
            <div class="btn-group listViewActions pull-left">
                <select class="select2" id="channelFilter" style="width: 250px;">
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['CHANNELS']->value, 'CHANNEL');
$_smarty_tpl->tpl_vars['CHANNEL']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['CHANNEL']->value) {
$_smarty_tpl->tpl_vars['CHANNEL']->do_else = false;
?>
                        <option value="<?php echo $_smarty_tpl->tpl_vars['CHANNEL']->value->getId();?>
" <?php if ($_smarty_tpl->tpl_vars['SELECTED_CHANNEL_ID']->value == $_smarty_tpl->tpl_vars['CHANNEL']->value->getId()) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['CHANNEL']->value->getName();?>
</option>
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                </select>
            </div>
            <?php if ($_smarty_tpl->tpl_vars['SELECTED_CHANNEL_ID']->value) {?>
            <div class="btn-group listViewActions pull-right">
                <button class="btn btn-primary" id="syncTemplates" data-channel-id="<?php echo $_smarty_tpl->tpl_vars['SELECTED_CHANNEL_ID']->value;?>
">
                    <i class="fa fa-refresh"></i>&nbsp;<strong><?php echo vtranslate('LBL_SYNC_TEMPLATES',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</strong>
                </button>
            </div>
            <?php }?>
        </div>
    </div>
    <div class="listViewContentDiv" id="listViewContents" style="margin-top: 20px;">
        <table class="table table-bordered listViewEntriesTable">
            <thead>
                <tr class="listViewHeaders">
                    <th width="30%"><?php echo vtranslate('LBL_TEMPLATE_NAME',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</th>
                    <th width="20%"><?php echo vtranslate('LBL_CATEGORY',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</th>
                    <th width="15%"><?php echo vtranslate('LBL_LANGUAGE',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</th>
                    <th width="15%"><?php echo vtranslate('LBL_STATUS',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</th>
                    <th width="20%" style="text-align: center;"><?php echo vtranslate('LBL_ACTIONS',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</th>
                </tr>
            </thead>
            <tbody>
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['TEMPLATES']->value, 'TEMPLATE');
$_smarty_tpl->tpl_vars['TEMPLATE']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['TEMPLATE']->value) {
$_smarty_tpl->tpl_vars['TEMPLATE']->do_else = false;
?>
                    <tr class="listViewEntries" data-id="<?php echo $_smarty_tpl->tpl_vars['TEMPLATE']->value->getId();?>
">
                        <td><?php echo $_smarty_tpl->tpl_vars['TEMPLATE']->value->get('template_name');?>
</td>
                        <td><?php echo $_smarty_tpl->tpl_vars['TEMPLATE']->value->get('category');?>
</td>
                        <td><?php echo $_smarty_tpl->tpl_vars['TEMPLATE']->value->get('language');?>
</td>
                        <td>
                            <span class="label <?php if ($_smarty_tpl->tpl_vars['TEMPLATE']->value->get('status') == 'APPROVED') {?>label-success<?php } else { ?>label-info<?php }?>">
                                <?php echo $_smarty_tpl->tpl_vars['TEMPLATE']->value->get('status');?>

                            </span>
                        </td>
                        <td style="text-align: center;">
                            <a href="javascript:void(0);" class="templateMapping" data-id="<?php echo $_smarty_tpl->tpl_vars['TEMPLATE']->value->getId();?>
" title="<?php echo vtranslate('LBL_MAPPING',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
">
                                <i class="fa fa-exchange" style="font-size: 16px;"></i>
                            </a>
                        </td>
                    </tr>
                <?php
}
if ($_smarty_tpl->tpl_vars['TEMPLATE']->do_else) {
?>
                    <tr class="emptyRecordsDiv">
                        <td colspan="5" class="emptyRecordsMessage" style="text-align: center; padding: 50px;">
                            <p><?php echo vtranslate('LBL_NO_TEMPLATES_FOUND',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</p>
                            <br/>
                            <button class="btn btn-primary btn-lg" id="syncTemplatesCenter" data-channel-id="<?php echo $_smarty_tpl->tpl_vars['SELECTED_CHANNEL_ID']->value;?>
">
                                <i class="fa fa-refresh"></i>&nbsp;<strong><?php echo vtranslate('LBL_SYNC_TEMPLATES',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</strong>
                            </button>
                        </td>
                    </tr>
                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            </tbody>
        </table>
    </div>
</div>
</div>
<?php }
}
