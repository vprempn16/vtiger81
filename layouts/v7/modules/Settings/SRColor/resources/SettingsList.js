/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
jQuery.Class("SRColor_SettingsList",{},{
	registerDelete : function(){
		jQuery(document).on('click','.atm_row_del',function(e){
			e.preventDefault();
			var id = jQuery(this).attr("data-ser-id");
			var message = "Are You Sure Want to Delete?";
			app.helper.showConfirmationBox({'message' : message}).then(function(){
				var aDeferred = jQuery.Deferred();
				var params = {
					'module' : app.getModuleName(),
					'parent' : app.getParentModuleName(),
					'id': id,
					'action': 'listDeleteAjax'
				};

				app.helper.showProgress();
				app.request.post({'data' : params}).then(
					function(err, data) {
						app.helper.hideProgress();
						if(err === null){
							jQuery(document).find(".row-"+id).remove();
						}else {
							jQuery('.errorMessage', form).removeClass('hide');
							aDeferred.reject();
							console.log(data);
						}
					}
				);
				return aDeferred.promise();
			});
		});
	},

        registerEvents: function(){
                var thisInstance = this;
                thisInstance.registerDelete();

        }
});
$(document).ready(function(){
        var instance = new SRColor_SettingsList();
        instance.registerEvents();
});
