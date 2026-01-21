{strip}
<main class="chat-main">
    <input type="hidden" value="{$LAST_MODULE_RELATION}" id="relationRecordId">
    <style>
.commentText {
    flex: 1;
    width: 100%;
    border: 0;
    padding: 6px;
    margin: 0;
    min-height: 25px;
    overflow-y: hidden;
    box-sizing: border-box;
    background-color: rgb(236, 245, 252);
    border-radius: 8px;

height: 38px;
}
.message.bundle {
    text-align: center;
    font-size: 16px;
    background-color: transparent;
    padding: 15px;
    color: #a14c00;
    font-weight: 900;
    border-bottom: 2px solid #cccccc;
}
/*.chat-date-heading {
    text-align: center;
    font-size: 12px;
    color: #999;
    margin: 15px 0;
    position: relative;
}

.chat-date-heading::before,
.chat-date-heading::after {
    content: "";
    display: inline-block;
    width: 30%;
    height: 1px;
    background: #ccc;
    vertical-align: middle;
    margin: 0 10px;
}*/
.chat-date-bubble {
    display: inline-block;
    margin: 20px auto 10px auto;
    background-color: #eaeaea;
    color: #444;
    padding: 5px 12px;
    font-size: 12px;
    border-radius: 6px;
    text-align: center;
}
    </style>
    <input type="hidden" value="{$RECORD}" id="recordid">
     <div class="chat-header">
        <div class="chat-info">
        <img src="https://www.gravatar.com/avatar/2c7d99fe281ecd3bcd65ab915bac6dd5?s=250" alt="Sreesh Kumar">
    <div class="row name_relrecor_banner">
            <h4>{$LABEL}</h4>
    </div>
	<button type="button" class="close" aria-label="Close" data-dismiss="modal"><span aria-hidden="true" class="fa fa-close"></span></button>
        </div>
    </div>
    <div class="chat-messages">
	 {foreach from=$MESSAGES key=day item=messages}
        <div class="chat-date-bubble">{$day}</div>

        {foreach from=$messages item=message}
            {if $message.comment}
                <div class="message sent">
                    <p>{$message.comment}</p>
                    <span class="time">{$message.time}</span>
                    <span class="fa fa-check-circle tick sent"></span>
                </div>
            {/if}
        {/foreach}
    {/foreach}
    </div>
    <div class="chat-input">
        <i class="fa fa-paperclip relateDocToContact" aria-hidden="true"></i>
    <textarea id="commentTextArea" placeholder="Type something..." class="commentText"></textarea>
        <button type="button" class="sendMessage">
            <i class="fa fa-paper-plane" aria-hidden="true"></i>
        </button>
    </div>
</main>
{/strip}
