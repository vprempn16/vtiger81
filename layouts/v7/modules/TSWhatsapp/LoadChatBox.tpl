{strip}
<div class="modal-body" style="
    background-color: transparent;
    margin-top: 35px;
    padding: 0;
">
    <style>
        /*.Whatsapp {
            font-family: monospace;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }*/

.Whatsapp {
    padding: 15px;
    background: transparent;
    height: fit-content;
}
.modal-backdrop.in{
    background-color:black !important;
}
.currentRelRecord{
    margin-top: 15px;
    font-size: 16px;
}

.chat-container {
    width: 100%;
    height: 89vh;
    background-color: #ffffff;
    overflow: hidden;
    display: flex;
font-family: inherit;
border: 1px solid #cecece;
}
.sidebar {
    background-color: white;
    color: #ffffff;
    padding: 20px;
    display: flex;
    flex-direction: column;
    border-right: 1px solid #e3e3e3;
    width: 25%;
padding:0;
}
.search-bar{
    margin: 16px 14px;
}
.search-bar input {
    width: 100%;
    padding: 10px;
    border-radius: 5px;
    border: none;
    outline: none;
    background-color: rgb(199 227 249);
    color: grey;
}
        .contact-list {
            cursor: pointer;
            margin-top: 20px;
            overflow-y: scroll;
            flex-grow: 1;
            height: 72vh;
        }
        .contact {
            display: flex;
            align-items: center;
            padding: 10px 0;
            position: relative;
padding: 6px 14px;
        }

        .contact img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .contact-info h4 {
            margin: 0;
            font-size: 13px;
            color: black;
overflow: hidden;
    white-space: nowrap;
    width: 183px;
    text-overflow: ellipsis;
        } 
.name_relrecor_banner{
    width: 85%;
    margin: 0;
    margin-top: 13px;
}
.loadRelatedLists {
    margin-right: 15px;
}
        .contact-info p {
            margin: 5px 0 0;
            font-size: 10px;
            color: #a0b0b9;
        }

        .contact-info .date {
            font-size: 12px;
            color: #6c7a89;
        }

.chat-main {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    background-color: white;
    position: relative;
    width: 75%;
}
.moduleSelect {
    background-color: #b6b6b64f;
    width: 336px;
    height: fit-content;
    padding: 14px;
}
.relateDocToContact{
    font-size: 24px;
    color: #a0b7d2;
    margin-left: 11px;
    cursor: pointer;
    margin-right: 11px;
}
.chat-header {
border-bottom: 1px solid #f0f0f0;
    padding-bottom: 10px;
    background: white;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
        }

        .chat-header img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin: 10px;
        }
.chat-info {
display: flex;
     align-items: center;
background-color: rgb(252, 255, 253);
border-bottom:1px solid #cecece;
}
        .chat-header .chat-info h4 {
            margin: 0;
            font-size: 14px;
font-weight: 900;
padding-bottom: 7px;
        }
.active_contact{
    background: #c7e3f966;
font-weight:900;
}

        .chat-header .chat-info .status {
            font-size: 14px;
            color: #a0b0b9;
        }

        .chat-messages {
flex-grow: 1;
    overflow-y: auto;
    padding: 10px 45px;
    height: 100px;
 display: flex;
    flex-direction: column;
        }
.message {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }

        .message p {
            margin: 0;
            padding: 10px;
            border-radius: 5px;
        }

        .message.received p {
            background-color: white;
            align-self: flex-start;
        }

.message.sent p {
    align-self: flex-end;
     background-color: #ffffff;       /* White background */
  color: #000000;                  /* Black text */
  border: 1px solid #ddd;          /* Light gray border */
  border-radius: 12px 12px 0 12px; /* Rounded bubble (tail on left) */
  padding: 10px 14px;
  margin: 0;
  display: inline-block;
  max-width: 100%;
  font-size: 14px;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
	
}
        .message .time {
            font-size: 12px;
            color: #a0b0b9;
            margin-top: 5px;
        }

.chat-input {
    display: flex;
    align-items: center;
    width: 100%;
    padding: 6px;
    box-sizing: border-box;
    margin: 0;
    border-top: 1px solid #cecece;
}
 .chat-input input {
flex-grow: 1;
    padding: 10px;
    border-radius: 5px;
    border: 0;
    outline: none;
        }

.chat-input button {
    color: #c7e3f9;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    background: transparent;
    font-size: 19px;
    margin-left: 9px;
}
        .message.sent {
            text-align: right;
        }

        .timestatus_stats {
            color: grey;
            border-radius: 11px;
            background: #e7e7e7;
            padding: 6px 13px;
        }

        .timestatus {
            text-align: center;
        }
.chatrecenttime{
    color: #c3c3c3;
    position: absolute;
    right: 0;
    top: 13px;
}
</style>
<div class="Whatsapp">
        <div class="chat-container">
            <aside class="sidebar">
                <div class="search-bar">
                    <input type="text" placeholder="Type here to search...." class="filterContact">
                </div>
                <div class="contact-list">
                    {if !empty($CONTACTS)}
                        {foreach from=$CONTACTS item=LIST}
                            <div class="contact" data-id="{$LIST['contactid']}">
                                <img src="https://www.gravatar.com/avatar/2c7d99fe281ecd3bcd65ab915bac6dd5?s=250">
                                <div class="contact-info">
                                    <h4>{$LIST['lastname']}</h4>
                                    <p>{$LIST['last_message']}</p>
                                </div>
                                    <span class="chatrecenttime">{$LIST['createdtime']}</span>
                            </div>
                        {/foreach}
                    {else}
                        <h3 style="
                            color: grey;
                            text-align: center;
                        ">No records found</h3>
                    {/if}
                </div>
            </aside>
            <main class="chat-main">
                <h3 style="
                    text-align: center;
                    color: grey;
                    margin-top: 30%;
                ">Select a contact</h3>
            </main>
        </div>
    </div>
</div>
{/strip}
