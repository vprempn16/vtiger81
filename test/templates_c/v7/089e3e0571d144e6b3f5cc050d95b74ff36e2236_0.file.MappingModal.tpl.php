<?php
/* Smarty version 4.3.2, created on 2026-03-11 14:10:09
  from '/var/www/html/vtiger81/layouts/v7/modules/Settings/Whatsapp/MappingModal.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_69b177c169c137_45790591',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '089e3e0571d144e6b3f5cc050d95b74ff36e2236' => 
    array (
      0 => '/var/www/html/vtiger81/layouts/v7/modules/Settings/Whatsapp/MappingModal.tpl',
      1 => 1773215310,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b177c169c137_45790591 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3 class="modal-title"><?php echo vtranslate('LBL_MAP_TEMPLATE',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
: <?php echo $_smarty_tpl->tpl_vars['TEMPLATE_NAME']->value;?>
</h3>
        </div>
        <div class="modal-body" style="padding: 20px;">
            <form class="form-horizontal" id="mappingForm">
                <input type="hidden" name="template_id" value="<?php echo $_smarty_tpl->tpl_vars['TEMPLATE_ID']->value;?>
" />
                <input type="hidden" name="meta_template_id" value="<?php echo $_smarty_tpl->tpl_vars['META_TEMPLATE_ID']->value;?>
" />
                <input type="hidden" name="template_language" value="<?php echo $_smarty_tpl->tpl_vars['TEMPLATE_LANGUAGE']->value;?>
" />
                
                <div class="form-group">
                    <label class="control-label col-lg-3"><?php echo vtranslate('LBL_SELECT_MODULE',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</label>
                    <div class="col-lg-8">
                        <select class="select2" name="crm_module" id="crm_module" style="width: 100%;">
                            <option value=""><?php echo vtranslate('LBL_SELECT_OPTION',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</option>
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['MODULES']->value, 'MODULE_MODEL', false, 'TAB_ID');
$_smarty_tpl->tpl_vars['MODULE_MODEL']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['TAB_ID']->value => $_smarty_tpl->tpl_vars['MODULE_MODEL']->value) {
$_smarty_tpl->tpl_vars['MODULE_MODEL']->do_else = false;
?>
                                <option value="<?php echo $_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getName();?>
" <?php if ($_smarty_tpl->tpl_vars['MAPPED_MODULE']->value == $_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getName()) {?>selected<?php }?>>
                                    <?php echo vtranslate($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getName(),$_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getName());?>

                                </option>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        </select>
                    </div>
                </div>

                <hr />

                <div id="mappingContainer">
                    <?php if (!empty($_smarty_tpl->tpl_vars['GROUPED_VARIABLES']->value)) {?>
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['GROUPED_VARIABLES']->value, 'GROUP_VARS', false, 'GROUP_TYPE');
$_smarty_tpl->tpl_vars['GROUP_VARS']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['GROUP_TYPE']->value => $_smarty_tpl->tpl_vars['GROUP_VARS']->value) {
$_smarty_tpl->tpl_vars['GROUP_VARS']->do_else = false;
?>
                            <h4 style="margin-top: 20px; margin-bottom: 15px;"><strong><?php echo ucfirst(strtolower($_smarty_tpl->tpl_vars['GROUP_TYPE']->value));?>
</strong></h4>
                            
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['GROUP_VARS']->value, 'VARIABLE');
$_smarty_tpl->tpl_vars['VARIABLE']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['VARIABLE']->value) {
$_smarty_tpl->tpl_vars['VARIABLE']->do_else = false;
?>
                                <?php $_smarty_tpl->_assignInScope('VAR_NAME', $_smarty_tpl->tpl_vars['VARIABLE']->value['name']);?>
                                <div class="form-group" style="margin-bottom: 5px;">
                                    <label class="control-label col-lg-3" style="text-align: left; padding-top: 6px;">
                                        <?php if ($_smarty_tpl->tpl_vars['VARIABLE']->value['example']) {?>
                                            <?php echo $_smarty_tpl->tpl_vars['VARIABLE']->value['key'];?>
 &rarr; <?php echo $_smarty_tpl->tpl_vars['VARIABLE']->value['example'];?>

                                        <?php } else { ?>
                                            <?php echo $_smarty_tpl->tpl_vars['VARIABLE']->value['key'];?>

                                        <?php }?>
                                        <?php if ($_smarty_tpl->tpl_vars['VARIABLE']->value['context']) {?>
                                            <div style="font-weight: normal; font-size: 11px; margin-top: 3px;"><?php echo $_smarty_tpl->tpl_vars['VARIABLE']->value['context'];?>
</div>
                                        <?php }?>
                                    </label>
                                    <div class="col-lg-8" style="padding-top: 3px;">
                                        <select class="select2 crm-field-select" name="mapping[<?php echo $_smarty_tpl->tpl_vars['VARIABLE']->value['type'];?>
][<?php echo $_smarty_tpl->tpl_vars['VARIABLE']->value['name'];?>
]" style="width: 100%;">
                                            <option value=""><?php echo vtranslate('LBL_SELECT_OPTION',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</option>
                                            <?php if (!empty($_smarty_tpl->tpl_vars['MODULE_FIELDS']->value)) {?>
                                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['MODULE_FIELDS']->value, 'FIELD_LABEL', false, 'FIELD_NAME');
$_smarty_tpl->tpl_vars['FIELD_LABEL']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['FIELD_NAME']->value => $_smarty_tpl->tpl_vars['FIELD_LABEL']->value) {
$_smarty_tpl->tpl_vars['FIELD_LABEL']->do_else = false;
?>
                                                    <option value="<?php echo $_smarty_tpl->tpl_vars['FIELD_NAME']->value;?>
" <?php if ((isset($_smarty_tpl->tpl_vars['MAPPINGS']->value[$_smarty_tpl->tpl_vars['VAR_NAME']->value])) && $_smarty_tpl->tpl_vars['MAPPINGS']->value[$_smarty_tpl->tpl_vars['VAR_NAME']->value]['crm_field'] == $_smarty_tpl->tpl_vars['FIELD_NAME']->value) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['FIELD_LABEL']->value;?>
</option>
                                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

                        <hr style="margin-top: 30px; margin-bottom: 20px;" />
                        
                        <h4 style="margin-bottom: 15px;">Template Preview</h4>
                        <div style="background-color: #fafbfc; border: 1px solid #e1e4e8; padding: 20px; border-radius: 6px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; color: #24292e; max-width: 100%;">
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['COMPONENTS']->value, 'COMP');
$_smarty_tpl->tpl_vars['COMP']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['COMP']->value) {
$_smarty_tpl->tpl_vars['COMP']->do_else = false;
?>
                                <?php if ($_smarty_tpl->tpl_vars['COMP']->value['type'] == 'HEADER') {?>
                                    <?php if ($_smarty_tpl->tpl_vars['COMP']->value['format'] == 'TEXT') {?>
                                        <div style="font-weight: 600; font-size: 16px; margin-bottom: 10px;"><?php echo $_smarty_tpl->tpl_vars['COMP']->value['text'];?>
</div>
                                    <?php } elseif ($_smarty_tpl->tpl_vars['COMP']->value['format'] == 'IMAGE') {?>
                                        <div style="background: #e1e4e8; height: 120px; width: 100%; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; border-radius: 4px; color: #586069;">[ IMAGE HEADER ]</div>
                                    <?php } elseif ($_smarty_tpl->tpl_vars['COMP']->value['format'] == 'DOCUMENT') {?>
                                        <div style="background: #e1e4e8; padding: 12px; margin-bottom: 15px; border-radius: 4px; color: #586069;">&#128196; [ DOCUMENT HEADER ]</div>
                                    <?php } elseif ($_smarty_tpl->tpl_vars['COMP']->value['format'] == 'VIDEO') {?>
                                        <div style="background: #e1e4e8; padding: 12px; margin-bottom: 15px; border-radius: 4px; color: #586069;">&#127909; [ VIDEO HEADER ]</div>
                                    <?php }?>
                                <?php } elseif ($_smarty_tpl->tpl_vars['COMP']->value['type'] == 'BODY') {?>
                                    <div style="white-space: pre-wrap; font-size: 14px; line-height: 1.5; margin-bottom: 10px;"><?php echo $_smarty_tpl->tpl_vars['COMP']->value['text'];?>
</div>
                                <?php } elseif ($_smarty_tpl->tpl_vars['COMP']->value['type'] == 'FOOTER') {?>
                                    <div style="color: #586069; font-size: 13px; margin-top: 10px;"><?php echo $_smarty_tpl->tpl_vars['COMP']->value['text'];?>
</div>
                                <?php } elseif ($_smarty_tpl->tpl_vars['COMP']->value['type'] == 'BUTTONS') {?>
                                    <div style="margin-top: 15px; display: flex; flex-direction: column; gap: 8px;">
                                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['COMP']->value['buttons'], 'BTN');
$_smarty_tpl->tpl_vars['BTN']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['BTN']->value) {
$_smarty_tpl->tpl_vars['BTN']->do_else = false;
?>
                                        <div style="background-color: #ffffff; border: 1px solid #e1e4e8; color: #0366d6; text-align: center; padding: 8px 16px; border-radius: 4px; font-size: 14px; display: inline-block; cursor: default;">
                                            <?php if ($_smarty_tpl->tpl_vars['BTN']->value['type'] == 'URL') {?>
                                                <i class="fa fa-external-link"></i>
                                            <?php } elseif ($_smarty_tpl->tpl_vars['BTN']->value['type'] == 'PHONE_NUMBER') {?>
                                                <i class="fa fa-phone"></i>
                                            <?php } elseif ($_smarty_tpl->tpl_vars['BTN']->value['type'] == 'QUICK_REPLY') {?>
                                                <i class="fa fa-reply"></i>
                                            <?php }?>
                                            <?php echo $_smarty_tpl->tpl_vars['BTN']->value['text'];?>

                                        </div>
                                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                    </div>
                                <?php }?>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        </div>

                    <?php } else { ?>
                        <div class="alert alert-info">
                            <?php echo vtranslate('LBL_NO_VARIABLES_FOUND',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>

                        </div>
                    <?php }?>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo vtranslate('LBL_CANCEL',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</button>
            <button type="button" class="btn btn-primary" id="saveMappingBtn"><?php echo vtranslate('LBL_SAVE',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</button>
        </div>
    </div>
</div>
<?php }
}
