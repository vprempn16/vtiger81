/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
jQuery.Class("ServiceCompetency_JS",{},{
    registerSaveServer: function(){
        jQuery(document).find(".atm_saveButton").unbind().click(function(e){
            e.preventDefault();
            var thisInstance = this;
            var aDeferred = jQuery.Deferred();            
            var isValid = true;
            jQuery(".role-error").remove();
            jQuery('input.roleprice').each(function() {
                var input = jQuery(this);
                var value = input.val().trim();

                if (value === "") {
                    isValid = false;

                    if (input.next('.role-error').length === 0) {
                        input.after('<div class="role-error" style="color:red;font-size:12px;margin-top:3px;">Required field</div>');
                    }
                }
            });
            if (!isValid) {
                return false; // Stop execution if invalid
            }
            var form = document.getElementById("RolePricing");
            var formData = jQuery(form).serialize();
            params = {};
            var params = {
                'module' : app.getModuleName(),
                'parent' : app.getParentModuleName(),
                'action': 'SaveFormAjax',
                'formData' : formData,
            };
            //jQuery.extend(params,data);
            app.helper.showProgress();
            app.request.post({data:params}).then(function(err,data) {
                   app.helper.hideProgress();
                if(data.success == true ){
                      app.helper.showSuccessNotification({"message":data.message});
                }else{
                    app.helper.showErrorNotification({"message":data.message});
                }
            });
        });
    },
    registerWorkingDaysSave : function(){
    console.log('registerWorkingDaysSave')
        jQuery(document).find(".wd_saveButton").unbind().click(function(e){
            e.preventDefault();
            var thisInstance = this;
            var aDeferred = jQuery.Deferred();
            var isValid = true;
            jQuery(".wd-error").remove();
            jQuery('input.working_days').each(function() {
                var input = jQuery(this);
                var value = input.val().trim();
                if (value === "") {
                    isValid = false;
                    if (input.next('.wd-error').length === 0) {
                        input.after('<div class="wd-error" style="color:red;font-size:12px;margin-top:3px;">Required field</div>');
                    }
                }
            });
            if (!isValid) {
                return false; // Stop execution if invalid
            }
               var workingDaysData = {};
    jQuery('input.working_days').each(function() {
        var name = jQuery(this).attr('name');
        var value = jQuery(this).val();
        workingDaysData[name] = value;
    });
            var form = document.getElementById("Workingdays");
            var formData = jQuery(form).serialize();
            params = {};
            var params = {
                'module' : app.getModuleName(),
                'parent' : app.getParentModuleName(),
                'action': 'SaveWorkingDays',
                'formData' : formData,
                'working_days':workingDaysData,
            };
            //jQuery.extend(params,data);
            app.helper.showProgress();
            app.request.post({data:params}).then(function(err,data) {
                   app.helper.hideProgress();
                if(data.success == true ){
                      app.helper.showSuccessNotification({"message":data.message});
                }else{
                    app.helper.showErrorNotification({"message":data.message});
                }
            });
        });

    },
    registerWorkingdaysTab: function(){ 
        var thisInstance = this;
        var contents = jQuery('#layoutEditorContainer').find('.contents');
        var relatedContainer = contents.find('#userWorkingdays');
        var relatedTab = contents.find('.workingDaysTab');

        relatedTab.click(function (e) {
        var params = {};
        params['module'] = app.getModuleName();
        params['parent'] = app.getParentModuleName();
        params['view'] = 'GetUserWorkingDays';
        app.helper.showProgress();
        app.request.post({data:params}).then(function(err,data) {
                app.helper.hideProgress();
                if (err === null) {
                    relatedContainer.html(data);
                    thisInstance.registerWorkingDaysSave();
                }
                });
        });  
    },
    
    registerEvents: function(){
        var thisInstance = this;
        thisInstance.registerSaveServer();
        thisInstance.registerWorkingdaysTab();
        thisInstance.registerWorkingDaysSave();
    }
});

$(document).ready(function(){
        var instance = new ServiceCompetency_JS();
        console.log( "registerOnchangeEvent");
        instance.registerEvents();
});
