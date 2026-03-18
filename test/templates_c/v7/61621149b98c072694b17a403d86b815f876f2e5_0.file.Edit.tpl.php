<?php
/* Smarty version 4.3.2, created on 2026-03-18 11:50:42
  from '/var/www/html/vtiger81/layouts/v7/modules/Settings/VTAtomCommentsMentions/Edit.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_69ba91923a8859_22026633',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '61621149b98c072694b17a403d86b815f876f2e5' => 
    array (
      0 => '/var/www/html/vtiger81/layouts/v7/modules/Settings/VTAtomCommentsMentions/Edit.tpl',
      1 => 1773830737,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69ba91923a8859_22026633 (Smarty_Internal_Template $_smarty_tpl) {
?>                                                                                                                                                                                               <div class="editViewPageDiv editViewContainer" id="EditViewOutgoing" style="padding-top:0px;"><div class="col-lg-12 col-md-12 col-sm-12"><div><h3 style="margin-top: 0px;"><?php echo vtranslate('VT Atom Comments Mention',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</h3></div><form id="VtAtomCommentConfig" data-detail-url="<?php echo $_smarty_tpl->tpl_vars['LSITVIEWURL']->value;?>
" method="POST"><input type="hidden" name="id" value="<?php echo $_smarty_tpl->tpl_vars['recordId']->value;?>
"/><div class="blockData"><br><div class="block"><div><h4><?php echo vtranslate('VtAtom Comments Mention',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</h4></div><hr><table class="table editview-table no-border"><tbody><tr><td class="<?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
 fieldLabel"><label><?php echo vtranslate('Comment mentions',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</label></td><td class="<?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
 fieldValue"><div class=" col-lg-6 col-md-6 col-sm-12"><input type="checkbox" class="vtatom-commentcheck" name="comment_mentions" data-linklabel="VtAtomCommentMentions" data-type="comment_mentions" <?php if ($_smarty_tpl->tpl_vars['RECORD']->value['comment_mentions'] == 'on') {?>checked<?php }?> ></div></td></tr></tbody></table><br><div style="padding: 5px;" class="block cmt-sendmail-block <?php if ($_smarty_tpl->tpl_vars['RECORD']->value['comment_mentions'] == 'off') {?>hide<?php }?>"><table class="table editview-table no-border"><tbody><tr><td class="<?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
 fieldLabel"><label><?php echo vtranslate('Send Mail ',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</label></td><td class="<?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
 fieldValue"><div class=" col-lg-6 col-md-6 col-sm-12"><input type="checkbox" class="vtatom-commentcheck" name="send_commentmail" data-type="send_commentmail" <?php if ($_smarty_tpl->tpl_vars['RECORD']->value['send_commentmail'] == 'on') {?>checked<?php }?> ></div></td></tr></tbody></table></div><br></div></div></form></div></div><?php }
}
