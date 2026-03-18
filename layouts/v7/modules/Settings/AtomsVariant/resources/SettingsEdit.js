/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
jQuery.Class("AtomsVariant_JS",{},{

	registerSaveServer: function(){
		jQuery(document).find(".atm_saveButton").unbind().click(function(e){
			e.preventDefault();
			var thisInstance = this;
			var aDeferred = jQuery.Deferred();
			var options  = $("select[name='variant_fields[]']").find(':selected').val();
			if(options != '' ){
				var form = document.getElementById("AtomsVariantConfig");
				var formData = jQuery(form).serialize();
				params = {};
				var params = {
					'module' : app.getModuleName(),
					'parent' : app.getParentModuleName(),
					'action': 'SaveFormAjax',
					'formData' : formData,
                    'options': options,
				};
				//jQuery.extend(params,data);
				app.helper.showProgress();
				app.request.post({data:params}).then(
					function(err,data) {
						app.helper.hideProgress();
						if(data.success == true ){
							app.helper.showSuccessNotification({"message":data.message});
						}else{
							app.helper.showErrorNotification({"message":data.message});
						}
					}
				);
			}
		});
	},
	registerEvents: function(){
		var thisInstance = this;
		thisInstance.registerSaveServer();
	}
});

$(document).ready(function(){
		var instance = new AtomsVariant_JS();
		instance.registerEvents();
});
