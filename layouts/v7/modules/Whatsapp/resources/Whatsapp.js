/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
class Whatsapp_Js {

    /**
     * Function to register events
     */
    registerEvents() {
        this.addSendWhatsappButton();
    }

    /**
     * Function to append Send Whatsapp button in Module Detail View
     */
    addSendWhatsappButton() {
        var aDeferred = jQuery.Deferred();

        // Ensure we are in detail view
        var view = app.getViewName();
        if (view === 'Detail') {
            var moduleName = app.getModuleName();

            var emailButton = jQuery('.detailViewButtoncontainer button:contains("Send Email")');
            if (emailButton.length === 0) {
                emailButton = jQuery('#detailViewButtoncontainer .btn-group button[data-url*="Emails"]');
            }
            if (emailButton.length === 0) {
                emailButton = jQuery('.detailViewButtoncontainer .btn:contains("Email")');
            }

            var buttonContainer = jQuery('.detailViewButtoncontainer .btn-group').first();

            if (buttonContainer.length > 0) {
                var whatsappBtnHtml = '<button class="btn btn-default" id="sendWhatsappBtn" type="button" onclick="Whatsapp_Js.prototype.showSendWhatsappModal();"><strong>Send Whatsapp</strong></button>';

                if (emailButton.length > 0) {
                    // Append after Send Email button
                    emailButton.after(whatsappBtnHtml);
                } else {
                    // Append to the container
                    buttonContainer.append(whatsappBtnHtml);
                }
            }
        }

        aDeferred.resolve();
        return aDeferred.promise();
    }

    showSendWhatsappModal() {
        var moduleName = app.getModuleName();
        var recordId = app.getRecordId();
        
        var postData = {
            'module': 'Whatsapp',
            'view': 'MassActionAjax',
            'mode': 'showComposeWhatsappModal',
            'source_module': moduleName,
            'record': recordId
        };

        var progressIndicatorElement = jQuery.progressIndicator();
        app.request.post({'data': postData}).then(
            function(err, data) {
                progressIndicatorElement.progressIndicator({'mode': 'hide'});
                if(data) {
                    app.helper.showModal(data, {
                        'cb': function(modalContainer) {
                            var instance = new Whatsapp_Js();
                            instance.registerComposeModalEvents(modalContainer);
                        }
                    });
                }
            }
        );
    }

    /**
     * Function to register events within the Compose WhatsApp Modal
     */
    registerComposeModalEvents(modalContainer) {
        var thisInstance = this;
        
        // Initialize Select2
        vtUtils.showSelect2ElementView(modalContainer.find('select.select2'));

        var channelSelect = modalContainer.find('#whatsappChannel');
        var templateSelect = modalContainer.find('#whatsappTemplate');
        var freeFormContainer = modalContainer.find('#whatsappFreeFormContainer');
        var templatePreviewContainer = modalContainer.find('#whatsappTemplatePreviewContainer');
        var templatePreviewBox = modalContainer.find('#whatsappTemplatePreviewBox');
        var sendBtnLabel = modalContainer.find('#sendBtnLabel');

        // Handle Channel Change
        channelSelect.on('change', function(e) {
            var channelId = jQuery(e.currentTarget).val();
            var sourceModule = modalContainer.find('[name="source_module"]').val();
            
            if(!channelId) {
                templateSelect.html('<option value="none">' + app.vtranslate('None - Type Message') + '</option>').trigger('change');
                return;
            }

            var progressParams = {
                'message': app.vtranslate('Fetching Templates...'),
                'blockInfo': { 'enabled': true }
            };
            var progressInstance = jQuery.progressIndicator(progressParams);

            var actionParams = {
                'module': 'Whatsapp',
                'action': 'MassActionAjax',
                'mode': 'getTemplatesByChannel',
                'channel_id': channelId,
                'source_module': sourceModule
            };

            app.request.post({'data': actionParams}).then(function(err, response) {
                progressInstance.progressIndicator({'mode': 'hide'});
                if(!err && response) {
                    var optionsHtml = '<option value="none">' + app.vtranslate('None - Type Message') + '</option>';
                    jQuery.each(response, function(index, template) {
                        optionsHtml += '<option value="' + template.id + '">' + template.name + ' (' + template.language + ')</option>';
                    });
                    templateSelect.html(optionsHtml).trigger('change');
                }
            });
        });

        // Handle Template Change
        templateSelect.on('change', function(e) {
            var templateId = jQuery(e.currentTarget).val();
            var recordId = modalContainer.find('[name="record"]').val();
            var sourceModule = modalContainer.find('[name="source_module"]').val();

            if(!templateId || templateId === 'none') {
                // Show Free Form
                templatePreviewContainer.hide();
                freeFormContainer.show();
                sendBtnLabel.text(app.vtranslate('Send Message'));
            } else {
                // Show Template Preview
                freeFormContainer.hide();
                templatePreviewContainer.show();
                templatePreviewBox.html('<div class="text-center" style="padding: 20px;"><i class="fa fa-spinner fa-spin"></i> ' + app.vtranslate('Generating Preview...') + '</div>');
                sendBtnLabel.text(app.vtranslate('Send Template'));

                var actionParams = {
                    'module': 'Whatsapp',
                    'action': 'MassActionAjax',
                    'mode': 'getTemplatePreview',
                    'template_id': templateId,
                    'record': recordId,
                    'source_module': sourceModule
                };

                app.request.post({'data': actionParams}).then(function(err, response) {
                    if(!err && response) {
                        // Response should contain the parsed text
                        templatePreviewBox.html(response.preview_html);
                    } else {
                        templatePreviewBox.html('<div class="text-danger">Error loading preview.</div>');
                    }
                });
            }
        });

        // Handle Form Submission
        var form = modalContainer.find('#massWhatsappForm');
        form.on('submit', function(e) {
            e.preventDefault();
            
            // Basic validation
            var toNumber = form.find('#whatsappToNumber').val();
            var channel = form.find('#whatsappChannel').val();
            
            if(!toNumber) {
                app.helper.showErrorNotification({message: app.vtranslate('Please select a To Number')});
                return false;
            }
            if(!channel) {
                app.helper.showErrorNotification({message: app.vtranslate('Please select a Channel')});
                return false;
            }

            var formData = new FormData(form[0]);
            var progressParams = {
                'message': app.vtranslate('Sending WhatsApp Message...'),
                'blockInfo': { 'enabled': true }
            };
            var progressInstance = jQuery.progressIndicator(progressParams);

            app.request.post({'data': formData}).then(function(err, response) {
                progressInstance.progressIndicator({'mode': 'hide'});
                if(!err && response && response.success) {
                    app.helper.hideModal();
                    app.helper.showSuccessNotification({message: response.message || app.vtranslate('Message Sent successfully')});
                } else {
                    app.helper.showErrorNotification({message: (response && response.message) || app.vtranslate('Error sending message')});
                }
            });
        });
    }
}

jQuery(document).ready(function () {
    var instance = new Whatsapp_Js();
    instance.registerEvents();
});
