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
        app.request.post({ 'data': postData }).then(
            function (err, data) {
                progressIndicatorElement.progressIndicator({ 'mode': 'hide' });
                if (data) {
                    app.helper.showModal(data, {
                        'cb': function (modalContainer) {
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
        var sendBtnSubmit = modalContainer.find('#sendWhatsappBtnSubmit');

        // Handle Recipient Change (Validation)
        var recipientSelect = modalContainer.find('#whatsappToNumber');
        recipientSelect.on('change', function (e) {
            var phoneField = jQuery(e.currentTarget).val();
            console.log(phoneField);
            var recordId = modalContainer.find('[name="record"]').val();
            var sourceModule = modalContainer.find('[name="source_module"]').val();

            if (!phoneField) {
                sendBtnSubmit.prop('disabled', true);
                return;
            }

            var actionParams = {
                'module': 'Whatsapp',
                'action': 'MassActionAjax',
                'mode': 'validateRecipient',
                'record': recordId,
                'source_module': sourceModule,
                'phone_field': phoneField
            };

            app.request.post({ 'data': actionParams }).then(function (err, response) {
                if (!err && response) {
                    if (response.has_country_code) {
                        sendBtnSubmit.prop('disabled', false);
                        if (!response.is_existing) {
                            // Optional warning for new recipients
                            app.helper.showSuccessNotification({
                                message: app.vtranslate('Number validated with country code. First time messaging this recipient.')
                            });
                        }
                    } else {
                        app.helper.showErrorNotification({
                            message: app.vtranslate('The selected number does not have a country code. Please update the record or use a different field.')
                        });
                        sendBtnSubmit.prop('disabled', true);
                    }
                }
            });
        });

        // Handle Channel Change
        channelSelect.on('change', function (e) {
            var channelId = jQuery(e.currentTarget).val();
            var sourceModule = modalContainer.find('[name="source_module"]').val();

            if (!channelId) {
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

            app.request.post({ 'data': actionParams }).then(function (err, response) {
                progressInstance.progressIndicator({ 'mode': 'hide' });
                if (!err && response) {
                    var optionsHtml = '<option value="none">' + app.vtranslate('None - Type Message') + '</option>';
                    jQuery.each(response, function (index, template) {
                        optionsHtml += '<option value="' + template.id + '">' + template.name + ' (' + template.language + ')</option>';
                    });
                    templateSelect.html(optionsHtml).trigger('change');
                }
            });
        });

        // Handle Template Change
        templateSelect.on('change', function (e) {
            var templateId = jQuery(e.currentTarget).val();
            var recordId = modalContainer.find('[name="record"]').val();
            var sourceModule = modalContainer.find('[name="source_module"]').val();

            if (!templateId || templateId === 'none') {
                // Show Free Form
                templatePreviewContainer.hide();
                freeFormContainer.show();
                sendBtnLabel.text(app.vtranslate('Send Message'));
                modalContainer.find('#sendWhatsappBtnSubmit').prop('disabled', false);
            } else {
                // Show Template Preview
                freeFormContainer.hide();
                templatePreviewContainer.show();
                templatePreviewBox.html('<div class="text-center" style="padding: 20px;"><i class="fa fa-spinner fa-spin"></i> ' + app.vtranslate('Generating Preview...') + '</div>');
                sendBtnLabel.text(app.vtranslate('Send Template'));
                modalContainer.find('#sendWhatsappBtnSubmit').prop('disabled', true); // Disable while loading

                var actionParams = {
                    'module': 'Whatsapp',
                    'action': 'MassActionAjax',
                    'mode': 'getTemplatePreview',
                    'template_id': templateId,
                    'record': recordId,
                    'source_module': sourceModule
                };

                app.request.post({ 'data': actionParams }).then(function (err, response) {
                    if (!err && response) {
                        // Response should contain the parsed text
                        templatePreviewBox.html(response.preview_html);
                        if (response.isValid === false) {
                            modalContainer.find('#sendWhatsappBtnSubmit').prop('disabled', true);
                        } else {
                            modalContainer.find('#sendWhatsappBtnSubmit').prop('disabled', false);
                        }
                    } else {
                        templatePreviewBox.html('<div class="text-danger">Error loading preview.</div>');
                        modalContainer.find('#sendWhatsappBtnSubmit').prop('disabled', true);
                    }
                });
            }
        });

        // Handle File Selection Validation
        modalContainer.find('#whatsappMedia').on('change', function (e) {
            var file = e.target.files[0];
            if (!file) return;

            var allowedTypes = [
                'audio/aac', 'audio/mp4', 'audio/mpeg', 'audio/amr', 'audio/ogg', 'audio/opus',
                'application/vnd.ms-powerpoint', 'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/pdf', 'text/plain', 'application/vnd.ms-excel',
                'image/jpeg', 'image/png', 'image/webp',
                'video/mp4', 'video/3gpp'
            ];

            if (allowedTypes.indexOf(file.type) === -1) {
                app.helper.showErrorNotification({
                    message: app.vtranslate('Unsupported file type: ') + file.type + '. ' + app.vtranslate('Please use JPG, PNG, PDF, etc.')
                });
                jQuery(e.target).val(''); // Clear the input
            }
        });

        // Handle Form Submission
        var form = modalContainer.find('#massWhatsappForm');
        form.on('submit', function (e) {
            e.preventDefault();

            // Basic validation
            var channelId = form.find('#whatsappChannel').val();
            var recipientField = form.find('#whatsappToNumber').val();
            var templateId = form.find('#whatsappTemplate').val();

            if (!recipientField) {
                app.helper.showErrorNotification({ message: app.vtranslate('Please select a To Number') });
                return false;
            }
            if (!channelId) {
                app.helper.showErrorNotification({ message: app.vtranslate('Please select a Channel') });
                return false;
            }

            var type = (templateId === 'none' || !templateId) ? 'message' : 'template';
            var details = {};

            if (type === 'message') {
                details.text = form.find('#whatsappMessageText').val();
                if (!details.text) {
                    app.helper.showErrorNotification({ message: app.vtranslate('Message text is required') });
                    return false;
                }
            } else {
                details.template_id = templateId;
            }

            var formData = new FormData();
            formData.append('module', 'Whatsapp');
            formData.append('action', 'MassActionAjax');
            formData.append('mode', 'sendWhatsappMessage');
            formData.append('channel_id', channelId);
            formData.append('type', type);
            formData.append('details', JSON.stringify(details));
            formData.append('recipients', JSON.stringify([recipientField]));
            formData.append('source_module', form.find('[name="source_module"]').val());

            var recordId = form.find('[name="record"]').val();
            if (recordId) formData.append('record', recordId);

            var selectedIds = form.find('[name="selected_ids"]').val();
            if (selectedIds) formData.append('selected_ids', selectedIds);

            var fileInput = form.find('#whatsappMedia');
            if (fileInput.length > 0 && fileInput[0].files.length > 0) {
                formData.append('whatsapp_media', fileInput[0].files[0]);
            }

            var progressIndicatorElement = jQuery.progressIndicator({
                'message': app.vtranslate('Sending WhatsApp Message...'),
                'blockInfo': { 'enabled': true }
            });

            var params = {
                'url': 'index.php',
                'data': formData,
                'processData': false,
                'contentType': false
            };

            app.request.post(params).then(function (err, response) {
                progressIndicatorElement.progressIndicator({ 'mode': 'hide' });
                if (!err && response) {
                    app.helper.hideModal();
                    app.helper.showSuccessNotification({ message: app.vtranslate('Message(s) processed. Check logs for details.') });
                } else {
                    app.helper.showErrorNotification({ message: err || app.vtranslate('Error sending message') });
                }
            });
        });
    }
}

jQuery(document).ready(function () {
    var instance = new Whatsapp_Js();
    instance.registerEvents();
});
