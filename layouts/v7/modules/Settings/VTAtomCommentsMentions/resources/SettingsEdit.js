jQuery.Class("SettingsEdit",{},{
	registerSaveAjax : function(){
		var thisInstance = this;
		if($('#VtAtomCommentConfig').length){
			$('body').on('change','.vtatom-commentcheck',function(e) {
				e.preventDefault();
				var ischecked = $(this).is(':checked');
				var type = $(this).attr('data-type');
				var linklabel = $(this).attr('data-linklabel');
				var params = {
					'module' : app.getModuleName(),
					'parent' : app.getParentModuleName(),
					'action': 'SaveAjax',
					'ischecked' : ischecked,
					'type' : type,
					'linklabel' : linklabel,
				};
				app.helper.showProgress();
				app.request.post({'data' : params}).then(
					function(err, data) {
						app.helper.hideProgress();
						if(err === null){
							if(type == 'comment_mentions' && !ischecked && !$('.cmt-sendmail-block.hide').length ){
								 $('.cmt-sendmail-block').addClass('hide');
							}
							if(type == 'comment_mentions' && ischecked){
								$('.cmt-sendmail-block').removeClass('hide');
							}
						}else {
							console.log(data);
						}
					}
				);

			});
		}
	},
	registerEvents: function(){
		var thisInstance = this;
		thisInstance.registerSaveAjax();
	}
});
$(document).ready(function(){
	var instance = new SettingsEdit();
	instance.registerEvents();
});

