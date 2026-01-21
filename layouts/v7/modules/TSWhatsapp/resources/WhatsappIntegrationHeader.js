jQuery.Class("TSWhatsapp_Js",{},{	
	loadWhatsAppHome: function(){
        	var html = '<li class="dropdown"><div class=""><a class="whatsappWrapper" href="#" role="button" style=""><span class="fa fa-whatsapp" aria-hidden="true" title=""></span></a><div class="dropdown-menu CallNotifierWrapper" role="menu" style="width: 500px;margin-right:-3px; height:400px; max-height: 426px;overflow-y: scroll;background-color: white; box-shadow: -4px 5px 21px 6px  rgba(0,0,0,0.2);" ></div></div></li>';
        	$(document).find('.global-nav .nav').prepend(html);
    	},
	openChatBox :function(){ 
		$(document).on('click', '.whatsappWrapper', function(e) {
			e.preventDefault();
			var ele = $(this);
			var container = $('.CallNotifierWrapper');
			container.html('<div class="text-center p-2">Loading...</div>');
			var chatview = ele.attr('data-mode');
			var params = {
				module: 'TSWhatsapp',
				view: 'LoadChatBox',
				mode: 'ajax',
				chatview : chatview,
			};
			app.helper.showProgress();
			app.request.get({data: params}).then(function(err, data) {
				app.helper.hideProgress();
				app.helper.showModal(data);
			});
		});
	},
	loadWhatsAppContacts:function(){
		console.log(app.getModule);
		if(app.getModuleName() == 'Contacts' && app.getViewName() == 'Detail'){
			var recordid = $("input#recordId").val();
			var html = "<button class='btn btn-default whatsapp_main' id='starToggle' data-id='"+recordid+"' data-mode='main' style='width:100px;''><div >Whats App</div></button>";  
			$('.detailViewContainer').find('.detailViewButtoncontainer .btn-group').prepend(html);
		}
	},
	ChatPanelMain: function(){
                var thisInstance = this;
                $('body').on('click','.whatsapp_main',function(e){
                        e.preventDefault();
                        var ele = $(this);
                        var recordid = $(this).data('id');
                        var params = {
                                'module' : 'TSWhatsapp',
                                'view' : 'ChatMain',
                                'recordid':recordid
                        };
                        app.helper.showProgress();
                        app.request.get({data:params}).then(function(err,data){
				app.helper.showModal(data);
                                var div = $('.chat-messages');
                                div.scrollTop(div.prop("scrollHeight"));
                                $('select.select2').select2();
                                thisInstance.callRelRecordPanel();
                                app.helper.hideProgress();
                        });
                });
        },
	ChatPanel: function(){
		var thisInstance = this;
		$('body').on('click','.contact',function(e){
			e.preventDefault();
			var ele = $(this);
			var recordid = $(this).data('id');
			var params = {
				'module' : 'TSWhatsapp',
				'view' : 'ChatPanel',
				'recordid':recordid
			};
			app.helper.showProgress();
			app.request.get({data:params}).then(function(err,data){
				$('.active_contact').removeClass('active_contact');
				$(ele).addClass('active_contact');

				$(document).find('.chat-main').replaceWith(data);
				var div = $('.chat-messages');
				div.scrollTop(div.prop("scrollHeight"));
				$('select.select2').select2();
				thisInstance.callRelRecordPanel();
				app.helper.hideProgress();
			});
		});
	},
	callRelRecordPanel: function(){
        var thisInstance = this;
        var currentSticky = null;

        $('.chat-messages').scroll(function() {
            console.log('Div is scrolling');
            var $bundles = $(this).find('.bundle');

            $bundles.each(function() {
                var $bundle = $(this);
                var rect = $bundle[0].getBoundingClientRect();

                if (rect.top >= 0 && rect.top <= window.innerHeight) {
                    if (currentSticky && currentSticky[0] !== $bundle[0]) {
                        currentSticky.removeClass('sticky');
                    }
                    if (!currentSticky || currentSticky[0] !== $bundle[0]) {
                        $bundle.addClass('sticky');
                        currentSticky = $bundle;
                    }
                    var text = $('.sticky').text();$(document).find('.currentBundle').remove();
                    $('.chatRelContents').find('.currentRelRecord').remove();
                    $('.chatRelContents').append('<div class="col-lg-12 currentRelRecord">'+text+'</div>');
                    /*$('select.select2').select2();
                    var $select = $('select.loadRelatedLists');

                    var selectedOption = $select.find('option:selected');

                    selectedOption.text(text);*/

                } else {
                    $bundle.removeClass('sticky');
                }
            });
        });

    },
	sendMessage: function() {
        $('body').on('click', '.sendMessage', function(e) {
            e.preventDefault();
            var recordid = $(document).find('#recordid').val();
            var commentText = $(document).find('.commentText').val();
            var relationRecordId = $(document).find('#relationRecordId').val();
            if (relationRecordId == '' || !relationRecordId) {
                var relationRecordId = 0;
            }
            var params = {
                'module': 'TSWhatsapp',
                'view': 'SaveMessages',
                'recordid': recordid,
                'commentText': commentText,
                'relationRecordId': relationRecordId
            };
            app.helper.showProgress();
            app.request.get({data: params}).then(function(err,data) {
                app.helper.hideProgress();
                //console.log(data);
                $(document).find('.chat-messages').append(data);
                $(document).find('.commentText').val("");
                var div = $('.chat-messages');
                div.scrollTop(div.prop("scrollHeight"));
            });
        });
    },
	registerEvents :function(){
		this.loadWhatsAppHome();	
		this.openChatBox();
		this.ChatPanel();
		this.ChatPanelMain();
		this.sendMessage();
	}
});
jQuery(document).ready(function(e){
	var instance = new TSWhatsapp_Js();
        instance.registerEvents();
	instance.loadWhatsAppContacts();
	});
