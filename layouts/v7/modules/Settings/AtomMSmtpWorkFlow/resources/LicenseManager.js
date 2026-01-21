/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
jQuery.Class("LicenseManager_JS",{},{ 

	registerSaveServer: function(){
		jQuery(document).find(".atm_saveButton").unbind().click(function(e){
			e.preventDefault();
			var thisInstance = this;
			var aDeferred = jQuery.Deferred();
			var licensekey = $("input[name='msmtpworkflow_license_key']").val();
			var form = document.getElementById("LicenseManagerConfig");
			var formData = jQuery(form).serialize();
			console.log(params);
			params = {};
			var params = {
				'module' : app.getModuleName(),
				'parent' : app.getParentModuleName(),
				'action': 'SaveLicense',
				'formData' : formData,
			};
			//jQuery.extend(params,data);
			app.helper.showProgress();
			app.request.post({data:params}).then(
				function(err,data) {
					app.helper.hideProgress();
					console.log(data.message);
					if(data.success == true ){
						app.helper.showSuccessNotification({"message":data.message});
						location.reload();
					}else{
						app.helper.showErrorNotification({"message":data.message});
					}
				}
			);
		});
	},

	registerActiveDeactiveKey: function (){  
		$('body').on('click','#direct_api',function(e){
			e.preventDefault();
			var thisInstance = this;
			var aDeferred = jQuery.Deferred();
			var action = $(this).val();
			var label = $(this).text();
			var message = "Are You Sure Want to "+label+" Key ?";
			app.helper.showConfirmationBox({'message' : message}).then(function(){
				var params = {
					'module' : app.getModuleName(),
					'parent' : app.getParentModuleName(),
					'action': 'ActiveDeactiveKey',
					'mode' : action,

				};

				app.helper.showProgress();
				app.request.post({'data' : params}).then(
					function(err, data) {
						app.helper.hideProgress();
						if(data.success == true ){
							app.helper.showSuccessNotification({"message":data.message});
							location.reload();
						}else{
							app.helper.showErrorNotification({"message":data.message });
						}
					}
				);
			});
		});
	},

	registerEvents: function(){
		var thisInstance = this;
		thisInstance.registerSaveServer();
		thisInstance.registerActiveDeactiveKey();
	}

});

$(document).ready(function(){
	var instance = new LicenseManager_JS();
	instance.registerEvents();
});



