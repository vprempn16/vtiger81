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
    var urlParams = new URLSearchParams(window.location.search);
    var isDuplicate = urlParams.get('isDuplicate');
    if (isDuplicate === 'true') {
        params.isDuplicate = true;
    }
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
                    consultantCell.find('.sc_role_select').select2({ placeholder: 'Select Consultant' });
                    
                    var startdateEle = consultantCell.find('.sc_start_date');
                    var enddateEle = consultantCell.find('.sc_end_date');
                    startdateEle.datepicker({format: 'yyyy-mm-dd',autoclose: true,date: '',calendars: 1,starts: 1,className: 'globalCalendar'});
                    enddateEle.datepicker({format: 'yyyy-mm-dd',autoclose: true,date: '',calendars: 1,starts: 1,className: 'globalCalendar'});
                    var searchSpan = consultantCell.find(".relatedScPopup");
                
                    thisInstance.onclickPopup(searchSpan,lineItemRow);

                    thisInstance.registerDisableActionsForExistingSC();
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
                                options.data.includes(`src_module=${module}`)
                        );
                        const hasSupportedSearchModule = supportedSearchModules.some(searchModule =>
                                options.data.includes(`module=${searchModule}`)
                        );
                        if (hasSupportedModule && options.data.includes('view=PopupAjax')  && options.data.includes('src_module=SalesOrder') && options.data.includes('module=ServiceCompetency') && hasSupportedSearchModule) {
                                const params = new URLSearchParams(options.data);
                                var start_date = $('#popupModal').find('input#sc_start_date').val();
                                var end_date  = $('#popupModal').find('input#sc_end_date').val();
                                var manday = $('#popupModal').find('input#manday').val();
                                var src_record = $('#popupModal').find('input#srcRecord').val(); 
                                var serviceId = $('#popupModal').find('input#service_id').val();
                                var role = $('#popupModal').find('input#role').val();

                                params.set('view', 'Popup'); // You can name this as you like
                                params.set('startDate', start_date); // keep the search term
                                params.set('endDate', end_date); 
                                params.set('manday', manday);
                                params.set('src_record', src_record);
                                params.set('service_id', serviceId);
                                params.set('role', role);
                                params.set('isSearch',true);
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
                var role  = lineItemRow.find('.sc_role_select').find('option:selected').val();
                var manday = lineItemRow.find('.qty').val();
                var startDate = startDateField.val();  // yyyy-mm-dd
                var endDate   = endDateField.val();    // yyyy-mm-dd
                var today = new Date();
                var soDueDate = jQuery('[name="duedate"]').val();
                var dueDateObj = null;
                               vtUtils.hideValidationMessage(lineItemRow.find('[name*="serviceDisplay"]'));
                vtUtils.hideValidationMessage(startDateField);
                vtUtils.hideValidationMessage(endDateField);
                                vtUtils.hideValidationMessage(jQuery('[name="duedate"]'));
                if (soDueDate) {
    			dueDateObj = thisInstance.parseVtigerDate(soDueDate);
                }
                if(soDueDate == '' || !soDueDate){
                     app.helper.showErrorNotification({message: "Please select Due Date"});
                    vtUtils.showValidationMessage(jQuery('[name="duedate"]'), app.vtranslate('JS_REQUIRED_FIELD'));
                    return false;
                }
                if (!serviceId) {
                    app.helper.showErrorNotification({message: "Please select a Service first"});
                    return false;    
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
                if (startDate) {
                    var startDateObj = new Date(startDate);
                     if (startDateObj < today) {
                        //vtUtils.showValidationMessage(startDateField, "Start Date cannot be in the past");
                        // return false;
                     }
                     if (endDate) {
                        var endDateObj = new Date(endDate);
                        if (startDateObj > endDateObj) {
                            vtUtils.showValidationMessage(endDateField, "End Date must be greater than Start Date");
                            return false;
                        }
                    }
                }
                if (endDate && dueDateObj) {
                        var endDateObj2 = new Date(endDate);
                        endDateObj2.setHours(0,0,0,0);
			if (endDateObj2 > dueDateObj) {
                                vtUtils.showValidationMessage( endDateField,"End Date should not exceed Sales Order Due Date (" + soDueDate + ")");
                                return false;
                        }
                }
                if(role == ''){
                    app.helper.showErrorNotification({message: "Please select a Role"});
                    return;
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
                    role : role,
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
	parseVtigerDate : function(dateStr) {
		var format = jQuery('body').data('user-dateformat'); // dd-mm-yyyy
		var parts = dateStr.split(/[.\-/]/);
		var formatParts = format.split(/[.\-/]/);

		var day, month, year;

		for (var i = 0; i < formatParts.length; i++) {
			if (formatParts[i] === 'dd') day = parts[i];
			if (formatParts[i] === 'mm') month = parts[i];
			if (formatParts[i] === 'yyyy') year = parts[i];
		}

		var dateObj = new Date(year, month - 1, day);
		dateObj.setHours(0,0,0,0);

		return dateObj;
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
				     console.log(info,'serviceName');
                              // popup-provided values
                              var popupServiceId = info.servicename || info.hdnProductId || ''; // service/product id
                              var consultantUserId = info.consultantname || '';
                              var consultantRole = info.consultantrole || '';
                              var freeDates = info.total_free_days || []; // array or count
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
					console.log(rowServiceId,popupServiceId,rowConsultantId,consultantUserId);
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
				console.log(duplicateFound ,duplicateTotalQty,freeDates,freeDates,'sa');
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
	var thisInstance = this;
        var errorMessage = app.vtranslate('Please fill required fields: Service Competency in all rows.');
            var soDueDate = jQuery('[name="duedate"]').val();
            var dueDateObj = null;
	     if (soDueDate) {
			dueDateObj = thisInstance.parseVtigerDate(soDueDate);
	     }
            var today = new Date();
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
               vtUtils.hideValidationMessage(row.find('[name*="serviceDisplay"]'));
                vtUtils.hideValidationMessage(startDateField);
                vtUtils.hideValidationMessage(endDateField);
                vtUtils.hideValidationMessage(jQuery('[name="duedate"]'));
                if(soDueDate == '' || !soDueDate){
                    app.helper.showErrorNotification({message: "Please select Due Date"});
                    vtUtils.showValidationMessage(jQuery('[name="duedate"]'), app.vtranslate('JS_REQUIRED_FIELD'));
                    isValid = false;
                }

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
                if (startDateValue) {
                    var startDateObj = new Date(startDateValue);
                     if (startDateObj < today) {
                        // isValid = false;
                        //vtUtils.showValidationMessage(startDateField, "Start Date cannot be in the past");
                     }
                     if (endDateValue) {
                        var endDateObj = new Date(endDateValue);
                        if (startDateObj > endDateObj) {
                            isValid = false;
                            vtUtils.showValidationMessage(endDateField, "End Date must be greater than Start Date");
                        }
                    }
                }
                if (endDateValue && dueDateObj) {
                        var endDateObj2 = new Date(endDateValue);
                        if (endDateObj2 > dueDateObj) {
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
                                var startDateFormatted = matchedData.startdate_display;
                                var endDateFormatted   = matchedData.enddate_display;
                                if (productRecordId == matchedData.productid) {
                                    var consultantName = matchedData.consultantName || '';
                                    var roleLabel = $('<br><br><span class="consultant-role" style=" font-weight:bold; color:#333;"></span>').text('Role: ' + matchedData.consultantrole);
                                     var roleLabel = matchedData.consultantrole || '';
                                    if(consultantName != ''){
                                        var previewHtml = `<span class="consult-value">${consultantName}</span>`;
                                        //consultantnameTd.prepend(previewHtml);
                                       // consultantnameTd.find('.consult-value').after(roleLabel);
                                    }
                                    var htmlContent = `
                                        <div class="consult-detail">
                                         <input type="hidden" class='ticketcount' name="ticketcount${rowNum}" value="${matchedData.ticketscount}">
        <input type="hidden" class='servicecontractsid' name="servicecontractsid${rowNum}" value="${matchedData.servicecontractsid}">
                                            <span><strong>Start Date :</strong> ${startDateFormatted}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <span><strong>End Date :</strong> ${endDateFormatted}</span><br>
                                            <span><strong>Role :</strong> ${roleLabel}</span><br>
                                            <span><strong>Consultant :</strong> ${consultantName}</span>
                                        </div>`;
                                    consultantnameTd.html(htmlContent);
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
            const roleSelect = this.getRoleSelectHtml(details.consultantrole, seqNo);
        var html = `
        <input type="hidden" class='ticketcount' name="ticketcount${seqNo}" value="${details.ticketscount}"> 
        <input type="hidden" class='servicecontractsid' name="servicecontractsid${seqNo}" value="${details.servicecontractsid}">
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
                <td style="padding:4px 6px; font-weight:bold;">Role :</td>
                <td colspan="3" style="padding:4px 6px;">
                    ${roleSelect}
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

        </table>
    `;
        return html;
    },
    getRoleSelectHtml: function(selectedValue, seqNo) {
                       // Ideally load from picklist API or global config
                       const roleOptions = [
                           "Project Manager",
                       "Reviewer",
                       "Implementer",
                       "Learner",
                       "Not Started"
                       ];

                       let html = `<select class="sc_role_select" name="role${seqNo}">`;

                       roleOptions.forEach(role => {
                               const isSelected = (role === selectedValue) ? 'selected' : '';
                               html += `<option value="${role}" ${isSelected}>${role}</option>`;
                               });

                       html += `</select>`;
                       return html;
                   },
	registerDisableActionsForExistingSC: function () {
		var thisInstance = this;
		jQuery('.lineItemRow').each(function () {
			var row = jQuery(this);
			var scId = row.find('td.consultant-details').find('.servicecontractsid').val();
			if (scId && scId !== "0") {
				//row.find('.deleteRow').hide();

				row.find('.dragHandle').addClass('disabled-drag')
					.css('opacity', '0.4')
					.css('cursor', 'not-allowed');

				row.attr('data-no-drag', 'true');
				//row.find('input.qty').attr("readonly", "readonly");
				row.addClass('locked-row');

			}
		});
		thisInstance.initializeSortable();
		jQuery(document).off('click', '#addService').on('click', '#addService', function (e) {
			thisInstance.registerDisableActionsForExistingSC();
		});
	//	jQuery(document).off('click', '.deleteRow').on('click', '.deleteRow', function (e) {
	//		thisInstance.registerDisableActionsForExistingSC();
		//	});
		jQuery(document).off('click.deleteRowConfirm').on('click.deleteRowConfirm', '.deleteRow', function (e) {

			e.preventDefault();
			e.stopPropagation();
			e.stopImmediatePropagation(); // VERY IMPORTANT

			var deleteBtn = jQuery(this);
			var row = deleteBtn.closest('.lineItemRow');
			var scId = row.find('.servicecontractsid').val();

			// Professional correct message
			var message = `
    <b>Confirm Line Item Deletion</b><br><br>
    This line item is linked to existing <b>Service Contracts</b> and associated <b>Tickets</b>.<br><br>

    Before removing this row, please ensure that the related Service Contracts and Tickets are reviewed and removed if necessary.<br><br>

    Failing to do so may lead to inconsistencies in consultant allocation, availability, and reporting.<br><br>

    <b>Do you want to proceed with removing this line item?</b>
    `;

			// Show confirmation
			app.helper.showConfirmationBox({message: message}).then(
				function () {
					// ✅ AFTER confirmation → delete row manually

					// Remove row safely
					row.remove();

					// Trigger recalculation if needed
					jQuery('#lineItemTab').trigger('change');

				},
				function () {
					// ❌ Cancel → do nothing
				}
			);

		});
	},
	initializeSortable: function() {
		if (jQuery('.lineitemTableContainer').length) {
			var tbody = jQuery("#lineItemTab");

			if (typeof tbody.sortable !== 'undefined' && 
				typeof tbody.sortable('instance') !== 'undefined') {
				// Sortable is initialized, destroy it properly
				tbody.sortable('destroy');
			}

			tbody.sortable({
				items: "tr.lineItemRow:not([data-no-drag='true'])", // Use attribute selector
				cancel: "[data-no-drag='true'], .locked-row, input, textarea, button, select, a",
				handle: ".dragHandle", 
				axis: "y",
				containment: "parent",
				cursor: "move",
				tolerance: "pointer",
				placeholder: "sortable-placeholder",
				helper: "clone",
				opacity: 0.8,
				start: function(e, ui) {
					ui.placeholder.height(ui.item.height());
					ui.placeholder.css('background-color', '#f0f0f0');
					ui.placeholder.css('border', '1px dashed #ccc');
				},
				stop: function(e, ui) {
				},
				change: function(e, ui) {
					// Optional: Handle while dragging
				}
			});
		}
	},
	disableDragOnLockedRows_old : function () {
		if (jQuery('.lineitemTableContainer').length) {
			var tbody = jQuery("#lineItemTab tbody");
			tbody.on("mousedown", ".lineItemRow", function (e) {  
				var row = jQuery(this);
				if (row.attr("data-no-drag") === "true") {
					e.stopImmediatePropagation(); // stops sortable from starting
					e.preventDefault();
					return false;
				}
			});

		}
	},


     registerEvents : function(){
     var thisInstance = this;
        this.addColumnInPageLoad();
        this.showConsultantInDetail();
        this.changeSellingPrice();
        this.registerSaveClick();
        this.popupSearchPreFilter();
        this.registerDisableActionsForExistingSC();
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
