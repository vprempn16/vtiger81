jQuery.Class("ServiceCompetencyHeader_Js",{},{
    
    eventName : '',
    lineItemsHolder : false,
    taxTypeElement : false,
    numOfCurrencyDecimals : false,
    popupPageContentsContainer : false,
    loadCustomProductPopupExt : false,
    registerOnChangeEventExt : false,
    ListViewEntryClickExt : false,
    initializeVariables : function() {
        this.lineItemsHolder = jQuery('#lineItemTab');
        this.taxTypeElement = jQuery('#taxtype');
        this.numOfCurrencyDecimals = parseInt(jQuery('.numberOfCurrencyDecimal').val());
    },

    setEventName : function(eventName) {
        this.eventName = eventName;
    },
    getEventName : function() {
        return this.eventName;
    },
    getPopupPageContainer : function(){
        if(this.popupPageContentsContainer == false) {
            this.popupPageContentsContainer = $('body').find('#popupPageContainer'); // Query('#popupPageContainer');
        }
        return this.popupPageContentsContainer;

    },
    addColumnInPageLoad: function (){
    var thisInstance = this;
    var currentModule = app.getModuleName();
    if (["SalesOrder"].indexOf(currentModule) === -1 || app.getViewName() != 'Edit') {
        return;
    }

    var clonerow = jQuery('#lineItemTab tr.lineItemCloneCopy');
    var cloneQuantityTd = clonerow.find('td').eq(2);
    var headTr = jQuery('#lineItemTab tbody tr').eq(0);
    var headTd = headTr.find('td').eq(2);

    // Add Consultant Header if not exists
    if (headTr.find(".consultant-header").length === 0) {
        headTd.after('<td class="consultant-header"><strong>Consultant</strong></td>');
    }

    // Add consultant column cell in line item clone
    if (clonerow.find(".consultant-details").length === 0) {
        cloneQuantityTd.after('<td style="width:400px;" class="consultant-details"></td>');
    }

    var recordId = jQuery("input[name='record']").val();
    if (!recordId) {
        recordId = ['invoice_id', 'quote_id', 'salesorder_id', 'purchaseorder_id','record']
            .map(p => new URLSearchParams(window.location.search).get(p))
            .find(Boolean);
    }
    if(!recordId) return;

    var params = {
        module: "ServiceCompetency",
        action: "GetLineItemDetails",
        record: recordId,
    };

    app.helper.showProgress();
    app.request.post({ data: params }).then(function(err, data) {
        app.helper.hideProgress();
        if (err === null && data.success === true) {
            var lineItemDetails = JSON.parse(data.lineItemDetails);

            jQuery('#lineItemTab tr.lineItemRow').each(function (index, tr) {
                var seqNo = index + 1;
                var lineItemRow = jQuery(tr);
                var rowNum = lineItemRow.attr("data-row-num");
                if(!rowNum || rowNum == 0) return;

                var quantityTd = lineItemRow.find('td').eq(2);
                if (lineItemRow.find(".consultant-details").length === 0) {
                    quantityTd.after('<td style="width:400px" class="consultant-details"></td>');
                }

                var consultantCell = lineItemRow.find('.consultant-details');
                consultantCell.empty();

                // Check if line item is Service
                if (lineItemDetails[seqNo] && lineItemDetails[seqNo].module == "Services") {

                    // Hidden inputs
                    var html = thisInstance.getConsultantLineItemDetails(lineItemDetails[seqNo], seqNo);
                    consultantCell.append(html);
                    var startdateEle = consultantCell.find('.sc_start_date');
                    var enddateEle = consultantCell.find('.sc_end_date');
                    startdateEle.datepicker({format: 'yyyy-mm-dd',date: '',calendars: 1,starts: 1,className: 'globalCalendar'});
                    enddateEle.datepicker({format: 'yyyy-mm-dd',date: '',calendars: 1,starts: 1,className: 'globalCalendar'});
                    var searchSpan = consultantCell.find(".relatedScPopup");
                    // Finally, append the full field into your target cell
                    //consultantCell.append(referenceWrapper);
                    // var consultantrole = lineItemDetails[seqNo].consultantrole;
                    // role label placeholder
                    // var roleLabel = jQuery('<br><span class="consultant-role" style="margin-left:10px; font-weight:bold; color:#333;"></span>').text('Role: ' + consultantrole);
                    // consultantCell.append(roleLabel);

                    // 🔍 When popup button clicked
                    //app.registerEventForDatePickerFields(consultantCell);
                    thisInstance.onclickPopup(searchSpan,lineItemRow);
                    }
                });
            }
        });
    },
	popupSearchPreFilter :function(){  
		jQuery.ajaxPrefilter(function (options, originalOptions, jqXHR) {
			// Check for multiple modules and both search modules
			const supportedModules = ['Invoice', 'SalesOrder', 'Quotes', 'PurchaseOrder'];
			const supportedSearchModules = ['Products', 'Services','ServiceCompetency' ];

			// Ensure options.data exists and is a string before processing
			if (!options.data || typeof options.data !== 'string') {
				return; // Skip processing if data is not available or not a string
			}

			const hasSupportedModule = supportedModules.some(module =>
				options.data.includes(`module=${module}`)
			);

			const hasSupportedSearchModule = supportedSearchModules.some(searchModule =>
				options.data.includes(`search_module=${searchModule}`)
			);
			if (hasSupportedModule && options.data.includes('src_module=SalesOrder') && options.data.includes('module=ServiceCompetency') && hasSupportedSearchModule) {
				const params = new URLSearchParams(options.data);
				consolr.log(params,'prefilter');
				//const searchValue = params.get('search_value') || '';
					
				//params.set('module', 'MirceamiCustomizations');
				//params.set('action', 'GetProducts'); // You can name this as you like
				//params.set('search_value', searchValue); // keep the search term

				//params.set('custom_product_search', '1');

				options.data = params.toString();
			}
		});
	}, 	
    onclickPopup : function(searchSpan,lineItemRow){
        var thisInstance = this;
        searchSpan.on('click', function(e) {
                var triggerer = jQuery(e.currentTarget);
                var serviceId = lineItemRow.find('input[name^="hdnProductId"]').val() || 0;
                var recordId = jQuery("input[name='record']").val();
                var salesOrderId = recordId;
                var startDateField = lineItemRow.find('.sc_start_date');
                var endDateField   = lineItemRow.find('.sc_end_date');
                var manday = lineItemRow.find('.qty').val();
                var startDate = startDateField.val();  // yyyy-mm-dd
                var endDate   = endDateField.val();    // yyyy-mm-dd

                if (!serviceId) {
                    app.helper.showErrorNotification({message: "Please select a Service first"});
                    return;    
                }
                if (!startDate || startDate.trim() === '') {
                    app.helper.showErrorNotification({ message: "Please fill Start Date before selecting Consultant" });
                    vtUtils.showValidationMessage(startDateField, app.vtranslate('JS_REQUIRED_FIELD'));
                    return false;
                }
                if (!endDate || endDate.trim() === '') {
                        app.helper.showErrorNotification({ message: "Please fill End Date before selecting Consultant" });
                        vtUtils.showValidationMessage(endDateField, app.vtranslate('JS_REQUIRED_FIELD'));
                        return false;
                }

                if (!recordId || recordId === '' || recordId === '0') {
                    if (!startDate || startDate.trim() === '') {
                        app.helper.showErrorNotification({ message: "Please fill the Start Date before selecting Service Competency" });
                        vtUtils.showValidationMessage(jQuery("input[name='start_period']"), app.vtranslate('JS_REQUIRED_FIELD'));
                        return false;
                    }
                 }

                 // Open vtiger popup (standard)
                 var popupParams = {
                    module: 'ServiceCompetency',
                    view: 'Popup',
                    src_module: 'SalesOrder',
                    src_record: salesOrderId,
                    service_id: serviceId,
                    startDate : startDate,
                    manday : manday,
                    endDate: endDate,
                };
                    var popupInstance = Vtiger_Popup_Js.getInstance();
                    popupInstance.showPopup(popupParams,'post.PopupSelection.click');
                    var popupReferenceModule = triggerer.data('moduleName');
                    var variantinfo = {};
                    var postPopupHandler = function(e, data){
                        data = JSON.parse(data);
                        if(!$.isArray(data)){
                            data = [data];
                        }
                        for(var id in data){
                            if(typeof data[id] == "object"){
                                var record = data[id];
                                for(var prodid in record){
                                    record = record[prodid];
                                }      
                            }
                        }
                        thisInstance.postPopupAction(triggerer, data, popupReferenceModule);
                    }
                    app.event.off('post.PopupSelection.click');
                    app.event.one('post.PopupSelection.click', postPopupHandler);
        });
    },
    /*postPopupAction : function(itemRow, selectedItemsData,selectedModuleName){
        for(var index in selectedItemsData) {
            if(index != 0) {
                // need to write multiple select items
            }else{
                var responseData = selectedItemsData[index];
                for(var id in responseData){
                    var recordId = id;
                    var recordData = responseData[id];
                    var selectedName = recordData.name;
                    var servicename = selectedName;//jQuery(selectedName).text();
                    var info = recordData.info;
                    var consultantname = info.consultantname;
                    var consultantrole = info.consultantrole;
                    var selling_price = recordData.selling_price;
                    itemRow.closest('tr').find('.listPrice').val(selling_price).trigger("focusout");
                    itemRow.closest('tr').find('.listPrice').trigger('input');
                    itemRow.closest('.input-group').find('.serviceDisplay').val(servicename);
                    itemRow.closest('.input-group').find('.servicecompetencyid').val(recordId);
                    itemRow.closest('.input-group').find('.consultantname').val(consultantname);
                    itemRow.closest('.input-group').find('.clearReferenceSelection').removeClass('hide');
                    itemRow.closest('.consultant-details').find('.consultant-role').text('Role: ' + consultantrole);
                }
            }
        }
    },*/

        postPopupAction: function(itemRow, selectedItemsData, selectedModuleName) {
                     var self = this;
                     // Loop popup items (supports multi-select)
                     for (var index in selectedItemsData) {
                         var responseData = selectedItemsData[index];
                         for (var id in responseData) {
                             (function () {
                              var recordId = id; // servicecompetency record id
                              var recordData = responseData[id];
                              var serviceName = recordData.name;
                              var info = recordData.info || {};
                              // popup-provided values
                              var popupServiceId = info.servicename || info.hdnProductId || ''; // service/product id
                              var consultantUserId = info.consultantname || '';
                              var consultantRole = info.consultantrole || '';
                              var freeDates = info.freedays || []; // array or count
                              var freeCount = Array.isArray(freeDates) ? freeDates.length : parseInt(freeDates || 0);
                              var selling_price = recordData.selling_price;
                              // Current row qty (attempt common selectors)
                                itemRow.closest('.input-group').find('.consultantname').val(consultantUserId);

                              var qtyField = itemRow.closest('tr.lineItemRow').find('input.quantity, input[name^=\"qty\"]');
                              var currentTicketCount = parseInt(itemRow.closest('tr.lineItemRow').find('input.ticketcount').val() || 0);
                              var currentQty = parseInt(qtyField.val() || 0);
                                var duplicateTotalQty = 0;
                                var duplicateFound = false;
                                var ticketCountVal = '';
                                var combinedQty = 0;
                                jQuery('#lineItemTab').find('tr.lineItemRow').each(function() {
                                        var $r = jQuery(this);
                                        if ($r[0] === itemRow.closest('tr')[0]) return;
                                        var rowServiceId = $r.find('input[name^="hdnProductId"]').val() || $r.find('.hdnProductId').val() || '';
                                        // get that row's consultant user id (hidden)
                                        var rowConsultantId = $r.find('input[name^="consultantname"]').val() || $r.find('.consultantname').val() || '';
                                        // If service ids and consultant ids match, accumulate qty
                                        var ticketCountVal = 0;
                                        var ticketCountInput = $r.find('input[name^="ticketcount"]');
                                        if (ticketCountInput.length) {
                                            ticketCountVal = parseInt(ticketCountInput.val() || 0);
                                        }   
                                        if (rowServiceId === popupServiceId && rowConsultantId === consultantUserId) {
                                            duplicateFound = true;
                                            if (ticketCountVal === 0) {
                                            // only sum qty for rows that are NEW (ticketcount == 0)
                                                var q = parseInt($r.find('input.quantity, input[name^="qty"], input[name^="quantity"]').val() || 0);
                                                duplicateTotalQty += (isNaN(q) ? 0 : q);
                                            }
                                        }
                                });

                                var combinedQty = duplicateTotalQty + currentQty;
                                function applyValues() {
                                    itemRow.closest('tr.lineItemRow').find('.listPrice').val(selling_price).trigger("focusout").trigger('input');
                                    itemRow.closest('.input-group').find('.serviceDisplay').val(serviceName);
                                    itemRow.closest('.input-group').find('.servicecompetencyid').val(recordId); // servicecompetency id
                                    itemRow.closest('.input-group').find('.consultantname').val(consultantUserId);
                                    itemRow.closest('.input-group').find('.clearReferenceSelection').removeClass('hide');
                                    itemRow.closest('.consultant-details').find('.consultant-role').text(consultantRole);
                                }
                                if( currentTicketCount > 0) {
                                      applyValues();
                                    return;
                                }
                                if (duplicateFound && duplicateTotalQty > freeDates) {
                                    var message = 
                                        "You have already selected the same Service and Consultant.\n\n" +
                                        "Total Quantity you select : " + duplicateTotalQty + "\n" +
                                        "Available Free Days: " + freeCount + "\n\n" +
                                        "Proceeding may cause manday shortage and affect ticket creation. Do you want to continue?";

                                    app.helper.showConfirmationBox({ message: message }).then(function () {
                                            applyValues();
                                            }, function () {
                                            // cancel → do nothing
                                                itemRow.closest('.input-group').find('.consultantname').val('');
                                                itemRow.closest('.input-group').find('.serviceDisplay').val('');
                                                itemRow.closest('.input-group').find('.servicecompetencyid').val(''); // servicecompetency id
                                                itemRow.closest('.input-group').find('.clearReferenceSelection').addClass('hide');
                                                itemRow.closest('.consultant-details').find('.consultant-role').text('');

                                            });

                                } else {
                                    // CASE 3: No conflict
                                    applyValues();
                                }
                                })();

                              }
                     }
               },
        registerSaveClickold : function(){
            var thisInstance = this;
            // When page is loaded, change save button type
            var currentModule = app.getModuleName();
            if (["SalesOrder"].indexOf(currentModule) === -1 || app.getViewName() != 'Edit') {
                return;
            }
        var saveBtn = jQuery('button[type="submit"].saveCustomButton');
        var form = jQuery('#EditView');
        if (!saveBtn.length) {
            saveBtn.attr('type', 'button'); // prevent auto submit
            saveBtn.off('click').on('click', function(e) {
                e.preventDefault();
                // run validation
                var valid = thisInstance.validateLineItems();
                if (valid) {
                    // Validation passed → convert to submit and trigger submit manually
                    saveBtn.attr('type', 'submit');
                    //saveBtn.trigger('click');
                    ///form.trigger('submit');
                    //form.vtValidate({
                      //  submitHandler: function (form) {
                     //    return true;
                      //  }
                    //});
                } else {
                    return false;
                }
            });
        }
    },   
    registerSaveClick: function () {
    var thisInstance = this;
    var currentModule = app.getModuleName();
    if (["SalesOrder"].indexOf(currentModule) === -1 || app.getViewName() != 'Edit') {
        return;
    }

    var form = jQuery('#EditView');
    var saveBtn = jQuery('button[type="submit"].saveButton');

    if (saveBtn.length) {
        // Change button type and class on load
        saveBtn.attr('type', 'button')
               .removeClass('saveButton')
               .addClass('customSaveButton');

        // Bind click event to custom button
        jQuery(document).off('click', '.customSaveButton').on('click', '.customSaveButton', function (e) {
            e.preventDefault();

            // Run validation
            var valid = thisInstance.validateLineItems();
            if (valid) {
                // Validation passed → restore original attributes
                var btn = jQuery(this);
                btn.removeClass('customSaveButton')
                   .addClass('saveButton')
                   .attr('type', 'submit');

                // Trigger native click event (which will submit form)
                btn.trigger('click');
            } else {
                app.helper.showErrorNotification({ message: "Please fill required fields before saving." });
                return false;
            }
        });
    }
    },

     validateLineItems: function() {
        var isValid = true;
        var errorMessage = app.vtranslate('Please fill required fields: Service Competency in all rows.');
            var soDueDate = jQuery('[name="duedate"]').val();
            var dueDateObj = null;
            if (soDueDate) {
                var parts = soDueDate.split('/');  // ["28","02","2026"]
                var formatted = parts[2] + '-' + parts[1] + '-' + parts[0]; // yyyy-mm-dd
                dueDateObj = new Date(formatted);
            }
          jQuery('.lineItemRow').each(function() {
            var row = jQuery(this);
            var module = row.find('.itemNameDiv').find('.lineItemType').val()
            if(module == 'Services') {
                var servicecompetency = row.find('[name*="servicecompetencyid"]').val();
                var consultantname = row.find('[name*="consultantname"]').val();
                var endDateValue = row.find('.sc_end_date').val();
                var startDateField   = row.find('.sc_start_date');
                var startDateValue   = startDateField.val(); // yyyy-mm-dd
                var endDateField = row.find('.sc_end_date');    

                row.find('[name*="serviceDisplay"] ').css('border', '');
                endDateField.css('border', '');
                if (!servicecompetency) {
                    isValid = false;
                    if (!servicecompetency)
                        vtUtils.showValidationMessage(row.find('[name*="serviceDisplay"]'), app.vtranslate('JS_REQUIRED_FIELD'));
                }
                    
                if (!startDateValue) {
                    isValid = false;
                    vtUtils.showValidationMessage(startDateField, "Start Date is required");
                }

                if (!endDateValue) {
                    isValid = false;
                    vtUtils.showValidationMessage(endDateField, "End Date is required");
                }
                if (endDateValue && dueDateObj) {
                        var endDateObj = new Date(endDateValue);
                        if (endDateObj > dueDateObj) {
                                isValid = false;
                                vtUtils.showValidationMessage( endDateField,"End Date should not exceed Sales Order Due Date (" + soDueDate + ")");
                        }
                }
            }
        });
        if (!isValid) {
           app.helper.showErrorNotification({ message: errorMessage });
        }
        return isValid;
    },
    registerEventForListViewEntryClick : function(){
            var thisInstance = this;
            thisInstance.ListViewEntryClickExt = true;
            var popupPageContentsContainer = this.getPopupPageContainer();
            $('body').on('click','.listViewScEntries',function(e){
                thisInstance.getListViewEntries(e);
            });
    },
    getListViewEntries: function(e){
        e.preventDefault();
        var preEvent = jQuery.Event('pre.popupSelect.click');
        app.event.trigger(preEvent);
        if(preEvent.isDefaultPrevented()){
            return;
        }
        var thisInstance = this;
        var row  = jQuery(e.currentTarget);
        var dataUrl = row.data('url');
        if(typeof dataUrl != 'undefined'){
            dataUrl = dataUrl+'&currency_id='+jQuery('#currencyId').val();

            app.request.post({"url":dataUrl}).then(function(err,data){
                    for(var id in data){
                        if(typeof data[id] == "object"){
                            var recordData = data[id];
                        }
                    }
                thisInstance.done(data,thisInstance.getEventName());
            });
                         e.preventDefault();
        } else {
            var id = row.data('id');
            var recordName = row.attr('data-name');
            var recordInfo = row.data('info');
            var selling_price = row.attr('data-selling_price');
            var referenceModule = jQuery('#popupPageContainer').find('#module').val();
            var response ={};
            response[id] = {'name' : recordName,'info' : recordInfo, 'module' : referenceModule ,'selling_price' : selling_price};
            thisInstance.done(response,thisInstance.getEventName());
            e.preventDefault();
        }
    },
    done : function(result,eventToTrigger){
        var event = "post.popupSelection.click";
        if(typeof eventToTrigger !== 'undefined'){
            event = eventToTrigger;
        }
        if(typeof event == 'function') {
            event(JSON.stringify(result));
        } else {
            app.event.trigger(event, JSON.stringify(result));
        }
        app.helper.hidePopup();
    },

     /*addColumnInPageLoad: function (){
        var currentModule = app.getModuleName();
        if (["SalesOrder"].indexOf(currentModule) === -1 || app.getViewName() != 'Edit') {
            return;
        }
        var clonerow = jQuery('#lineItemTab tr.lineItemCloneCopy');
        var cloneTd = clonerow.find('td').eq(1);
        var cloneQuantityTd = clonerow.find('td').eq(2);
        var headTr = jQuery('#lineItemTab tbody tr').eq(0);
        var headTd = headTr.find('td').eq(2);
        var consultantTd = clonerow.find(".consultant-details");
        var headQuantityTd = headTr.find('td').eq(2);
        var consultantHeader = headTr.find(".consultant-header");
        if(consultantHeader.length === 0){
            headTd.after('<td class="consultant-header" ><strong>Consultant Name </strong></td>');
        }
        if(consultantTd.length === 0) {
            cloneQuantityTd.after('<td style="width:300px;" class="consultant-details"></td>');
        }
        var thisInstance = this;
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
            module: "ServiceCompetency",
            action: "GetLineItemDetails",
            record: recordId,
        }; 
        app.helper.showProgress();
        app.request.post({ data: params }).then(
        function(err, data) {
            app.helper.hideProgress();
                if (err === null && data.success === true) {
                    var lineItemDetails = JSON.parse(data.lineItemDetails);
                    jQuery('#lineItemTab tr.lineItemRow').each(function (index, tr) {
                        var seqNo = index + 1;
                        var row = jQuery(tr);
                        var lineItemRow = jQuery(this);
                        var rowNum = lineItemRow.attr("data-row-num"); // Get row number (1,2,3...)
                        if(rowNum != undefined && rowNum != 0){
                        var quantityTd = lineItemRow.find('td').eq(2);
                        if (lineItemRow.find(".atompricehike-td").length === 0) {
                            quantityTd.after('<td class="consultant-details"></td>'); 
                        }
                        var consultantCell = row.find('.consultant-details');
                        consultantCell.html(''); // Clear
                        if (lineItemDetails[seqNo] && lineItemDetails[seqNo].consultants_list && lineItemDetails[seqNo].module == "Services" ){
                            var consultantData = lineItemDetails[seqNo].consultants_list;
                            var selectedVal = lineItemDetails[seqNo].consultantname || '';
                            var  servicecompetencyid = lineItemDetails[seqNo].servicecompetencyid || '';
                            var select = jQuery('<select id="consultantname" class="consultant-select form-control" name="consultantname'+ seqNo +'"><option value="">Select an option</option></select>');
                            var inputField = jQuery('<input type="hidden" name="servicecompetencyid'+ seqNo +'" id="servicecompetencyid" value=0>');
                               var roleLabel = $('<br><span class="consultant-role" style="margin-left:10px; font-weight:bold; color:#333;"></span>')
                        .text('Role: ' + lineItemDetails[seqNo].consultantrole);
                            consultantData.forEach(function (opt) {
                                    var option = jQuery('<option>').val(opt.id).text(opt.name).attr('data-servicecompetencyid',opt.servicecompetencyid);
                                    if (opt.id == selectedVal) {
                                        option.attr('selected', 'selected');
                                    }
                                    select.append(option);
                            });
                            consultantCell.append(select);
                            consultantCell.append(inputField);

                            consultantCell.find("select#consultantname").after(roleLabel);

                            select.select2({ width: '100%', placeholder: 'Select Consultant' });    
                        }
                        }
                    });
                }                
        });
     },
     */
     showConsultantInDetail : function(){
        var thisInstance = this;
            var url = window.location.href;
            var params = new URLSearchParams(url.split('?')[1]);
            var currentModule = app.getModuleName();
            if (["SalesOrder"].indexOf(currentModule) === -1  || app.getViewName() != 'Detail' && params.get('requestMode') != 'full'){
                return;
            }
            var headTr = jQuery('.lineItemsTable tbody tr').eq(0);
            var headTd = headTr.find('td').eq(0);
            var quantityHead = headTr.find('td').eq(1);
            if(headTr.find('.pricehike-header').length === 0){
                quantityHead.after('<td class="consultant-header" ><strong>Consultant Name </strong></td>');
            }
            var recordId = jQuery("input#recordId").val();
            if (!recordId) {
                return;
            }
            var params = {
                module: "ServiceCompetency",
                action: "GetLineItemDetails",
                record: recordId,
            };
            app.helper.showProgress();
            app.request.post({ data: params }).then(
                function(err, data) {
                app.helper.hideProgress();
                if (err === null && data.success === true) {
                    var lineItemDetails = JSON.parse(data.lineItemDetails);
                                        var i = 0;
                    jQuery(".lineItemTableDiv table.lineItemsTable tbody tr").each(function() {
                            var lineItemRow = jQuery(this);
                            var rowNum = lineItemRow.attr("data-row-num");
                            if(i != 0){
                                var consultantnameTd = lineItemRow.find(".consultantname-value");

                                if (consultantnameTd.length === 0) {
                                    lineItemRow.find("td").eq(1).after('<td class="consultantname-value"></td>');
                                    consultantnameTd = lineItemRow.find(".consultantname-value");
                                }

                                if(lineItemDetails[i]){
                                        matchedData = lineItemDetails[i];
                                }
                                var tdfieldValue = lineItemRow.find("td").eq(0).find('a.fieldValue');
                                var productUrl = tdfieldValue.attr('href');
                                var productParams = new URLSearchParams(productUrl.split('?')[1]);
                                var productRecordId = productParams.get('record');
                                if (productRecordId == matchedData.productid) {
                                    var consultantName = matchedData.consultantName || '';
                                    var roleLabel = $('<br><br><span class="consultant-role" style=" font-weight:bold; color:#333;"></span>').text('Role: ' + matchedData.consultantrole);
                                    if(consultantName != ''){
                                        var previewHtml = `<span class="consult-value">${consultantName}</span>`;
                                        consultantnameTd.prepend(previewHtml);
                                        consultantnameTd.find('.consult-value').after(roleLabel);
                                    }
                                }
                            }       
                        i++;
                    });       
                }
            });
     },
     changeSellingPrice : function(){
        var thisInstance =  this;
        $(document).on('change', 'select#consultantname', function () {
            var id = $(this).find("option:selected").val();
            var servicecompetencyid = $(this).find("option:selected").attr('data-servicecompetencyid');
            var $row = $(this).closest('tr');
            $row.find('.consultant-role').remove();
            var recordId = $row.find('input.selectedModuleId').val();
            $row.find('input#servicecompetencyid').val(servicecompetencyid);
            if(id != ''){
                var params = {
                    module: "ServiceCompetency",
                    action: "GetRoleAndPrice",
                    record: recordId,
                    user_id : id,
                }; 
                app.request.get({ data: params }).then(function(err, data) {
                    if (err === null && data.success === true) {
                        var details = JSON.parse(data.details);
                        $row.find('input.listPrice').val(details.selling_price).blur();
                         var roleLabel = $('<br><span class="consultant-role" style="margin-left:10px; font-weight:bold; color:#333;"></span>')
                        .text('Role: ' + details.consultantrole);
                        $row.find('select#consultantname').after(roleLabel);
                    }
                });
                
            }
        });
     },
    getConsultantLineItemDetails: function(details, seqNo) {

        var html = `
        <input type="hidden" class='ticketcount' name="ticketcount${seqNo}" value="${details.ticketscount}"> 
        <table class="sc-table" style="width: 100%; margin-top: 5px; border-spacing: 0 10px;">
            <tr>
                <td class="sc-label">Start Date :</td>
                <td class="sc-field">
                    <input type="text"
                        class="inputElement dateField sc_start_date"
                        data-date-format="yyyy-mm-dd"
                        name="start_date${seqNo}"
                        value="${details.startdate || ''}">
                </td>

                <td class="sc-label">End Date :</td>
                <td class="sc-field">
                    <input type="text"
                        name="end_date${seqNo}"
                        class="inputElement dateField sc_end_date"
                        data-date-format="yyyy-mm-dd"
                        value="${details.enddate || ''}">
                </td>
            </tr>
            <tr>
                <td class="sc-label">Consultant :</td>
                <td colspan="3" class="sc-field">
                    <div class="referencefield-wrapper">
                        <input type="hidden" name="popupReferenceModule" value="ServiceCompetency">
                        <div class="input-group">
                            <input type="hidden"
                                name="servicecompetencyid${seqNo}"
                                class="sourceField servicecompetencyid"
                                value="${details.servicecompetencyid || ''}">
                            <input type="hidden"
                                name="consultantname${seqNo}"
                                class="sourceField consultantname"
                                value="${details.consultantname || ''}">

                            <input type="text"
                                id="serviceDisplay_${seqNo}"
                                name="serviceDisplay${seqNo}"
                                class="autoComplete inputElement serviceDisplay form-control"
                                placeholder="Type to search Consultant"
                                value="${details.consultantName || ''}">

                            <a href="#"
                                class="clearReferenceSelection ${details.servicecompetencyid ? '' : 'hide'}">×</a>

                            <span class="input-group-addon relatedScPopup cursorPointer" title="Select Consultant">
                                <i class="fa fa-search"></i>
                            </span>
                        </div>
                    </div>

                </td>
            </tr>

            <tr>
                <td style="padding:4px 6px; font-weight:bold;">Role :</td>
                <td colspan="3" style="padding:4px 6px;">
                    <span class="consultant-role">${details.consultantrole || ''}</span>
                </td>
            </tr>
        </table>
    `;

        return html;
    },
     registerEvents : function(){
        this.addColumnInPageLoad();
        this.showConsultantInDetail();
        this.changeSellingPrice();
        this.registerSaveClick();
	this.popupSearchPreFilter();
     }
});
jQuery(document).ready(function(e){
        var instance = new ServiceCompetencyHeader_Js();
        instance.registerEvents();
        app.event.on("post.Popup.Load",function(event,params){
            var eventToTrigger = params.eventToTrigger;
            if(typeof eventToTrigger != "undefined"){
                instance.setEventName(params.eventToTrigger);
                instance.registerEventForListViewEntryClick();

           }
        });
         $(document).ajaxComplete(function( event,xhr,settings ){
            if (settings.hasOwnProperty("url") ){
                var url =settings.url;
                if(url != ''){
                    var params = new URLSearchParams(url.split('?')[1]);
                    var module =  params.get('module');
                    var requestMode = params.get('requestMode');
                    var view = params.get('view');
                    if(requestMode == 'full' && view == 'Detail'){
                                instance.showConsultantInDetail();
                    }
                }
            }

        });
});


document.addEventListener("DOMContentLoaded", function () {
    var css = `
        .sc-table td {
            padding: 4px 6px;
            vertical-align: middle;
        }

        .sc-label {
            white-space: nowrap;
            font-weight: 600;
            width: 15%;
        }

        .sc-field {
            width: 35%;
        }
    `;

    var style = document.createElement("style");
    style.type = "text/css";
    style.appendChild(document.createTextNode(css));
    document.head.appendChild(style);
});
