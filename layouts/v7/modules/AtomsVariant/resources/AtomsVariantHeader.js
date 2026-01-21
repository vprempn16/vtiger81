jQuery.Class("AtomsVariant_Js",{},{ 
    eventName : '',
    lineItemsHolder : false,
    taxTypeElement : false,
    numOfCurrencyDecimals : false,
    popupPageContentsContainer : false,
    loadCustomProductPopupExt : false,
    registerOnChangeEventExt : false,
    ListViewEntryClickExt : false,
    showVariantInfoDetailViewExt : false,
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
        var clonerow = jQuery('#lineItemTab tr.lineItemCloneCopy');
        var cloneTd = clonerow.find('td').eq(1);
        var variantTd = clonerow.find(".variant-details");
        var headTr = jQuery('#lineItemTab tbody tr').eq(0);
        var headTd = headTr.find('td').eq(1);
        var variantHead = headTr.find(".variant-header");
        clonerow.find("input[name='atomsvariantinfo']").remove();
        clonerow.find("input[name='atomsvariantid']").remove();  
        clonerow.append("<input type='hidden' name='atomsvariantinfo' value=''>");
        clonerow.append("<input type='hidden' name='atomsvariantid' value=''>");
    
        if(variantHead.length === 0){
            headTd.after('<td class="variant-header" ><strong>Variant Details </strong></td>');
        }
        if (variantTd.length === 0) {
            cloneTd.after('<td style="width:300px;" class="variant-details"></td>');
        }
        var thisInstance = this;
        var currentModule = app.getModuleName();
        if (["Invoice", "SalesOrder", "Quotes"].indexOf(currentModule) === -1  && app.getViewName() != 'Edit') {
            return;
        }
        var recordId = jQuery("input[name='record']").val();
        if (!recordId) {
            return;
        }
        var params = {
            module: "AtomsVariant",
            action: "GetLineItemVariantDetails",
            record: recordId
        };
        app.helper.showProgress();
        app.request.post({ data: params }).then(
        function(err, data) {
            app.helper.hideProgress();
                if (err === null && data.success === true) { 
                    var variantData = JSON.parse(data.variantDetails);
                    jQuery("table#lineItemTab tbody tr").each(function() {
                            var lineItemRow = jQuery(this);
                            var rowNum = lineItemRow.attr("data-row-num"); // Get row number (1,2,3...)
                            var atomsvariantinfo = "atomsvariantinfo"+rowNum;
                            var atomsvariantid = "atomsvariantid"+rowNum;
                            if(rowNum != undefined && rowNum != 0){
                                var productInput = jQuery("input[name='hdnProductId" + rowNum + "']"); // Find Product ID field
                                var productId = productInput.val();

                                var variantTd = lineItemRow.find(".variant-details");
                                if (variantTd.length === 0) {
                                    lineItemRow.find("td").eq(1).after('<td class="variant-details"></td>');
                                    variantTd = lineItemRow.find(".variant-details");
                                }                           
                                if (!productId) {
                                    return; 
                                }
                                var matchedVariant = null;
                                if (variantData[rowNum].productid == productId && variantData[rowNum].variantinfo) {
                                        matchedVariant = variantData[rowNum];
                                }
                                if (!matchedVariant || !matchedVariant.variantinfo) {
                                    return; 
                                }
                                var variantInfo = matchedVariant.variantinfo;
                                if(typeof variantInfo === 'string'){
                                    //variantInfo = JSON.parse(variantInfo);
                                    variantInfo = JSON.parse(variantInfo.replace(/&quot;/g, '"'));
                                }
                                var variantId = matchedVariant.variantid;


                                lineItemRow.find("input[name='atomsvariantinfo']").remove(); 
                                lineItemRow.find("input[name='atomsvariantid']").remove();
                                lineItemRow.append("<input type='hidden' name='"+ atomsvariantinfo +"'  value='" + JSON.stringify(variantInfo) + "'>");
                                lineItemRow.append("<input type='hidden' name='"+ atomsvariantid +"'  value='" + variantId + "'>");
                                var variantHtml = "<ul>";
                                for (var key in variantInfo) {
                                    if (variantInfo.hasOwnProperty(key)) {
                                            variantHtml += "<li><b>" + key + ":</b> " + variantInfo[key] + "</li>";
                                    }
                                }
                                variantHtml += "</ul>";
                                variantTd.html(variantHtml);
                            }
                    });
                }
        }); 
    },
    showVariantInfoDetailView : function(){
        var thisInstance = this;
        //if(!thisInstance.showVariantInfoDetailViewExt){
            thisInstance.showVariantInfoDetailViewExt = true; 
            var url = window.location.href;
            var params = new URLSearchParams(url.split('?')[1]);
            var currentModule = app.getModuleName();
            if (["Invoice", "SalesOrder", "Quotes"].indexOf(currentModule) === -1  && app.getViewName() != 'Detail' && params.get('requestMode') != 'full'){
                return;
            }
            var headTr = jQuery('.lineItemsTable tbody tr').eq(0);
            var headTd = headTr.find('td').eq(0);
            var variantHead = headTr.find(".variant-header");
            if(variantHead.length === 0){
                headTd.after('<td class="variant-header" ><strong>Variant Details </strong></td>');
            }
            var recordId = jQuery("input#recordId").val();
            if (!recordId) {
                return;
            }
            var params = {
                module: "AtomsVariant",
                action: "GetLineItemVariantDetails",
                record: recordId
            };
            app.helper.showProgress();
            app.request.post({ data: params }).then(
            function(err, data) {
                app.helper.hideProgress();
                    if (err === null && data.success === true) {   
                    var variantData = JSON.parse(data.variantDetails);
                    var i = 0;
                    jQuery("table.lineItemsTable tbody tr").each(function() {
                            var lineItemRow = jQuery(this);
                            var rowNum = lineItemRow.attr("data-row-num"); 
                            if(i != 0){
                                var variantTd = lineItemRow.find(".variant-details");
                                if (variantTd.length === 0) {
                                    lineItemRow.find("td").eq(0).after('<td class="variant-details"></td>');
                                    variantTd = lineItemRow.find(".variant-details");
                                }
                                var matchedVariant = {};
                                if(!variantData[i]){
                                    return;
                                }
                                if (variantData[i].variantinfo) {
                                        matchedVariant = variantData[i];
                                }
                                var tdfieldValue = lineItemRow.find("td").eq(0).find('a.fieldValue');
                                var productUrl = tdfieldValue.attr('href');
                                var productParams = new URLSearchParams(productUrl.split('?')[1]);
                                var productRecordId = productParams.get('record'); 
                                if (matchedVariant.variantinfo && productRecordId == matchedVariant.productid) {
                                    //console.log(productRecordId,matchedVariant.productid,'tdfieldValue');//lineItemRow.find("td").eq(0).find('a.fieldValue') );
                                    var variantInfo = matchedVariant.variantinfo;
                                    if(typeof variantInfo === 'string'){
                                        //variantInfo = JSON.parse(variantInfo);
                                        variantInfo = JSON.parse(variantInfo.replace(/&quot;/g, '"'));
                                    }
                                    var variantId = matchedVariant.variantid;
                                    var variantHtml = "<ul>";
                                    for (var key in variantInfo) {
                                        if (variantInfo.hasOwnProperty(key)) {
                                                variantHtml += "<li><b>" + key + ":</b> " + variantInfo[key] + "</li>";
                                        }
                                    }
                                    variantHtml += "</ul>";
                                    variantTd.html(variantHtml);
                                }
                            }
                            i++;
                     });
                }
            });
        //}
    }, 
    loadCustomProductPopup: function () {
        var thisInstance = this;
        if(!thisInstance.loadCustomProductPopupExt){
        thisInstance.loadCustomProductPopupExt = true;
        thisInstance.lineItemsHolder.on('click','.lineItemPopupVariant',function(e){
            if ( app.getModuleName() === "SalesOrder" || app.getModuleName() === "Invoice" ){ //|| app.getModuleName() === "Quotes" ){
                var triggerer = jQuery(e.currentTarget);
                thisInstance.showLineItemPopup();
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
                                variantinfo = record.variantinfo;
                                variantid = record.variantid;

                            }
                        }
                    }
                    //console.log(variantinfo,'postPopupHandler');
                    thisInstance.showVariantDetailsInLineItem(triggerer.closest('tr'), variantinfo,variantid, popupReferenceModule );
                    thisInstance.postLineItemSelectionActions(triggerer.closest('tr'), data, popupReferenceModule);
                }
                app.event.off('post.LineItemVariantPopupSelection.click');
                //app.event.off('post.LineItemVariantShow.click');
                app.event.one('post.LineItemVariantPopupSelection.click', postPopupHandler);
                //app.event.one('post.LineItemVariantShow.click', postPopupHandlerForVariant);
                vtUtils.showSelect2ElementView(jQuery("#popupModal").find(".modal-content select.select2"));
                thisInstance.registerOnChangeEvent();
            }
        });
        }
    }, 
    postLineItemSelectionActions : function(itemRow, selectedLineItemsData, lineItemSelectedModuleName ){
         for(var index in selectedLineItemsData) {
            if(index != 0) {
                if(lineItemSelectedModuleName == 'Products') {
                    jQuery('#addProduct').trigger('click', selectedLineItemsData[index]);
                }
            }else{
                itemRow.find('.lineItemType').val(lineItemSelectedModuleName);
                var inventeryInstance =  new Inventory_Edit_Js();
                inventeryInstance.mapResultsToFields(itemRow, selectedLineItemsData[index]);
            }
        }
    },
    showVariantDetailsInLineItem : function(itemRow,variantDetails,variantid,lineItemSelectedModuleName  ){
       if(variantDetails != undefined){
        var rowNum = itemRow.attr('data-row-num');
        var atomsvariantinfo = "atomsvariantinfo"+rowNum;
        var atomsvariantid = "atomsvariantid"+rowNum;
        var headTr = jQuery('#lineItemTab tbody tr').eq(0);

        
        var headTd = headTr.find('td').eq(1);
        var rowtd = jQuery('input.productName', itemRow).closest('td');
        var variantTd = itemRow.find(".variant-details");
        var variantHead = headTr.find(".variant-header");
        var variantid = variantid;
        var variantInfo = variantDetails;
        //console.log(variantid,variantInfo,variantDetails,'showVariantDetailsInLineItem');
        if(variantHead.length === 0){
            headTd.after('<td class="variant-header" ><strong>Variant Details </strong></td>');
        }
        if (variantTd.length === 0) {
            rowtd.after('<td style="width:300px;" class="variant-details"></td>');
        }
        var variantHtml = "<ul>";
        for (var key in variantDetails) {
            if (variantDetails.hasOwnProperty(key) && key !== "productid" && key !== "productname" ) {
                  variantHtml += "<li><b>" + key + ":</b> " + variantDetails[key] + "</li>";
               }
        }
        variantHtml += "</ul>";
        itemRow.find("td.variant-details").html(variantHtml);
        if(itemRow.find("input[name='atomsvariantinfo']").length && itemRow.find("input[name='atomsvariantid']").length){
            itemRow.find("input[name='atomsvariantinfo']").remove();
            itemRow.find("input[name='atomsvariantid']").remove();
        }
        if(  itemRow.find("input[name='atomsvariantinfo"+rowNum+"']").length && itemRow.find("input[name='atomsvariantid"+rowNum+"']").length ){
            itemRow.find("input[name='atomsvariantinfo"+rowNum+"']").remove();
            itemRow.find("input[name='atomsvariantid"+rowNum+"']").remove();
        }
        itemRow.append("<input type='hidden' name='"+atomsvariantid+"' value='" + variantid + "'>");
        itemRow.append("<input type='hidden' name='"+atomsvariantinfo+"' value='" + JSON.stringify(variantInfo) + "'>");
        }
        //console.log(headTd, variantDetails,variantHtml, lineItemSelectedModuleName,'showVariantDetailsInLineItem');    
    },
    showLineItemPopup : function(){    
        var params = {
            'module': "AtomsVariant", 
            'view': 'AtomVariantPopupView', 
        };
        var popupInstance = Vtiger_Popup_Js.getInstance();
        popupInstance.showPopup(params, 'post.LineItemVariantPopupSelection.click');
    
    },
    registerEventForListViewEntryClick : function(){
        var thisInstance = this;
       // if(!thisInstance.ListViewEntryClickExt){
            thisInstance.ListViewEntryClickExt = true;
            var popupPageContentsContainer = this.getPopupPageContainer();

            //popupPageContentsContainer.off('click', '.listViewVariantEntries');
            //popupPageContentsContainer.on('click','.listViewVariantEntries',function(e){
            $('body').on('click','.listViewVariantEntries',function(e){
                thisInstance.getListViewEntries(e);
            });
        //}
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
        var variantInfo = row.data('variantinfo');
        var variantid = row.data('variantid');
        if(typeof dataUrl != 'undefined'){
            dataUrl = dataUrl+'&currency_id='+jQuery('#currencyId').val();

            app.request.post({"url":dataUrl}).then(function(err,data){
                    for(var id in data){
                        if(typeof data[id] == "object"){
                            var recordData = data[id];
                        }
                    }
                    for(var id in data){
                         if(typeof data[id] == "object"){
                            var record = data[id];
                            for(var prodid in record){
                                record = record[prodid];
                                record.variantinfo = variantInfo;
                                record.variantid = variantid;
                            }
                        }
                    }
                //data.variantinfo = variantInfo;
                thisInstance.done(data,thisInstance.getEventName());
            });
                         e.preventDefault();
        } else {
            var id = row.data('id');
            var recordName = row.attr('data-name');
            var recordInfo = row.data('info');
            var referenceModule = jQuery('#popupPageContainer').find('#module').val();
            var response ={};
            response[id] = {'name' : recordName,'info' : recordInfo, 'module' : referenceModule};
            thisInstance.done(response,thisInstance.getEventName());
            e.preventDefault();
        }
    },
    doneVariant : function(variantInfo){
        var event = "post.LineItemVariantShow.click";
        if(typeof event == 'function') {
            event(JSON.stringify(variantInfo));
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
    registerOnChangeEvent : function(){ 
           var thisInstance = this;
           if(!thisInstance.registerOnChangeEventExt){
            thisInstance.registerOnChangeEventExt = true;
                jQuery('body').on('change','select[name="products"]',function(){    
                        var ele = jQuery(this);
                        var id = ele.find(":selected").val();
                        jQuery("#variantTableContainer").html("");
                        jQuery("tr[data-custom='variantFields']").remove();
                        var params = {
                                'module' : "AtomsVariant",
                                'view': "getFields",
                                'id' : id,
                                'mode' : "getSelectedFields",
                        };
                         if (!id || id === "") {
                                ele.closest("tr").nextAll("tr[data-custom='variantFields']").remove();
                                return;
                        }
                        app.helper.showProgress();
                        app.request.post({'data' : params}).then(
                                function(err, data) {
                                        app.helper.hideProgress();
                                        if(err === null){
                                                if(data.success == true){
                                                        var productRow = ele.closest("tr");
                                                        productRow.nextAll("tr[data-custom='variantFields']").remove();

                                                        var newRows = jQuery(data.html);
                                                        newRows.attr("data-custom", "variantFields"); 
                                                        newRows.insertAfter(productRow);
                                                        vtUtils.showSelect2ElementView(jQuery("#popupModal").find(".modal-content select.select2"));
                                                }
                                        }else {
                                                //console.log(data);     
                                        }
                                }
                        );
                }); 
                jQuery('body').on('change','select.variant_field',function(){
                        var ele = jQuery(this);
                        var id = ele.find(":selected").val();
                        var prodid = jQuery("select[name='products']").find(":selected").val();

                        jQuery("#variantTableContainer").html("");
                        if (!id || id === "") {
                                ele.closest("tr").nextAll("tr[data-custom='variantFields']").remove();
                                return;
                        }   
                        var selectedValues = {};
                        jQuery("tr[data-custom='variantFields']").each(function(){
                                var selectField = jQuery(this).find("select.variant_field");
                                var fieldName = selectField.attr('data-fieldname');
                                var fieldValue = selectField.val();

                                if (fieldValue) {
                                    selectedValues[fieldName] = fieldValue; 
                                }
                        });
                        var params = {
                            'module': "AtomsVariant",
                            'view': "getFields",
                            'id': id,
                            'mode': "getVariantFieldAndValue",
                            'productid': prodid,
                            'selectedValues': selectedValues ,
                        };

                        app.helper.showProgress();
                        app.request.post({'data': params}).then(
                                function(err, data) {
                                app.helper.hideProgress();

                                if (data.success === true) {
                                    var productRow = ele.closest("tr");
                                    productRow.nextAll("tr[data-custom='variantFields']").remove();
                                    var newRows = jQuery(data.html);
                                    newRows.attr("data-custom", "variantFields");
                                    //newRows.insertAfter(productRow);

                                     /*if (data.lastField === true) {
                                         thisInstance.getFinalRecords(selectedValues, prodid);
                                        }*/
                                    if (newRows.find("select.variant_field option").length <= 1) {

                                        thisInstance.getFinalRecords(selectedValues, prodid);
                                    } else {
                                        newRows.insertAfter(productRow);
                                    }
                                    vtUtils.showSelect2ElementView(jQuery("#popupModal").find(".modal-content select.select2"));
                                }
                        });
                });
            }

    },
    getFinalRecords: function(selectedValues, productId){
                     var params = {
                         'module': "AtomsVariant",
                         'view': "getFields",
                         'mode': "getFinalRecords",
                         'productid': productId,
                         'selectedValues': selectedValues
                     };

                     app.helper.showProgress();
                     app.request.post({'data': params}).then(
                             function(err, data) {
                             app.helper.hideProgress();
                             if (err === null && data.success === true) {
                             // Append the table to the modal
                                 jQuery("#variantTableContainer").html(data.html);
                            }
                    });
    },
    registerEvents : function(){
        var thisInstance = this;
        thisInstance.initializeVariables();
    }
});
jQuery(document).ready(function(e){
        var instance = new AtomsVariant_Js();
        instance.registerEvents();
        instance.loadCustomProductPopup();
        instance.addColumnInPageLoad();
        instance.showVariantInfoDetailView();
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
                    var requestMode = params.get('requestMode');
                    var view = params.get('view');
                    if(requestMode == 'full' && view == 'Detail'){
                        instance.showVariantInfoDetailView();
                    }
                }
            }
        });
});
