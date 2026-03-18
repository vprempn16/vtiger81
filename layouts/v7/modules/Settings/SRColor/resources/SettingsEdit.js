jQuery.Class("SRColor",{},{
	getModuleFieldValues : function(){
		var thisInstance = this;
		jQuery('body').on('change','.selectCls',function(){
			var ele = jQuery(this);
			var aDeferred = jQuery.Deferred();
			var category  = ele.attr('data-category');
			var currentVal = ele.find(":selected").val();
			if(category == 'module'){
				category = 'get_fields';
			}
			if(category == 'field'){
				var selectedmodule = $('select[name="selected_module"]').find(":selected").val();
				category = 'get_fields_values'; 	
			}
			var params = {
				'module' : app.getModuleName(),
				'parent' : app.getParentModuleName(),
				'action': 'getModuleFieldValues',
				'category' : category,
				'currentVal' : currentVal,
				'selectedmodule': selectedmodule,
			};
			$('.picklist-table tbody').empty();
			app.helper.showProgress();
			app.request.post({'data' : params}).then(
				function(err, data) {
					app.helper.hideProgress();
					if(err === null){
						if(category == 'get_fields' && data.success == true){
							thisInstance.loadPicklistField(data);
							return true;
						}
						if(category == 'get_fields_values'){
							var row = data.picklistrow
							var picklistvalues = data.picklistvalues;
							$('input[name="picklistvalues"]').val(picklistvalues);
							row = JSON.parse(row);
							$('.picklist-table tbody').empty();
							$('.picklist-table tbody').html(row);
							thisInstance.loadColorPicker();
						}
					}else {
						jQuery('.errorMessage', form).removeClass('hide');
						aDeferred.reject();
						console.log(data);
					}
				}
			);
			return aDeferred.promise();
		});
	},
	loadPicklistField : function(data){
		var picklistfields = data.picklistfields;
		var selectedFields = data.selectedFields;
		picklistfields = JSON.parse(picklistfields);
		var fieldElement = $('select[name="field"]');
		var fieldSelected = fieldElement.val();
		if(typeof picklistfields == 'undefined') {
			picklistfields = {};
			picklistfields['none'] = 'None';
		}
		var options = '<option value>Select option</option>';
		for(var fieldVal in picklistfields) {
			//IE Browser consider the prototype properties also, it should consider has own properties only.
			if(picklistfields.hasOwnProperty(fieldVal)) {
				var fieldValue = fieldVal;
				var fieldLabel = picklistfields[fieldVal];
				options += '<option value="'+fieldValue+'"';
				if(fieldValue == fieldSelected){
					options += ' selected="selected" ';
				}
				if(selectedFields.includes(fieldValue)){
					options +='disabled';
				}
				options += '>'+fieldLabel+'</option>';                                                                                                                                                }                                                                                                                                                                                    }
		fieldElement.empty().html(options).trigger("");
	},
	saveAction : function(){
		var thisInstance = this;
		jQuery('body').on('click','.color_saveButton',function(e){
			e.preventDefault();
			var selected_module = $("select[name='selected_module']").find(':selected').val();
			var module_field = $("select[name='field']").find(':selected').val();
			if(selected_module != '' && module_field != ''){
				var form = document.getElementById("colorSettingsConfig");
				var params = jQuery(form).serialize();
				if(typeof params == 'undefined' ) {
					params = {};
				}
				//params.push({name:'module',value:'SRColor'});
				app.helper.showProgress();
				app.request.post({data:params}).then(
					function(err,data) {
						app.helper.hideProgress();
						if(data.success == true ){
							app.helper.showSuccessNotification({"message":data.message});
							window.location.href = data.url;
						}else{
							app.helper.showErrorNotification({"message":"Failed"});
						}
					}
				);
			}
		});
	},
	checkMetaKeyExists : function(selected_module,module_field,thisInstance){
		var fieldElement = $("select[name='field']");
		var data = '';
		var params = {
			'module' : app.getModuleName(),
			'parent' : app.getParentModuleName(),
			'action': 'checkMetaKeyExists',
			'selected_module' : selected_module,
			'module_field' : module_field,
		};	
		app.helper.showProgress();
		app.request.post({data:params}).then(
			function(err,data) {
				app.helper.hideProgress();
				if(data.success == true ){
					var message = selected_module+"_"+module_field+" Already Exists are you sure want to replace it?";
					app.helper.showConfirmationBox({'message' : message}).then(function(){
						var id = data.id;
						$('input[name="id"]').val(id);
						thisInstance.saveAjax();
					},
						function(error, err){
							fieldElement.select2('val','');
							$('input[name="id"]').val('');
							$("table.picklist-table > tbody >tr").remove();
							thisInstance.saveAjax();
							return false; 
						});

				}else{
					thisInstance.saveAjax();
					//app.helper.showErrorNotification({"message":"Failed"});
				}
			}
		);
	},
	saveAjax : function(){
		var selected_module = $("select[name='selected_module']").find(':selected').val();
                var module_field = $("select[name='field']").find(':selected').val();
	},
	registerCancel : function(){
                jQuery(document).on('click','.color_cancelLink',function(){
			var listviewurl = $('input[name="listviewurl"]').val();
                        window.location.href ="index.php"+listviewurl;
                });
        },
	loadColorPicker : function(){
		var picklistval = $('input[name="picklistvalues"]').val();
		var pickr ='';
		var pickrs = [];
		if(picklistval != ''){
			picklistval = JSON.parse(picklistval);
			var i = 0;
			var colorVal = '';
		 	var element = '';
			for(val in picklistval){
				element = document.getElementsByName(val);
			//	return false;
				colorVal = element[0].value;
				var hiddenInput = $('<input>').attr({
					type: 'hidden',
					name: val, 
					value: colorVal, 
				});
				$(element).after(hiddenInput);
				const pickr = new Pickr({
					el: element[0],
					theme: 'classic', 
					default:colorVal,
					swatches: [
						'rgba(244, 67, 54, 1)',
						'rgba(233, 30, 99, 0.95)',
						'rgba(156, 39, 176, 0.9)',
						'rgba(103, 58, 183, 0.85)',
						'rgba(63, 81, 181, 0.8)',
						'rgba(33, 150, 243, 0.75)',
						'rgba(3, 169, 244, 0.7)',
						'rgba(0, 188, 212, 0.7)',
						'rgba(0, 150, 136, 0.75)',
						'rgba(76, 175, 80, 0.8)',
						'rgba(139, 195, 74, 0.85)',
						'rgba(205, 220, 57, 0.9)',
						'rgba(255, 235, 59, 0.95)',
						'rgba(255, 193, 7, 1)'
					],
					components: {
						preview: true,
						opacity: true,
						hue: true,
						interaction: {
							hex: true,
							rgba: true,
							hsla: true,
							hsva: true,
							cmyk: true,
							input: true,
							clear: true,
							save: true
						}
					}
			}).on('init', pickr => {
				element.value = pickr.getSelectedColor().toRGBA().toString(0);
			}).on('save',  (color, instance) => {
				element.value = color.toRGBA().toString(0);
				currentEle = instance._root.root;
				parentELe = jQuery(currentEle).siblings('.color-picker');	
				inputName = parentELe.attr('data-color-element');
				colorValue =  color.toHEXA().toString();
				$("input[name='"+inputName+"']").val(colorValue);
				//inputElement.style = 'color: '+color.toRGBA().toString(0);
				//hiddenInput.val(colorValue);
				pickr.hide();
			});
				i++;
			pickrs.push(pickr);	
			}
			//button = document.getElementsByClassName("pcr-save");

		}
	},
	registerEvents : function(){
		this.getModuleFieldValues();
		this.saveAction();
		this.loadColorPicker();
		this.registerCancel();
	}
});
$(document).ready(function(){
	var instance = new SRColor();
	instance.registerEvents();
});
