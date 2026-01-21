<?php
/* Smarty version 4.3.2, created on 2025-09-06 12:10:27
  from '/var/www/html/vtiger81/layouts/v7/modules/BOM/GeneratePOPopup.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_68bc24b388e588_65932969',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ae5446ddbc33ce626e77782dd677e9f978131bd9' => 
    array (
      0 => '/var/www/html/vtiger81/layouts/v7/modules/BOM/GeneratePOPopup.tpl',
      1 => 1757156496,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68bc24b388e588_65932969 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="modal-dialog modal-lg">
<div class="modal-content">
	<div class="modal-header">
    		<h4 class="modal-title">Generate Purchase Order</h4>
	</div>
	<div class="modal-body">
    		<input type="hidden" id="bomRecordId" value="<?php echo $_smarty_tpl->tpl_vars['BOM_RECORD_ID']->value;?>
" />
<div class="container-fluid">
	<table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th style="width:70%">Product Name</th>
                <th style="width:30%">Quantity</th>
            </tr>
        </thead>
        <tbody>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['VENDOR_PRODUCTS']->value, 'vendorBlock', false, 'vendorName');
$_smarty_tpl->tpl_vars['vendorBlock']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['vendorName']->value => $_smarty_tpl->tpl_vars['vendorBlock']->value) {
$_smarty_tpl->tpl_vars['vendorBlock']->do_else = false;
?>
                <tr class="table-active vendor-row">
                    <td colspan="2"><b><?php echo $_smarty_tpl->tpl_vars['vendorName']->value;?>
</b></td>
                </tr>
                <input type="hidden" class="vendorId" value="<?php echo $_smarty_tpl->tpl_vars['vendorBlock']->value['vendorid'];?>
" />

                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['vendorBlock']->value['products'], 'product');
$_smarty_tpl->tpl_vars['product']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['product']->value) {
$_smarty_tpl->tpl_vars['product']->do_else = false;
?>
                    <tr>
                        <td>
                            <?php echo $_smarty_tpl->tpl_vars['product']->value['productname'];?>

                            <input type="hidden" class="productId" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['productid'];?>
" />
                            <input type="hidden" class="sequenceNo" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['sequence_no'];?>
" />
                        </td>
                        <td>
                            <?php echo $_smarty_tpl->tpl_vars['product']->value['qty'];?>

                            <input type="hidden" class="productQty" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['qty'];?>
" />
                        </td>
                    </tr>
                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </tbody>
    </table>
	</div>
	</div>
	<div class="modal-footer">
 	   	<button class="btn btn-success" id="generatePOBtn">Generate</button>
    		<button class="btn btn-danger" data-dismiss="modal">Cancel</button>
	</div>
 </div>
</div>

<?php }
}
