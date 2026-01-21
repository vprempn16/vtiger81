jQuery.Class('ColorHeader_Js', {},{
	ListViewAction : function(){
		var thisInstance  =  this;
		if(_META.module != '' && _META.view == 'List'){
			var module = _META.module;
			params = {
				'module' : 'SRColor',
				'action': 'getPickListValues',
				'current_module': module,
			};
			console.log(params);
			//params.push({name:'module',value:'SRColor'});
			app.helper.showProgress();
			app.request.post({data:params}).then(
				function(err,data) {
					app.helper.hideProgress();
					if(data.success == true ){
						console.log(data);
						Object.entries(data.details).forEach(([field, detail]) => {
						var picklistvalues = detail;
					 	var selectedfield = field;
						var i = 1;
						while ($('#'+module+'_listView_row_' + i).length) {
							var row = $('#'+module+'_listView_row_' + i);
							var tdElement = row.find('td');
							var targetTd = row.find('td[data-name="'+selectedfield+'"]');
							if(targetTd.length){
								var targetValue = targetTd.eq(0).attr('data-rawvalue');
								var fieldtype = targetTd.eq(0).attr('data-field-type');
								if(fieldtype == 'picklist'){
									var color = picklistvalues[targetValue];
									if(color != ''){
										row.css('background',color);
									}
								}
								//console.log(tdElement,targetTd.eq(0).attr('data-rawvalue'),'value',picklistvalues[targetValue],fieldtype);
							}
							i++;
						}
						});
					}else{
					}
				}
			);
		}
	
	},
	registerEvents : function(){
		this.ListViewAction();
	}
});
$(document).ready(function(){
	var ColorHeader = new ColorHeader_Js();
	ColorHeader.registerEvents();
});
