jQuery.Class("VtAtomCommentMentions",{},{
	userObj : false,
	isAlreadyExists : false,
	isOnchangeExists : false,
	registerOnChangeShowUsers : function(){
		var thisInstance =  this;
		if(!thisInstance.isOnchangeExists){
			thisInstance.isOnchangeExists = true;
			$('body').on('keyup','.commentcontent',function(){
				var textareaValue = $(this).val();
				var lastIndex = textareaValue.lastIndexOf('@');
				if (lastIndex !== -1) {
					var searchText = textareaValue.substring(lastIndex + 1);
					var userList = JSON.parse($('#userList').val());

					var filteredUsers = userList.filter(function(user) {
						return user.label.toLowerCase().includes(searchText.toLowerCase());
					});
					var filteredUsername = userList.filter(function(user) {
						return user.username.toLowerCase().includes(searchText.toLowerCase());
					});
					thisInstance.displayUserDropdown(filteredUsers,$(this));
				}else{
					$('#userDropdown').hide();
				}
			});
		}
	},
	displayUserDropdown : function(users,textarea) {
		var thisInstance = this;
		var dropdownHTML = '';
		var dropdown = $('#userDropdown');
		$.each(users, function(index, user) {
			dropdownHTML += '<div class="userOption" data-username="'+user.username+'">' + user.label + '</div>';
		});
		//$('#userDropdown').html(dropdownHTML).show();
		var textareaOffset = textarea.offset();
		var textareaHeight = textarea.outerHeight();
		var dropdownHeight = dropdown.outerHeight();
		var dropdownTop = '';
		dropdownTop = textareaOffset.top - dropdownHeight;
		dropdown.html(dropdownHTML).css({ top: dropdownTop , left: textareaOffset.left }).show();
		$('.userOption').click(function() {
			var username = $(this).attr('data-username');
			thisInstance.insertUser(username,textarea);
		});
	},
	insertUser : function(username,textarea) {
		var thisInstance = this;
		var textareaValue = textarea.val();
		var lastIndex = textareaValue.lastIndexOf('@');
    		var newText = textareaValue.substring(0, lastIndex + 1) + username;
		$(textarea).val(newText);
		$('#userDropdown').hide();
	},
	registerGetAllUser : function(){
		var thisInstance =  this;
		var style = document.createElement('style');
		style.type = 'text/css';
		if(!thisInstance.isAlreadyExists){
			thisInstance.isAlreadyExists =  true;
			var params = {};
			params = {
				'module' : 'VTAtomCommentsMentions',
				'view': 'GetAllUserForComment',
			};
			app.helper.showProgress();
			app.request.post({'data' : params}).then(
				function(err, data) {
					app.helper.hideProgress();
					if(err === null){
						if($('#recordId').length && !$('input[name="comment_mentions_users"]').length && data){
							$('#recordId').after("<input type='hidden' id='userList' value='"+data+"' name='comment_mentions_users'>");
							var css = '.userOption { cursor: pointer; padding: 5px; }' +
							          '.userOption:hover { background-color: lightgrey; }';
							style.appendChild(document.createTextNode(css));
							$('#recordId').after(style);
							$('#recordId').after('<div id="userDropdown" style="position: absolute; top: 0; left: 0; z-index: 100; background-color: #fff; border: 1px solid #ccc; border-top: 1px solid rgb(204, 204, 204);; display: none;"></div>');
						}
					}
				}
			);
		}
	},
	registerEvents : function(){
		this.registerGetAllUser();
		this.registerOnChangeShowUsers();
	}	
});
$(document).ready(function(){
	var instance = new VtAtomCommentMentions();                                                                                                                                                   var url = window.location.href;
        var params = new URLSearchParams(url.split('?')[1]);                                                                                                                                          var view = params.get('view');
        if(view == 'Detail'){                                                                                                                                                                                 instance.registerEvents();
        }             
	$(document).ajaxComplete(function(){
		var url = window.location.href;
                var params = new URLSearchParams(url.split('?')[1]);
                var relatedModule = params.get('relatedModule');
                if(relatedModule =='ModComments'){
			instance.registerEvents();
		}
	});
});
