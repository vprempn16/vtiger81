<?php
/* Smarty version 4.3.2, created on 2025-09-06 11:24:19
  from '/var/www/html/vtiger81/layouts/v7/modules/Products/MoreCurrenciesList.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_68bc19e3cbdce3_83642573',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd54f746e39811e6fb2471835644c388f790048bf' => 
    array (
      0 => '/var/www/html/vtiger81/layouts/v7/modules/Products/MoreCurrenciesList.tpl',
      1 => 1693558649,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68bc19e3cbdce3_83642573 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div id="currency_class" class="multiCurrencyEditUI modelContainer">
	<div class = "modal-dialog modal-lg">
		<div class = "modal-content">
			<?php ob_start();
echo vtranslate('LBL_PRICES',$_smarty_tpl->tpl_vars['MODULE']->value);
$_prefixVariable1=ob_get_clean();
$_smarty_tpl->_assignInScope('TITLE', $_prefixVariable1);?>
			<?php $_smarty_tpl->_subTemplateRender(vtemplate_path("ModalHeader.tpl",$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('TITLE'=>$_smarty_tpl->tpl_vars['TITLE']->value), 0, true);
?>
			<div class="multiCurrencyContainer">
				<div class = "currencyContent">
					<div class = "modal-body">
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="table listViewEntriesTable">
							<thead class="detailedViewHeader">
							<th><?php echo vtranslate('LBL_CURRENCY',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</th>
							<th><?php echo vtranslate('LBL_PRICE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</th>
							<th><?php echo vtranslate('LBL_CONVERSION_RATE','Products');?>
</th>
							<th><?php echo vtranslate('LBL_RESET_PRICE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</th>
							<th><?php echo vtranslate('LBL_BASE_CURRENCY',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</th>
							</thead>
							<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['PRICE_DETAILS']->value, 'price', false, 'count');
$_smarty_tpl->tpl_vars['price']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['count']->value => $_smarty_tpl->tpl_vars['price']->value) {
$_smarty_tpl->tpl_vars['price']->do_else = false;
?>
								<tr data-currency-id=<?php echo $_smarty_tpl->tpl_vars['price']->value['curname'];?>
>
									<?php if ($_smarty_tpl->tpl_vars['price']->value['check_value'] == 1 || $_smarty_tpl->tpl_vars['price']->value['is_basecurrency'] == 1) {?>
										<?php $_smarty_tpl->_assignInScope('check_value', "checked");?>
										<?php $_smarty_tpl->_assignInScope('disable_value', '');?>
									<?php } else { ?>
										<?php $_smarty_tpl->_assignInScope('check_value', '');?>
										<?php $_smarty_tpl->_assignInScope('disable_value', "disabled=true");?>
									<?php }?>

									<?php if ($_smarty_tpl->tpl_vars['price']->value['is_basecurrency'] == 1) {?>
										<?php $_smarty_tpl->_assignInScope('base_cur_check', "checked");?>
									<?php } else { ?>
										<?php $_smarty_tpl->_assignInScope('base_cur_check', '');?>
									<?php }?>
									<td>
										<div class="row col-lg-12">
											<div class="col-lg-10 currencyInfo"  style = "padding-left:5px">
												<span class="pull-left currencyName" ><?php echo getTranslatedCurrencyString($_smarty_tpl->tpl_vars['price']->value['currencylabel']);?>
 (<span class='currencySymbol'><?php echo $_smarty_tpl->tpl_vars['price']->value['currencysymbol'];?>
</span>)</span>
											</div>
											<div class="col-lg-2">
												<span><input type="checkbox" name="cur_<?php echo $_smarty_tpl->tpl_vars['price']->value['curid'];?>
_check" id="cur_<?php echo $_smarty_tpl->tpl_vars['price']->value['curid'];?>
_check" class="pull-right enableCurrency" <?php echo $_smarty_tpl->tpl_vars['check_value']->value;?>
></span>
											</div>
										</div>
									</td>
									<td>
										<div>
											<input <?php echo $_smarty_tpl->tpl_vars['disable_value']->value;?>
 type="text" size="10" class="col-lg-9 form-control convertedPrice" data-rule-currency ="true" name="<?php echo $_smarty_tpl->tpl_vars['price']->value['curname'];?>
" id="<?php echo $_smarty_tpl->tpl_vars['price']->value['curname'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['price']->value['curvalue'];?>
" data-decimal-separator='<?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->get('currency_decimal_separator');?>
' data-group-separator='<?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->get('currency_grouping_separator');?>
' />
										</div>
									</td>
									<td>
										<div>
											<input readonly="" type="text" size="10" class="col-lg-9 form-control conversionRate" name="cur_conv_rate<?php echo $_smarty_tpl->tpl_vars['price']->value['curid'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['price']->value['conversionrate'];?>
">
										</div>
									</td>
									<td>
										<div class = "textAlignCenter">
											<button <?php echo $_smarty_tpl->tpl_vars['disable_value']->value;?>
 type="button" class="btn btn-default currencyReset" id="cur_reset<?php echo $_smarty_tpl->tpl_vars['price']->value['curid'];?>
" value="<?php echo vtranslate('LBL_RESET',$_smarty_tpl->tpl_vars['MODULE']->value);?>
"><i class = "fa fa-refresh"></i>&nbsp;&nbsp;<?php echo vtranslate('LBL_RESET',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</button>
										</div>
									</td>
									<td>
										<div class="textAlignCenter">
											<input <?php echo $_smarty_tpl->tpl_vars['disable_value']->value;?>
 style = "vertical-align:middle" type="radio" class="baseCurrency" id="base_currency<?php echo $_smarty_tpl->tpl_vars['price']->value['curid'];?>
" name="base_currency_input" value="<?php echo $_smarty_tpl->tpl_vars['price']->value['curname'];?>
" <?php echo $_smarty_tpl->tpl_vars['base_cur_check']->value;?>
 />
										</div>
									</td>
								</tr>
							<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
						</table>
					</div>
				</div>
				<?php $_smarty_tpl->_subTemplateRender(vtemplate_path('ModalFooter.tpl',$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
			</div>
		</div>
	</div>
</div><?php }
}
