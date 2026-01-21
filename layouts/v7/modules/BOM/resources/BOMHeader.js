jQuery.Class("BOMHeader_Js",{
	 generatePOPopup : function(url) {
		                 var thisInstance = this;
		var params = new URLSearchParams(url.split('?')[1]);	
	 	var params = {
                        module: "BOM",
                        view: params.get('view'),
                        record: params.get('recordid'),
                };
		 app.helper.showProgress();
		 app.request.get({ data: params }).then(
			 function(err, data) {
				 app.helper.hideProgress();
				 if (err === null && data != '') {
					app.helper.showModal(data);
					 thisInstance.generatePo();
					console.log(thisInstance,'this');

				 }
			 });
	 },
	generatePo : function(){
		jQuery('#generatePOBtn').on('click', function() {
			var bomId = jQuery('#bomRecordId').val();
			var vendorBlocks = [];
			jQuery('.vendor-row').each(function() {
				var row = jQuery(this);
				var vendorId = row.nextAll('input.vendorId:first').val() || '';
				var products = [];

				row.nextUntil('.vendor-row').each(function() {
					var productRow = jQuery(this);
					if (productRow.find('.productId').length) {
						products.push({
							productid: productRow.find('.productId').val(),
							qty: productRow.find('.productQty').val(),
							sequence: productRow.find('.sequenceNo').val()
						});
					}
				});

				vendorBlocks.push({
					vendorid: vendorId,
					products: products
				});
			});
			var message = 'Are you sure want to proceed?';
                        app.helper.showConfirmationBox({'message': message}).then(function (e) {
			//console.log(vendorBlocks,bomId);
			var postData = {
				module: 'BOM',
				action: 'GeneratePO', 
				bomid: bomId,
				vendors: vendorBlocks
			};

			app.helper.showProgress();
			app.request.post({ data: postData }).then(function(err, response) {
				app.helper.hideProgress();
				if (err === null) {
					app.helper.showSuccessNotification({ message: 'Purchase Orders created successfully!' });
					app.hideModalWindow();

					//window.location.reload(); // refresh BOM detail to see related POs
				} else {
					app.helper.showErrorNotification({ message: 'Error while creating POs' });
				}
			});
			});
		});
	}

},{
	loadColunmInEdit : function(){
		var thisInstance = this;
		var currentModule = app.getModuleName();
		var modules = ["BOM"];
		if(modules.includes(currentModule) === false || app.getViewName() != 'Edit') {
			return false;
		}
		var recordId = jQuery("input[name='record']").val();
		if(recordId === ""){
			var url = window.location.href;
			var params = new URLSearchParams(url.split('?')[1]);
			recordId = ['invoice_id', 'quote_id', 'salesorder_id', 'purchaseorder_id','record'].map(p => new URLSearchParams(window.location.search).get(p)).find(Boolean);
		}

		if(!recordId) {
			return;
		}
		var params = {
			module: "BOM",
			action: "GetLineItemDetails",
			record: recordId,
		};
		app.helper.showProgress();
		app.request.post({ data: params }).then(
			function(err, data) {
				app.helper.hideProgress();
				if (err === null && data.success === true) {
					var itemData = JSON.parse(data.itemDetails);
					var qty_multiple =  parseFloat(itemData.qty_multiple || 0);
					jQuery(".qty-multiply-wrapper").find("#qty_multiple").val(qty_multiple);
				}
			});
	},
	appendQtyMultipleField : function(){
                var currentModule = app.getModuleName();
                if(currentModule != 'BOM' ){
                        return false;
                }
                if(app.getViewName() != 'Edit' ){
                        return false;
                }
                var wrappercontainer = jQuery('.well .row');
		if(wrappercontainer.find('.global-pricehike-wrapper').length > 0){
			var $firstCol = wrappercontainer.find('.col-sm-3').first();
			$firstCol.remove();
			$firstCol = wrappercontainer.find('.col-sm-3').first();
			var qtyMultipleHtml = `
				 <div class="col-sm-3 qty-multiply-wrapper">
				 <div class="pull-right">
				 <i class="fa fa-info-circle"></i>&nbsp;
				 <label>Qty Multiplayer (+)</label>
				 <input type="text" class="smallInputBox inputElement" id="qty_multiple" name="qty_multiple" style="width: 100px;"  />
				 </div>
				 </div>
				 `;
		}else{
			var $firstCol = wrappercontainer.find('.col-sm-4').first();
			$firstCol.remove();
			$firstCol = wrappercontainer.find('.col-sm-4').first();
			var qtyMultipleHtml = `
				 <div class="col-sm-4 qty-multiply-wrapper">
				 <div class="pull-right">
				 <i class="fa fa-info-circle"></i>&nbsp;
				 <label>Qty Multiplayer (+)</label>
				 <input type="text" class="smallInputBox inputElement" id="qty_multiple" name="qty_multiple" style="width: 100px;"  />
				 </div>
				 </div>
				 `;
		}
                if (jQuery('.qty-multiply-wrapper').length > 0) return;

                $firstCol.before(qtyMultipleHtml);
                jQuery(document).on('input', '#qty_multiple', function () {
                        var qtyValue = parseFloat(jQuery(this).val());
                        if (isNaN(qtyValue) || qtyValue < 0) qtyValue = 0;
                        jQuery("table#lineItemTab tbody tr").each(function () {
                                var row = jQuery(this);
                                var rowNum = row.attr("data-row-num");
                                if (!rowNum || rowNum == "0") return;

                                var $qtyField = row.find("input.qty");
                                if ($qtyField.length) {
                                        $qtyField.val(qtyValue);
                                        $qtyField.trigger('focusout');
                                }
                        });
                });
        },
	registerEvents : function(){
        var thisInstance = this;
    }
});
jQuery(document).ready(function(e){       
	var instance = new BOMHeader_Js();
	instance.registerEvents();
	instance.appendQtyMultipleField();
	instance.loadColunmInEdit();
});
