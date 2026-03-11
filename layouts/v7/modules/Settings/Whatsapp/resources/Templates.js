Settings_Vtiger_List_Js("Settings_Whatsapp_Templates_Js", {}, {

    // Trigger sync templates from Meta
    syncTemplates: function (url) {
        var self = this;
        alert('Syncing templates from: ' + url);
        app.helper.showProgress();
        AppConnector.request(url).then(
            function (data) {
                app.helper.hideProgress();
                if (data.success) {
                    app.helper.showSuccessNotification({ 'message': app.vtranslate('JS_TEMPLATES_SYNCED_SUCCESSFULLY', 'Settings:Whatsapp') });
                    window.location.reload();
                } else {
                    app.helper.showErrorNotification({ 'message': data.error.message });
                }
            },
            function (error) {
                app.helper.hideProgress();
            }
        );
    },

    // Register filter change event
    registerChannelFilterEvent: function () {
        var self = this;
        jQuery('#channelFilter').on('change', function (e) {
            var channelId = jQuery(e.currentTarget).val();
            window.location.href = 'index.php?module=Whatsapp&parent=Settings&view=Templates&channel_id=' + channelId;
        });
    },

    // Register sync button on templates page
    registerSyncTemplatesEvent: function () {
        var self = this;
        jQuery('#syncTemplates, #syncTemplatesCenter').on('click', function (e) {
            var channelId = jQuery(e.currentTarget).data('channel-id');
            var url = 'module=Whatsapp&parent=Settings&action=ActionAjax&mode=syncTemplates&record=' + channelId;
            self.syncTemplates(url);
        });
    },

    registerTemplateMappingEvent: function () {
        var self = this;
        jQuery('.templateMapping').on('click', function (e) {
            var element = jQuery(e.currentTarget);
            var templateId = element.data('id');
            var url = 'index.php?module=Whatsapp&parent=Settings&view=MappingModal&template_id=' + templateId;

            app.helper.showProgress();
            app.request.get({ 'url': url }).then(function (error, data) {
                app.helper.hideProgress();
                if (data) {
                    app.helper.showModal(data, {
                        'cb': function (modalContainer) {
                            self.registerMappingModalEvents(modalContainer);
                        }
                    });
                }
            });
        });
    },

    registerMappingModalEvents: function (modalContainer) {
        var self = this;

        vtUtils.showSelect2ElementView(modalContainer.find('select.select2'));

        modalContainer.find('#crm_module').on('change', function (e) {
            var moduleName = jQuery(e.currentTarget).val();
            if (!moduleName) {
                var selects = modalContainer.find('.crm-field-select');
                selects.select2('destroy');
                selects.html('<option value="">Select Field</option>');
                vtUtils.showSelect2ElementView(selects);
                return;
            }

            app.helper.showProgress();
            var actionUrl = 'index.php?module=Whatsapp&parent=Settings&action=ActionAjax&mode=getModuleFields&crm_module=' + moduleName;
            app.request.get({ 'url': actionUrl }).then(function (error, data) {
                app.helper.hideProgress();
                if (data) {
                    var options = '<option value="">Select Field</option>';
                    jQuery.each(data, function (fieldName, fieldLabel) {
                        options += '<option value="' + fieldName + '">' + fieldLabel + '</option>';
                    });
                    var selects = modalContainer.find('.crm-field-select');
                    console.log(selects, 'selects');
                    selects.each(function () {
                        var currentSelect = jQuery(this);
                        if (currentSelect.data('select2')) {
                            currentSelect.select2('destroy');
                        }
                        currentSelect.html(options);

                    });
                    vtUtils.showSelect2ElementView(modalContainer.find('select.crm-field-select'));
                }
            });
        });

        modalContainer.find('#saveMappingBtn').on('click', function (e) {
            var form = modalContainer.find('#mappingForm');
            var formData = form.serializeFormData();

            app.helper.showProgress();
            var saveParams = {
                'module': 'Whatsapp',
                'parent': 'Settings',
                'action': 'ActionAjax',
                'mode': 'saveMapping'
            };
            jQuery.extend(saveParams, formData);

            app.request.post({ 'data': saveParams }).then(function (error, data) {
                app.helper.hideProgress();
                if (data) {
                    app.helper.hideModal();
                    app.helper.showSuccessNotification({ 'message': 'Mapping Saved Successfully' });
                    window.location.reload();
                } else {
                    app.helper.showErrorNotification({ 'message': error || 'Error saving mapping' });
                }
            });
        });
    },

    registerEvents: function () {
        console.log('WhatsApp Templates JS Events Registered');
        this._super();
        this.registerChannelFilterEvent();
        this.registerSyncTemplatesEvent();
        this.registerTemplateMappingEvent();
    }
});
