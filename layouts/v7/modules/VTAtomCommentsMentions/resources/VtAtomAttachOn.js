jQuery.Class("VtAtomAttachOn",{},{
	url :window.location.href,
	CurrentRelatedModule : false,
	registerAddCommentsOnElements :function(){
		var thisInstance = this;
		if( $('.basicEditCommentBlock #'+thisInstance.CurrentRelatedModule+'_editView_fieldName_filename').length == 0 && thisInstance.CurrentRelatedModule =='ModComments'){ 
			$('body').on('click','.editComment',function(e){
				var currentAddBlockEle = $(this).parent().parent().parent().parent().parent().parent().parent().parent('.commentInfoHeader').siblings('.addCommentBlock');
				if(currentAddBlockEle.find('.MultiFile-wrap').length == 0 && currentAddBlockEle.find('.MultiFile-list').length == 0){
					$(currentAddBlockEle).find('.pull-right.row').before('<div class="fileUploadContainer text-left"><div class="fileUploadBtn btn btn-sm btn-primary"><span><i class="fa fa-laptop"></i> Attach Files</span><input type="file" id="ModComments_editView_fieldName_filename" class="inputElement  multi" maxlength="6" name="filename[]" value="" multiple=""></div> <div class="MultiFile-list" id="ModComments_editView_fieldName_filename_list"></div> </div>');
				}
				$('input[name="filename[]"]').MultiFile();
			});	
		}
	},
	registerEvents: function(){
		var thisInstance = this;
		var url =  this.url;
		var params = new URLSearchParams(url.split('?')[1]);
                var relatedModule = params.get('relatedModule');
		thisInstance.CurrentRelatedModule = relatedModule;
		if(thisInstance.CurrentRelatedModule =='ModComments'){
			thisInstance.registerAddCommentsOnElements();
		}
	}
});
$(document).ready(function(){
	var instance = new VtAtomAttachOn();
	instance.registerEvents();
	$( document ).ajaxComplete(function() {
		var url =window.location.href;
		var params = new URLSearchParams(url.split('?')[1]);
		var relatedModule = params.get('relatedModule');
		if(relatedModule =='ModComments'){
			instance.registerAddCommentsOnElements();
		}
	});
});

