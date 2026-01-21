jQuery.Class("Atom_MSmtp_Header",{},{
	ckEditorInstance: false,

	getUrlParams: function( urlString ) {
		var params = {};
		var urlstringsplit = urlString.split('?');
		var queryString = urlstringsplit[1];
		if (typeof queryString == 'undefined') {
			return params['type'] = false;;
		}
		var pairs = queryString.split('&');
		if (typeof pairs == 'undefined') {
			return params['type'] = false;;
		}
		for (var i = 0; i < pairs.length; i++) {
			var pair = pairs[i].split('=');
			params[pair[0]] = decodeURIComponent(pair[1]);
		}
		return params;
	},

	registerGetServerSelect: function(){
		var thisInstance = this;
		var form = jQuery('#saveTask');
		if(form.length){
			var prev_module = form.find("input[name='module']").val();
			//var source_module = form.find( "input[name='source_module']" ).val();
			var module_name = $(document).find( "select#module_name" ).val();
			var taskType = form.find("input[name='taskType']").val();
			var task_id = form.find("input[name='task_id']").val();
			var flag =  form.find("input[name='flag']").val();
			var for_workflow =  form.find("input[name='for_workflow']").val();
			if(prev_module == 'Workflows' && taskType == 'VTMSmtpTask' /*&& flag == "true"*/){
				var params = {
					'module' : 'AtomMSmtpWorkFlow',
					'action': 'getServerSelectHtml',
					'taskType':taskType,
					'task_id':task_id,
					'for_workflow' :for_workflow,
					'module_name': module_name,
				};
				app.request.post({'data' : params}).then(
					function(err, data) {
						app.helper.hideProgress();
						if(data.success == true){
							flag = "false";
							jQuery(data.mailServerHtml).insertBefore('.fromEmailField');
							jQuery(data.emailTemp).insertBefore('.content-row');
							jQuery("#serverMSId").select2({});
							jQuery("#task-emailtemplates").select2({});
							thisInstance.registerEvents();
						}
					}
				);
				form.find('input[name="flag"]').val("false");
			}
		}
	},
	registerVTAtomMSmtpTaskEvents : function (){
		var taskType = jQuery('#saveTask').find("input[name='taskType']").val();
		if(taskType == 'VTMSmtpTask' && !$('.cke_editor_content').length){
			var textAreaElement = jQuery('#content');
			ckEditorInstance = this.getckEditorInstance();
			ckEditorInstance.setElement(textAreaElement);
			ckEditorInstance.loadCkEditor(textAreaElement);

			var editor = CKEDITOR.instances['content'];
			editor.on('change', function(event) {
				var textAreaElement = jQuery('#content');
				textAreaElement.val(CKEDITOR.instances['content'].getData());
			});
		}

	},

	registerFillSMTPFromEmailFieldEvent: function () {
		var taskType = jQuery('#saveTask').find("input[name='taskType']").val();
		if(taskType == 'VTMSmtpTask'){
			var textAreaElement = jQuery('#content');
			//To keep the plain text value to the textarea which need to be
			//sent to server
			textAreaElement.val(CKEDITOR.instances['content'].getData());

			jQuery('#saveTask').on('change', '#fromEmailOption', function (e) {
				var currentElement = jQuery(e.currentTarget);
				var inputElement = currentElement.closest('.row').find('.fields');
				inputElement.val(currentElement.val());
			})
			jQuery('#task-fieldnames,#task_timefields,#task-templates,#task-emailtemplates').change(function (e) {
				var textAreaElement = jQuery('#content');
				var textarea = CKEDITOR.instances.content;
				var value = jQuery(e.currentTarget).val();
				if (textarea != undefined) {
					textarea.insertHtml(value);
				} else if (jQuery('textarea[name="content"]')) {
					var textArea = jQuery('textarea[name="content"]');
					textArea.insertAtCaret(value);
				}
				//textAreaElement.val(CKEDITOR.instances['content'].getData());
			});

			/*
			var instances = new Settings_Workflows_Edit_Js();	
			instances.registerVTPushNotificationTaskEvents();
			instances.preSaveVTEmailTask(taskType);
			*/
		}
	},
	checkHiddenStatusofCcandBcc: function () {
		var ccLink = jQuery('#ccLink');
		var bccLink = jQuery('#bccLink');
		if (ccLink.is(':hidden') && bccLink.is(':hidden')) {
			ccLink.closest('div.row').addClass('hide');
		}
	},
	/*
	 * Function to register the events for bcc and cc links
	 */
	registerCcAndBccEvents: function () {
		var thisInstance = this;
		jQuery('#ccLink').on('click', function (e) {
			var ccContainer = jQuery('#ccContainer');
			ccContainer.removeClass('hide');
			var taskFieldElement = ccContainer.find('select.task-fields');
			vtUtils.showSelect2ElementView(taskFieldElement);
			jQuery(e.currentTarget).hide();
			thisInstance.checkHiddenStatusofCcandBcc();
		});
		jQuery('#bccLink').on('click', function (e) {
			var bccContainer = jQuery('#bccContainer');
			bccContainer.removeClass('hide');
			var taskFieldElement = bccContainer.find('select.task-fields');
			vtUtils.showSelect2ElementView(taskFieldElement);
			jQuery(e.currentTarget).hide();
			thisInstance.checkHiddenStatusofCcandBcc();
		});
	},
	getckEditorInstance: function () {
		if (this.ckEditorInstance == false) {
			this.ckEditorInstance = new Vtiger_CkEditor_Js();
		}
		return this.ckEditorInstance;
	},
	registerEvents : function(){
		app.helper.hideProgress();
		var thisInstance = this;
		thisInstance.registerVTAtomMSmtpTaskEvents();
		thisInstance.registerFillSMTPFromEmailFieldEvent();
		thisInstance.registerCcAndBccEvents();
	}
});
jQuery(document).ready(function(e){
	$( document ).ajaxComplete(function( event, xhr, settings ) {
		var instance = new Atom_MSmtp_Header();
		var param = instance.getUrlParams( settings.url );
		if( param['type'] == "VTMSmtpTask" ) {
			app.helper.showProgress();
			setTimeout( () => {instance.registerGetServerSelect();} , 1000 );
		}
	});
});
