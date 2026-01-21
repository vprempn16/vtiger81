<?php
/* Smarty version 4.3.2, created on 2026-01-20 16:52:06
  from '/var/www/html/vtiger81/layouts/v7/modules/TSWhatsapp/ChatMain.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_696fb2b63041f1_90964303',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8e4ba7237e25cca89cd774b7f8550468aeaf0fad' => 
    array (
      0 => '/var/www/html/vtiger81/layouts/v7/modules/TSWhatsapp/ChatMain.tpl',
      1 => 1757144276,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_696fb2b63041f1_90964303 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="modal-body" style="background-color: transparent;margin-top: 35px;padding: 0;"><style>/*.Whatsapp {font-family: monospace;background-color: #f4f4f9;margin: 0;padding: 0;display: flex;justify-content: center;align-items: center;height: 100vh;}*/.Whatsapp {padding: 15px;background: transparent;height: fit-content;}.modal-backdrop.in{background-color:black !important;}.currentRelRecord{margin-top: 15px;font-size: 16px;}.chat-container {width: 100%;height: 89vh;background-color: #ffffff;overflow: hidden;display: flex;font-family: inherit;border: 1px solid #cecece;}.sidebar {background-color: white;color: #ffffff;padding: 20px;display: flex;flex-direction: column;border-right: 1px solid #e3e3e3;width: 25%;padding:0;}.search-bar{margin: 16px 14px;}.search-bar input {width: 100%;padding: 10px;border-radius: 5px;border: none;outline: none;background-color: rgb(199 227 249);color: grey;}.contact-list {cursor: pointer;margin-top: 20px;overflow-y: scroll;flex-grow: 1;height: 72vh;}.contact {display: flex;align-items: center;padding: 10px 0;position: relative;padding: 6px 14px;}.contact img {width: 40px;height: 40px;border-radius: 50%;margin-right: 10px;}.contact-info h4 {margin: 0;font-size: 13px;color: black;overflow: hidden;white-space: nowrap;width: 183px;text-overflow: ellipsis;}.name_relrecor_banner{width: 85%;margin: 0;margin-top: 13px;}.loadRelatedLists {margin-right: 15px;}.contact-info p {margin: 5px 0 0;font-size: 10px;color: #a0b0b9;}.contact-info .date {font-size: 12px;color: #6c7a89;}.chat-main {display: flex;flex-direction: column;justify-content: space-between;background-color: white;position: relative;width: 100%;}.moduleSelect {background-color: #b6b6b64f;width: 336px;height: fit-content;padding: 14px;}.relateDocToContact{font-size: 24px;color: #a0b7d2;margin-left: 11px;cursor: pointer;margin-right: 11px;}.chat-header {border-bottom: 1px solid #f0f0f0;padding-bottom: 10px;background: white;display: flex;flex-direction: column;justify-content: space-between;}.chat-header img {width: 50px;height: 50px;border-radius: 50%;margin: 10px;}.chat-info {display: flex;align-items: center;background-color: rgb(252, 255, 253);border-bottom:1px solid #cecece;}.chat-header .chat-info h4 {margin: 0;font-size: 14px;font-weight: 900;padding-bottom: 7px;}active_contact{background: #c7e3f966;font-weight:900;}.chat-header .chat-info .status {font-size: 14px;color: #a0b0b9;}.chat-messages {flex-grow: 1;overflow-y: auto;padding: 10px 45px;height: 100px;display: flex;flex-direction: column;}.message {display: flex;flex-direction: column;margin-bottom: 20px;}.message p {margin: 0;padding: 10px;border-radius: 5px;}.message.received p {background-color: white;align-self: flex-start;}.message.sent p {background-color: #ffffff;       /* White background */color: #000000;                  /* Black text */border: 1px solid #ddd;          /* Light gray border */border-radius: 12px 12px 0 12px; /* Rounded bubble (tail on left) */padding: 10px 14px;margin: 0;display: inline-block;max-width: 100%;font-size: 14px;box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);align-self: flex-end;}.message .time {font-size: 12px;color: #a0b0b9;margin-top: 5px;}.message {display: flex;flex-direction: column;margin-bottom: 15px;padding: 10px 12px;border-radius: 8px;font-size: 14px;position: relative;word-wrap: break-word;}.message.sent {align-self: flex-end;text-align: left;}.message-meta {display: flex;justify-content: flex-end;gap: 5px;font-size: 11px;color: #555;margin-top: 4px;}.tick.sent { color: gray; }.tick.delivered { color: blue; }.tick.read { color: #4fc3f7; }.chat-input {display: flex;align-items: center;width: 100%;padding: 6px;box-sizing: border-box;margin: 0;border-top: 1px solid #cecece;}.chat-input input {flex-grow: 1;padding: 10px;border-radius: 5px;border: 0;outline: none;}.chat-input button {color: #c7e3f9;border: none;border-radius: 5px;cursor: pointer;background: transparent;font-size: 19px;margin-left: 9px;}.message.sent {text-align: right;}.timestatus_stats {color: grey;border-radius: 11px;background: #e7e7e7;padding: 6px 13px;}.timestatus {text-align: center;}.chatrecenttime{color: #c3c3c3;position: absolute;right: 0;top: 13px;}.chat-close-btn {font-size: 24px;font-weight: bold;color: #888;cursor: pointer;transition: color 0.2s ease;}.chat-close-btn:hover {color: #000;}</style><div class="Whatsapp"><div class="chat-container"><main class="chat-main"><input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['LAST_MODULE_RELATION']->value;?>
" id="relationRecordId"><style>.commentText {flex: 1;width: 100%;border: 0;padding: 6px;margin: 0;min-height: 25px;overflow-y: hidden;box-sizing: border-box;background-color: rgb(236, 245, 252);border-radius: 8px;height: 38px;}.message.bundle {text-align: center;font-size: 16px;background-color: transparent;padding: 15px;color: #a14c00;font-weight: 900;border-bottom: 2px solid #cccccc;}/*.chat-date-heading {text-align: center;font-size: 12px;color: #999;margin: 15px 0;position: relative;}.chat-date-heading::before,.chat-date-heading::after {content: "";display: inline-block;width: 30%;height: 1px;background: #ccc;vertical-align: middle;margin: 0 10px;}*/.chat-date-bubble {display: inline-block;margin: 20px auto 10px auto;background-color: #eaeaea;color: #444;padding: 5px 12px;font-size: 12px;border-radius: 6px;text-align: center;}</style><input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['RECORD']->value;?>
" id="recordid"><div class="chat-header"><div class="chat-info"><img src="https://www.gravatar.com/avatar/2c7d99fe281ecd3bcd65ab915bac6dd5?s=250" alt="Sreesh Kumar"><div class="row name_relrecor_banner"><h4><?php echo $_smarty_tpl->tpl_vars['LABEL']->value;?>
</h4></div><button type="button" class="close" aria-label="Close" data-dismiss="modal"><span aria-hidden="true" class="fa fa-close"></span></button></div></div><div class="chat-messages"><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['MESSAGES']->value, 'messages', false, 'day');
$_smarty_tpl->tpl_vars['messages']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['day']->value => $_smarty_tpl->tpl_vars['messages']->value) {
$_smarty_tpl->tpl_vars['messages']->do_else = false;
?><div class="chat-date-bubble"><?php echo $_smarty_tpl->tpl_vars['day']->value;?>
</div><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['messages']->value, 'message');
$_smarty_tpl->tpl_vars['message']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['message']->value) {
$_smarty_tpl->tpl_vars['message']->do_else = false;
if ($_smarty_tpl->tpl_vars['message']->value['comment']) {?><div class="message sent"><p><?php echo $_smarty_tpl->tpl_vars['message']->value['comment'];?>
</p><span class="time"><?php echo $_smarty_tpl->tpl_vars['message']->value['time'];?>
</span><span class="fa fa-check-circle tick sent"></span></div><?php }
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?></div><div class="chat-input"><i class="fa fa-paperclip relateDocToContact" aria-hidden="true"></i><textarea id="commentTextArea" placeholder="Type something..." class="commentText"></textarea><button type="button" class="sendMessage"><i class="fa fa-paper-plane" aria-hidden="true"></i></button></div></main></div></div></div>
<?php }
}
