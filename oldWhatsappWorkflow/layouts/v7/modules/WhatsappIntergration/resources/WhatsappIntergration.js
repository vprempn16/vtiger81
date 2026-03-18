Vtiger.Class('WhatsappIntergration_Js', {},{
	registerOnchange : function(){
		var self = this;
		$('#task_template').on('change', function () {
			var templateId = $(this).val();
			if (templateId) {
				self.loadTemplate(templateId);
			} else {
				$('#templateMappingContainer').html('');
			}
		});
	},
	loadTemplate: function (templateId,contents = '',recepients ='') {
		var thisInstance = this;
		var params = {};
		params['module'] = 'WhatsappIntergration';
		params['view'] = 'GetTemplate';
		params['template_id'] = templateId;
		params['contents'] = contents;
		params['recepients'] = recepients;
		app.helper.showProgress();
		app.request.get({data:params}).then(function(err,data){
			app.helper.hideProgress();
			if(data != '' ){
				response= data;
				if (response && response.success && response.result && response.result.html) {
					jQuery('#WA_MappingContainer').html(response.result.html);
				} else if (response && response.html) {
					jQuery('#WA_MappingContainer').html(response.html);
				} else {
					jQuery('#WA_MappingContainer').empty();
				}	
				vtUtils.showSelect2ElementView(jQuery('#WA_MappingContainer').find('select'));
			}
		});
	},
	calTemplate : function(){
		var thisInstance = this
		var templateId = $('#task_template').val();
		var contents = $('input[name="contents"]').val();
		var recepients = $('input[name="recepients"]').val();
		if(templateId != ''){
			thisInstance.loadTemplate(templateId,contents,recepients);
		}
	},
	registerSaveServer: function(){
		jQuery(document).find(".atm_saveToken").unbind().click(function(e){
			e.preventDefault();
			var thisInstance = this;
			var aDeferred = jQuery.Deferred();
			var form = document.getElementById("whatsappmanager_config");
			var formData = jQuery(form).serialize();
			var app_id = $('input[name="app_id"]').val();
			var app_secret = $('input[name="app_secret"]').val();
			var phone_number_id =  $('input[name="phone_number_id"]').val();
			var business_id = $('input[name="business_id"]').val();
			var access_token = $('input[name="access_token"]').val();
			var flag = true;
			const param = new URLSearchParams(formData);
			if(param.get('app_id') == ''){
				flag = false;
                                message ='Please Enter App Id';
			}
			if(param.get('app_secret') == ''){
				flag = false;
                                message ='Please Enter App Secret';
			}
			if(param.get('phone_number_id') == ''){
				flag = false;
				message ='Please Enter Phone Number Id';
			} 
			if(param.get('business_id') == ''){
				flag = false;
				message ='Please Enter Business Id';
			}
			if( param.get('access_token') == ''){
				flag = false;
				message ='Please Enter Access Token';
			}
			if(flag){
				params = {};
				var params = {
					'module' : app.getModuleName(),
					'parent' : app.getParentModuleName(),
					'action': 'SaveToken',
					'app_id':app_id,
					'app_secret' : app_secret,
					'access_token': access_token,
					'phone_number_id': phone_number_id,
					'business_id':business_id,
				};
				//jQuery.extend(params,data);
				app.helper.showProgress();
				app.request.post({data:params}).then(
					function(err,data) {
						app.helper.hideProgress();
						if(data.success == true ){
							app.helper.showSuccessNotification({"message":data.message});
							location.reload();
						}else{
							app.helper.showErrorNotification({"message":data.message});
						}
					}
				);
			}else{
				 app.helper.showErrorNotification({"message":message});
			}
		});
	},
	registerEvents	: function(){
		// cal function here
		this.registerOnchange();
		this.registerSaveServer();
	}
});                                         
$(document).ready(function(){     
	var myInstance = new WhatsappIntergration_Js();
	myInstance.registerEvents();
	$(document).ajaxComplete(function( event,xhr,options ){
                var url = options.url;
                var params = new URLSearchParams(url.split('?')[1]);
                if( ( params.get('view') == 'EditTask' || params.get('view') == 'EditV7Task')  && params.get('module') == 'Workflows'){
		console.log(params,params.get('view'));
			myInstance.registerOnchange();
                }
		if(  params.get('view') == 'EditTask' &&  params.get('task_id') != '' ){
			myInstance.calTemplate();
		}
        });
})
